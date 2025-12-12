
import pandas as pd
import os

excel_path = r'c:\laragon\www\invest_expo_crm\Invest Expo DaTA.xlsx'
sheet_name = 'Invest Expo 2022'

if os.path.exists(excel_path):
    try:
        df = pd.read_excel(excel_path, sheet_name=sheet_name, header=None)
        print(f"Sheet: '{sheet_name}'")
        row = df.iloc[2] # Row index 2 (the one with '1.0' at col 0)
        for idx, val in enumerate(row):
            print(f"Col {idx}: {val}")
    except Exception as e:
        print(f"Error: {e}")
