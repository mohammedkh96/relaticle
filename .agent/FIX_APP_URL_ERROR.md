# Fix for "Invalid URI: Scheme is malformed" Error

## Problem
When running `php artisan serve` or any artisan command, you get:
```
Invalid URI: Scheme is malformed.
```

## Root Cause
Your `.env` file has a malformed `APP_URL` value. It's likely:
- Missing the `http://` or `https://` prefix
- Contains special characters
- Has extra spaces or quotes

## Solution

### Step 1: Open your `.env` file
Edit `c:\laragon\www\invest_expo_crm\.env`

### Step 2: Find the APP_URL line and fix it
Change from something like:
```
APP_URL=invest-expo-crm.test
```
or
```
APP_URL="http://localhost"
```

To (choose one):
```
APP_URL=http://invest-expo-crm.test
```
or
```
APP_URL=http://localhost
```

**Important**: No quotes, no trailing slashes, must start with `http://` or `https://`

### Step 3: Clear config cache and try again
```powershell
php artisan config:clear
php artisan serve
```

## Verification
If successful, you should see:
```
INFO  Server running on [http://127.0.0.1:8000].
Press Ctrl+C to stop the server
```
