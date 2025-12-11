import pandas as pd
import json
import os
import re

def normalize_text(text):
    if pd.isna(text):
        return None
    return str(text).strip()

def find_header_row(df):
    for i in range(min(20, len(df))):
        row_values = [str(x).lower().strip() for x in df.iloc[i].values]
        if 'company name' in row_values:
            return i
    return None

def process_dataframe(df, source_name):
    header_idx = find_header_row(df)
    if header_idx is None:
        print(f"Warning: Could not find header row in {source_name}")
        return []

    # Reload with correct header
    df.columns = df.iloc[header_idx]
    df = df.iloc[header_idx+1:].reset_index(drop=True)
    
    # Normalize column names
    df.columns = [str(c).strip().lower() for c in df.columns]
    
    records = []
    
    # Map columns
    # Expected: tel, company name, person name, phone number, email, note, address
    
    for _, row in df.iterrows():
        company_name = normalize_text(row.get('company name'))
        
        # Skip if no company name
        if not company_name:
            continue
            
        record = {
            'source': source_name,
            'company_name': company_name,
            'person_name': normalize_text(row.get('person name')),
            'company_tel': normalize_text(row.get('tel')),
            'person_phone': normalize_text(row.get('phone number')),
            'email': normalize_text(row.get('email')),
            'note': normalize_text(row.get('note')),
            'address': normalize_text(row.get('address'))
        }
        records.append(record)
        
    return records

all_records = []

# Process CSV
csv_path = r'c:\laragon\www\invest_expo_crm\Invest Expo DaTA - Invest Expo 2019.csv'
if os.path.exists(csv_path):
    print("Processing CSV...")
    try:
        df = pd.read_csv(csv_path, header=None) # Read without header first to find it
        records = process_dataframe(df, "CSV: 2019")
        all_records.extend(records)
        print(f"Added {len(records)} records from CSV")
    except Exception as e:
        print(f"Error reading CSV: {e}")

# Process Excel
excel_path = r'c:\laragon\www\invest_expo_crm\Invest Expo DaTA.xlsx'
if os.path.exists(excel_path):
    print("Processing Excel...")
    try:
        xls = pd.ExcelFile(excel_path)
        for sheet in xls.sheet_names:
            print(f"Processing sheet: {sheet}")
            df = pd.read_excel(excel_path, sheet_name=sheet, header=None)
            records = process_dataframe(df, f"Excel: {sheet}")
            all_records.extend(records)
            print(f"Added {len(records)} records from sheet {sheet}")
    except Exception as e:
        print(f"Error reading Excel: {e}")

# Save to JSON
output_path = r'c:\laragon\www\invest_expo_crm\invest_expo_data.json'
with open(output_path, 'w', encoding='utf-8') as f:
    json.dump(all_records, f, indent=4, ensure_ascii=False)

print(f"Total records saved: {len(all_records)}")
print(f"Saved to: {output_path}")
