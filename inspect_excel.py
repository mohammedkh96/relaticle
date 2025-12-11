import pandas as pd
import sys

try:
    file_path = r'c:\laragon\www\invest_expo_crm\Invest Expo DaTA.xlsx'
    # Read the Excel file. Reading all sheets to see what's inside.
    xls = pd.ExcelFile(file_path)
    print(f"Sheet names: {xls.sheet_names}")
    
    for sheet_name in xls.sheet_names:
        print(f"\n--- Sheet: {sheet_name} ---")
        df = pd.read_excel(file_path, sheet_name=sheet_name, nrows=5)
        print(df.to_string())
        print("\nColumns:", list(df.columns))

except Exception as e:
    print(f"Error: {e}")
