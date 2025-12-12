
import pandas as pd
import os

excel_path = r'c:\laragon\www\invest_expo_crm\Invest Expo DaTA.xlsx'
if os.path.exists(excel_path):
    try:
        xls = pd.ExcelFile(excel_path)
        print("Sheet Names Found:")
        for name in xls.sheet_names:
            print(f"- {name}")
    except Exception as e:
        print(f"Error: {e}")
