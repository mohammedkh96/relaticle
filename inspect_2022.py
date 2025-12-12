
import pandas as pd
import os

excel_path = r'c:\laragon\www\invest_expo_crm\Invest Expo DaTA.xlsx'
sheet_name = 'Invest Expo 2022'

if os.path.exists(excel_path):
    try:
        df = pd.read_excel(excel_path, sheet_name=sheet_name, header=None)
        print(f"Sheet: '{sheet_name}' - Total Rows: {len(df)}")
        print("First 5 rows:")
        print(df.head(5))
    except Exception as e:
        print(f"Error: {e}")
