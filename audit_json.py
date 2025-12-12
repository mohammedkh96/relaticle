
import json

with open(r'c:\laragon\www\invest_expo_crm\invest_expo_data.json', 'r', encoding='utf-8') as f:
    data = json.load(f)
    print(f"Total Records in JSON: {len(data)}")
    
    # Count by category
    exhibitors = len([r for r in data if r.get('is_exhibitor')])
    employees = len([r for r in data if r.get('is_employee_entry')])
    nor = len([r for r in data if not r.get('is_exhibitor') and not r.get('is_employee_entry')])
    
    print(f"Tagged as Exhibitor: {exhibitors}")
    print(f"Tagged as Employee Entry: {employees}")
    print(f"Untagged (Dropped?): {nor}")
