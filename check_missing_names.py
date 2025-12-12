
import json

with open(r'c:\laragon\www\invest_expo_crm\invest_expo_data.json', 'r', encoding='utf-8') as f:
    data = json.load(f)
    
    missing_name = 0
    for r in data:
        if not r.get('company_name'):
            missing_name += 1
            
    print(f"Total Rows: {len(data)}")
    print(f"Rows with NO Company Name: {missing_name}")
