# Invest Expo Data Import Instructions

I have successfully imported all data from **"Invest Expo DaTA - Invest Expo 2019.csv"** and **"Invest Expo DaTA.xlsx"** into your database.

## Summary of Work
1.  **Data Extraction**: I updated `convert_data.py` to handle **all 30+ sheets**, using strict header detection to avoid errors and expanded column mapping for phone numbers.
2.  **Database Seeding**: `ReimportDataSeeder.php` performs a **full reset** (truncating Events, Companies, People, etc.) and re-imports distinct data.
3.  **Schema Compliance**: No database schema changes were made.
4.  **Improvements (Final Run)**:
    -   **Company Phones**: Added support for columns like "Mobile", "Contact No", "Phone / WhatsApp". Fixed issue with "FALSE" valus.
    -   **Opportunities**: "Business Card" sheets are automatically imported as Opportunities.
    -   **Accuracy**: Stricter header detection logic ensures data is aligned correctly.

## Files Created
-   `convert_data.py`: Python script to read Excel/CSV and generate `invest_expo_data.json`.
-   `invest_expo_data.json`: Intermediate data file containing **4424 verified records**.
-   `database/seeders/ImportLegacyDataSeeder.php`: Main PHP Seeder logic.
-   `database/seeders/ReimportDataSeeder.php`: Helper seeder that **TRUNCATES ALL DATA** and runs the import.

## How to Run Again
If you update the Excel or CSV files, follow these steps to re-import:

1.  **Update Data**: Place the new files in the root folder.
2.  **Run Python Script** (to refresh the JSON):
    ```powershell
    python convert_data.py
    ```
3.  **Run Full Reset & Import**:
    > ⚠️ **passed**: This commands DELETES all events, companies, opportunities, and people before importing.
    ```powershell
    php artisan db:seed --class=ReimportDataSeeder
    ```

## Verification
-   **Total Records**: 4424
-   **Events**: Check `sysadmin/events` (e.g., "Invest Expo 2025").
-   **Exhibitors**: Check `sysadmin/participations`.
-   **Opportunities**: Check `sysadmin/opportunities`.
-   **Phones**: Check Company details; phone numbers should now be visible.
