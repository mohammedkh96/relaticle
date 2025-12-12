import pandas as pd
import json
import os
import re

def normalize_text(text):
    if pd.isna(text):
        return None
    text = str(text).strip()
    return text if text and text.lower() != 'nan' else None

def normalize_company_name(text):
    val = normalize_text(text)
    if not val: return None
    # Title cased: "invest expo" -> "Invest Expo"
    return val.title()

def clean_phone(text):
    if not text: return None
    # Remove obvious non-phone text like "FALSE"
    if str(text).upper() == 'FALSE': return None
    return str(text).strip()

def find_phone_in_row(row_values):
    # Regex for Iraqi/Kurdish phones: 07xxxxxxxxx (11 digits) or +964...
    # Flexible pattern: look for 10-14 digits, maybe with spaces
    # We clean the text first to remove spaces/dashes
    for val in row_values:
        if not val: continue
        val_str = str(val)
        # Remove spaces, dashes
        clean_val = re.sub(r'[\s\-\.]', '', val_str)
        
        # Matches 0750xxxxxxx or 0770xxxxxxx (11 digits)
        # Or 00964...
        if re.search(r'(?:^|[^\d])(07\d{9})(?:$|[^\d])', clean_val):
            return clean_val
        if re.search(r'(?:^|[^\d])(\+?964\d{9,10})(?:$|[^\d])', clean_val):
            return clean_val
        if re.search(r'(?:^|[^\d])(00964\d{9,10})(?:$|[^\d])', clean_val):
            return clean_val
    return None

def find_header_row(df, possible_headers):
    # Search first 20 rows
    for i in range(min(20, len(df))):
        row_values = [str(x).lower().strip() for x in df.iloc[i].values]
        
        match_count = 0
        for val in row_values:
            if val in possible_headers:
                match_count += 1
            elif 'company' == val or 'company name' == val:
                 match_count += 1
        
        if match_count > 0:
            return i
            
    return None

def get_column_map(df, header_row_index):
    # Map normalized column names to actual indices
    col_map = {}
    if header_row_index is None:
        return {}
    
    headers = [str(x).strip().lower() for x in df.iloc[header_row_index].values]
    for idx, header in enumerate(headers):
        col_map[header] = idx
    return col_map

def extract_value(df, row_idx, col_map, possible_names):
    # Try to find a value using a list of possible column names
    for name in possible_names:
        name = name.lower()
        if name in col_map:
            col_idx = col_map[name]
            # Safety check: ensure col_idx is within df bounds
            if col_idx < df.shape[1]:
                val = df.iloc[row_idx, col_idx]
                return normalize_text(val)
    return None

def process_dataframe(df, source_name):
    source_lower = source_name.lower()
    
    # 1. Classify Sheet
    exhibitor_keywords = [
        'invest expo', 'ninawa', 'baghdad', 'fair', 'show', 'exhibition', 'event', 
        'eco build', 'city scape'
    ]
    
    # Explicit list of known exhibitor sheets to be safe
    known_exhibitor_sheets = [
        'Invest Expo 2019', 'Invest Expo 2021', 'Invest Expo 2022', 'Invest Expo 2023', 'Invest Expo 2025',
        'Ninawa 2023', 'Luxury Event Baghdad2025', 'ECO Build IQ', 'Cityscape2025-Dahat', 'CITY SCAPE 2024- Maria',
        'IPS International Properrty Sho', 'IPS-2025-Dahat', 'AqarFair-Dahat', 'Real Estate Future Forum'
    ]

    # Explicit list of known Opportunity/Category sheets
    known_opportunity_sheets = [
        'Engineering Co.', 'Construction', 'Real Estate Co.', 'Solar System Companies', 
        'Insurance Companies- Maria', 'Projects', 'Business Cards', 'Bussines card - Mr Dilon',
        'my data - Maria', 'INVEST2025-Business Cards-Dahat', 'Int.data Dahat&Hilda',
        'Cyprus & Jordan int. data-Dahat', 'Int.Event organizers-Dahat', 'Jordanian Co-Dahat',
        'Qatar-Dahat', 'Noor', 'IRIES'
    ]
    
    is_exhibitor = False
    is_employee_entry = False
    
    # Check exact/known matches first
    if any(s.lower() == source_lower for s in [k.lower() for k in known_exhibitor_sheets]):
        is_exhibitor = True
    elif any(s.lower() == source_lower for s in [k.lower() for k in known_opportunity_sheets]):
        is_employee_entry = True
    # Fallback to keywords
    elif any(k in source_lower for k in exhibitor_keywords):
        is_exhibitor = True
    else:
        # Default fallback: if year present -> Exhibitor, else Employee
        if re.search(r'20\d{2}', source_name) and 'card' not in source_lower:
            is_exhibitor = True
        else:
            is_employee_entry = True

    # 2. Header Detection
    target_headers = ['company name', 'co.n.', 'organizer name', 'company', 'company name ', 'co.n', 'karmand company']
    
    header_idx = find_header_row(df, target_headers)
    
    # FALLBACK: If header not found but we KNOW this sheet is important
    if header_idx is None:
        if is_exhibitor or is_employee_entry:
            # Assume Row 0 is header or data starts at 1
            # Or just blindly try to map columns 0, 1, 2...
            # Let's try row 0 as header for now
            header_idx = 0
            # print(f"  > Forced header search at row 0 for {source_name}")
        else:
            print(f"Warning: Could not find header row in {source_name}. Skipping...")
            return []

    col_map = get_column_map(df, header_idx)
    
    # If col_map is empty or missing key columns, try a positional map fallback
    if 'invest expo 2022' in source_lower:
         # Special handling for the tricky 2022 sheet
         # Based on inspection: Col 0=Index, Col 1=False??, Col 2=Company, Col 3=Person
         col_map = {
             'company name': 2, 'company': 2,
             'person name': 3, 'name': 3, 
             # We rely on smart phone scan
         }
         # Force start row if header detection failed 
         if header_idx is None or header_idx == 0:
             header_idx = 1
             
    elif not any(k in col_map for k in ['company name', 'company', 'name']):
         # print(f"  > Header map weak for {source_name}, using positional fallback")
         # Assume Col 0 = Company, Col 1 = Person, Col 2 or 3 = Phone
         col_map = {
             'company name': 0, 'company': 0,
             'person name': 1, 'name': 1,
             'tel': 2, 'phone': 3, 'mobile': 4,
             'email': 5, 'address': 6, 'note': 7
         }

    records = []
    
    # Iterate data rows
    for i in range(header_idx + 1, len(df)):
        row_values = df.iloc[i].values
        
        # Extract fields using strict precedence
        raw_company = extract_value(df, i, col_map, ['company name', 'co.n.', 'organizer name', 'company', 'karmand company', 'company name '])
        company_name = normalize_company_name(raw_company)
        
        if not company_name:
            continue
            
        # Expanded phone list
        phone_cols = ['tel', 'company tel', 'phone', 'phone number', 'phone number ', 'phone no', 'phone / whatsapp', 'phone number(s)', 'mobile', 'contact no']
        
        # Try specific columns first
        company_tel = clean_phone(extract_value(df, i, col_map, phone_cols))
        person_phone = clean_phone(extract_value(df, i, col_map, phone_cols)) 
        
        # Aggressive Scan
        smart_phone = find_phone_in_row(row_values)
        
        if not company_tel and smart_phone:
            company_tel = smart_phone
        if not person_phone and smart_phone and company_tel != smart_phone:
            person_phone = smart_phone
            
        record = {
            'source': source_name,
            'company_name': company_name,
            'person_name': extract_value(df, i, col_map, ['person name', 'person in charge', 'contact person', 'mr.osama', 'name', 'person name ']),
            'company_tel': company_tel, 
            'person_phone': person_phone,
            'email': extract_value(df, i, col_map, ['email', 'email address(es)', 'email ']),
            'note': extract_value(df, i, col_map, ['note', 'notes', 'note ']),
            'address': extract_value(df, i, col_map, ['address', 'location', 'country', 'website & adress']), 
            'website': extract_value(df, i, col_map, ['website']),
            'is_exhibitor': is_exhibitor,
            'is_employee_entry': is_employee_entry
        }
        
        # Merge Website into Address if Address is empty, or Note
        if record['website'] and not record['address']:
             record['address'] = record['website']
        elif record['website']:
             record['note'] = (record['note'] or '') + f"\nWebsite: {record['website']}"

        # Logic to ensure we get a phone number for the company if possible
        if not record['company_tel'] and record['person_phone']:
            record['company_tel'] = record['person_phone']

        records.append(record)
        
    return records

all_records = []

# Process CSV
csv_path = r'c:\laragon\www\invest_expo_crm\Invest Expo DaTA - Invest Expo 2019.csv'
if os.path.exists(csv_path):
    print("Processing CSV...")
    try:
        df = pd.read_csv(csv_path, header=None)
        records = process_dataframe(df, "CSV: 2019")
        all_records.extend(records)
        print(f"  > Added {len(records)} records from CSV")
    except Exception as e:
        print(f"Error reading CSV: {e}")

# Process Excel
excel_path = r'c:\laragon\www\invest_expo_crm\Invest Expo DaTA.xlsx'
if os.path.exists(excel_path):
    print("Processing Excel...")
    try:
        xls = pd.ExcelFile(excel_path)
        for sheet in xls.sheet_names:
            # print(f"Processing sheet: {sheet}")
            try:
                df = pd.read_excel(excel_path, sheet_name=sheet, header=None)
                records = process_dataframe(df, f"Excel: {sheet}")
                all_records.extend(records)
                print(f"  > Added {len(records)} records from sheet '{sheet}'")
            except Exception as e:
                print(f"  > Error processing sheet '{sheet}': {e}")
    except Exception as e:
        print(f"Error reading Excel: {e}")

# Save to JSON
output_path = r'c:\laragon\www\invest_expo_crm\invest_expo_data.json'
with open(output_path, 'w', encoding='utf-8') as f:
    json.dump(all_records, f, indent=4, ensure_ascii=False)

print(f"\nTotal records processed: {len(all_records)}")
print(f"Saved to: {output_path}")
