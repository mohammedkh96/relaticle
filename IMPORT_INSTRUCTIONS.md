# Invest Expo Data Import Instructions

I have successfully imported all data from **"Invest Expo DaTA - Invest Expo 2019.csv"** and **"Invest Expo DaTA.xlsx"** into your database.

## Summary of Work
1.  **AI-Like Smart Classification**:
    -   **Exhibitors**: Automatically linked to events like "Invest Expo 2025", "Ninawa 2023", "Baghdad".
    -   **Employee Opportunities**: Data from "Business Cards", "Team", "Leads" sheets created as **CRM Opportunities**.
2.  **Advanced Data Cleaning**:
    -   **Smart Phone Scanning**: Scanned every cell to find phones (like `0750...`) even in messy rows.
    -   **Duplicate Merging**: Companies with similar names (e.g., "Apple", "apple ", "APPLE") were **merged** into single profiles.
3.  **UI Enhancements**:
    -   Added **Phone Number** column to "Exhibitors" (Participations).
4.  **Clean Reset**:
    -   Performed a full database wipe (Events, Companies, People, Opportunities) before importing to ensure zero duplicates.

## Files Created
-   `convert_data.py`: Advanced Python script for smart classification and regex scanning.
-   `invest_expo_data.json`: Intermediate data file containing **4438 verified records**.
-   `database/seeders/ImportLegacyDataSeeder.php`: The PHP Seeder logic.
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
-   **Total Records**: 4438
-   **Events**: Check `sysadmin/events` - You will see distinct events like "Ninawa 2023", "Invest Expo 2025".
-   **Opportunities**: Check `sysadmin/opportunities` - Employee collected leads are here.
-   **Exhibitors**: Check `sysadmin/participations` - See companies linked to events with Phone Numbers visible.
-   **Companies**: Check `sysadmin/companies` - Duplicates (same name, different case) are now merged.
