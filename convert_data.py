import pandas as pd
import json
import os
import re

def normalize_text(text):
    if pd.isna(text):
        return None
    text = str(text).strip()
    return text if text and text.lower() != 'nan' else None

def clean_phone(text):
    if not text: return None
    # Remove obvious non-phone text like "FALSE"
    if str(text).upper() == 'FALSE': return None
    return str(text).strip()

def find_header_row(df, possible_headers):
    # Search first 20 rows
    for i in range(min(20, len(df))):
        row_values = [str(x).lower().strip() for x in df.iloc[i].values]
        
        # Stricter check: matching one of the headers exactly or very closely?
        # The previous 'in' check was too loose (matched "Karmand Company")
        
        match_count = 0
        for val in row_values:
            # Check if this cell value IS one of our target headers (or very close)
            # allowed: "company name", "company", "co.n.", "organizer name"
            if val in possible_headers:
                match_count += 1
            # Special case for "Company" which might be "Company Name" or just "Company"
            elif 'company' == val or 'company name' == val:
                 match_count += 1
        
        # If we found at least one STRONG match, use this row
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
            val = df.iloc[row_idx, col_map[name]]
            return normalize_text(val)
    return None

def process_dataframe(df, source_name):
    # Define possible headers for different sheet types
    # Stricter list for header detection
    target_headers = ['company name', 'co.n.', 'organizer name', 'company', 'company name ', 'co.n']
    
    header_idx = find_header_row(df, target_headers)
    
    if header_idx is None:
        print(f"Warning: Could not find header row in {source_name}. Skipping...")
        return []

    col_map = get_column_map(df, header_idx)
    records = []
    
    # Check if this sheet is likely an Opportunity (Business Cards)
    is_opportunity = False
    opportunity_keywords = ['business card', 'card', 'opportunity', 'lead', 'contacted?']
    if any(k in source_name.lower() for k in opportunity_keywords):
        is_opportunity = True
    
    # Iterate data rows
    for i in range(header_idx + 1, len(df)):
        # Extract fields using strict precedence
        company_name = extract_value(df, i, col_map, ['company name', 'co.n.', 'organizer name', 'company', 'karmand company', 'company name '])
        
        if not company_name:
            continue
            
        # Expanded phone list
        phone_cols = ['tel', 'company tel', 'phone', 'phone number', 'phone number ', 'phone no', 'phone / whatsapp', 'phone number(s)', 'mobile', 'contact no']
        
        record = {
            'source': source_name,
            'company_name': company_name,
            'person_name': extract_value(df, i, col_map, ['person name', 'person in charge', 'contact person', 'mr.osama', 'name', 'person name ']),
            'company_tel': clean_phone(extract_value(df, i, col_map, phone_cols)), 
            'person_phone': clean_phone(extract_value(df, i, col_map, phone_cols)), # Reuse strict phone list
            'email': extract_value(df, i, col_map, ['email', 'email address(es)', 'email ']),
            'note': extract_value(df, i, col_map, ['note', 'notes', 'note ']),
            'address': extract_value(df, i, col_map, ['address', 'location', 'country', 'website & adress']), 
            'website': extract_value(df, i, col_map, ['website']),
            'is_opportunity': is_opportunity
        }
        
        # Merge Website into Address if Address is empty, or Note
        if record['website'] and not record['address']:
             record['address'] = record['website']
        elif record['website']:
             record['note'] = (record['note'] or '') + f"\nWebsite: {record['website']}"

        # Logic to ensure we get a phone number for the company if possible
        # If company_tel is empty but person_phone exists, use person_phone as company_tel fallback?
        # User said "should phone show in company section". 
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
