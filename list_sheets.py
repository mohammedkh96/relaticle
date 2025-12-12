
import pandas as pd
import os

excel_path = r'c:\laragon\www\invest_expo_crm\Invest Expo DaTA.xlsx'
if os.path.exists(excel_path):
    print("Listing sheets...")
    try:
        xls = pd.ExcelFile(excel_path)
        for sheet in xls.sheet_names:
            df = pd.read_excel(excel_path, sheet_name=sheet, header=None)
            print(f"Sheet: '{sheet}' - Rows: {len(df)}")
            # peek at first few rows to see likely headers
            if len(df) > 0:
                print(f"  Head (row 0): {df.iloc[0].values[:3]}")
            if len(df) > 1:
                print(f"  Head (row 1): {df.iloc[1].values[:3]}")
            print("-" * 20)
    except Exception as e:
        print(f"Error reading Excel: {e}")
