import pandas as pd
import sys

pd.set_option('display.max_rows', None)
pd.set_option('display.max_columns', None)

output_file = r'c:\laragon\www\invest_expo_crm\scan_output_utf8.txt'

try:
    with open(output_file, 'w', encoding='utf-8') as f:
        # Redirect stdout
        sys.stdout = f
        
        file_path = r'c:\laragon\www\invest_expo_crm\Invest Expo DaTA.xlsx'
        xls = pd.ExcelFile(file_path)
        print(f"All Sheet Names: {xls.sheet_names}")
        
        for sheet_name in xls.sheet_names:
            print(f"\n--- Analyzing Sheet: {sheet_name} ---")
            try:
                df = pd.read_excel(file_path, sheet_name=sheet_name, header=None, nrows=20)
                
                # Check for "Company"
                found_header = False
                for i, row in df.iterrows():
                    row_str = row.astype(str).str.lower().values
                    if any('company' in s for s in row_str):
                        print(f"Header found at row {i}: {list(row.values)}")
                        found_header = True
                        break
                
                if not found_header:
                    print("WARNING: Could not find 'Company' in first 20 rows.")
                    print("First 5 rows for inspection:")
                    print(df.head(5).to_string())
            except Exception as e:
                print(f"Error processing sheet {sheet_name}: {e}")

    print(f"Scan complete. Output saved to {output_file}")

except Exception as e:
    print(f"Global Error: {e}")
