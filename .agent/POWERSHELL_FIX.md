# Fix PowerShell Execution Policy

## Problem
Cannot run `npm run dev` or `npm run build` due to PowerShell execution policy.

**Error Message:**
```
npm : File C:\Program Files\nodejs\npm.ps1 cannot be loaded because running scripts is disabled on this system.
```

---

## Solution 1: Enable PowerShell Scripts (Recommended)

### **Step 1: Open PowerShell as Administrator**
1. Press `Windows + X`
2. Select "Windows PowerShell (Admin)" or "Terminal (Admin)"

### **Step 2: Run This Command**
```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

### **Step 3: Confirm**
- Type `Y` and press Enter when prompted

### **Step 4: Verify**
```powershell
Get-ExecutionPolicy
```
Should show: `RemoteSigned`

### **Step 5: Test npm**
```bash
cd C:\laragon\www\invest_expo_crm
npm run dev
```

---

## Solution 2: Use CMD Instead

If you can't change PowerShell policy, use Command Prompt instead:

### **Step 1: Open CMD**
1. Press `Windows + R`
2. Type `cmd`
3. Press Enter

### **Step 2: Navigate to Project**
```cmd
cd C:\laragon\www\invest_expo_crm
```

### **Step 3: Run npm**
```cmd
npm run dev
```

---

## Solution 3: Build Assets Once

If you just need assets compiled (not live reload):

### **In CMD or PowerShell:**
```bash
cd C:\laragon\www\invest_expo_crm
npm run build
```

This creates production-ready assets in `public/build/`.

---

## What Each Policy Means

| Policy | Description |
|--------|-------------|
| **Restricted** | No scripts allowed (default) |
| **RemoteSigned** | Local scripts OK, downloaded scripts must be signed |
| **Unrestricted** | All scripts allowed (not recommended) |

---

## Verification

After fixing, verify everything works:

```bash
# 1. Check policy
Get-ExecutionPolicy

# 2. Test npm
npm --version

# 3. Run dev server
npm run dev

# 4. Or build assets
npm run build
```

---

## For Production

In production, you should:
1. Build assets: `npm run build`
2. Don't run `npm run dev`
3. Serve from `public/build/`

---

## Still Having Issues?

### **Alternative: Use Git Bash**
1. Install Git for Windows
2. Use Git Bash terminal
3. Run `npm run dev` in Git Bash

### **Alternative: Use WSL**
1. Install Windows Subsystem for Linux
2. Run commands in WSL terminal

---

## Quick Reference

```powershell
# Check current policy
Get-ExecutionPolicy

# Set policy (as Admin)
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser

# Revert policy (if needed)
Set-ExecutionPolicy -ExecutionPolicy Restricted -Scope CurrentUser
```

---

**After fixing, you can run:**
- `npm run dev` - Development server with hot reload
- `npm run build` - Production build
