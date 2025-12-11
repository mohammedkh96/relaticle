# Invest Expo Data Import Instructions

I have successfully imported all data from **"Invest Expo DaTA - Invest Expo 2019.csv"** and **"Invest Expo DaTA.xlsx"** into your database.

## Summary of Work
1.  **Data Extraction**: I created a Python script (`convert_data.py`) to parse both the CSV and Excel files. This handled the messy formatting and combined everything into a clean JSON format.
2.  **Database Seeding**: I created a new Laravel Seeder `ImportLegacyDataSeeder.php` that reads the clean JSON and populates your database.
3.  **Schema Compliance**: As requested, I **did not change your database structure**.
    -   **Companies**: Mapped to the `companies` table (Name, Address, Phone).
    -   **People**: Mapped to the `people` table.
    -   **Contact Details**: Since the `people` table only has a `name` column, I stored the Person's **Phone** and **Email** in the **Notes** section attached to that person (or company).
4.  **Error Fixes**:
    -   Fixed `Class "Filament\Tables\Actions\ExportAction" not found` by switching to `Filament\Actions\ExportAction`.
    -   Added `ReimportDataSeeder` to clean old data before importing.

## Files Created
-   `convert_data.py`: Python script to read your Excel/CSV files and generate `invest_expo_data.json`.
-   `invest_expo_data.json`: Intermediate data file containing all 1178 records.
-   `database/seeders/ImportLegacyDataSeeder.php`: The PHP Seeder logic.
-   `database/seeders/ReimportDataSeeder.php`: Helper seeder to truncate tables and run import.

## How to Run Again
If you update the Excel or CSV files, follow these steps to re-import:

1.  **Update Data**: Place the new files in the root folder.
2.  **Run Python Script** (to refresh the JSON):
    ```powershell
    python convert_data.py
    ```
3.  **Run Clean Import** (Truncates tables and Imports):
    ```powershell
    php artisan db:seed --class=ReimportDataSeeder
    ```
    *Note: This will delete existing visitors, companies, and people before importing.*

## Verification
The import process processed **1178 records**. You can verify this by checking the `companies` and `people` tables in your Admin Panel.
