
import json

with open(r'c:\laragon\www\invest_expo_crm\invest_expo_data.json', 'r', encoding='utf-8') as f:
    data = json.load(f)
    
    unique_companies = set()
    for r in data:
        name = r.get('company_name')
        if name:
            unique_companies.add(name.lower().strip())
    
    print(f"Total Rows: {len(data)}")
    print(f"Unique Company Names: {len(unique_companies)}")
