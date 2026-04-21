# Deployment Guide — NBSC GCO System

## Running on Localhost (XAMPP)

1. Copy project to `C:\xampp\htdocs\inventory_system\`
2. Start Apache + MySQL in XAMPP Control Panel
3. Open **phpMyAdmin** → create database `inventory_db`
4. Import `database/schema.sql`
5. Import `database/seed_admins.sql` (inserts the 3 admin accounts)
6. Open `http://localhost/inventory_system/`

No extra config needed — `config/config.php` auto-detects localhost.

---

## Deploying to InfinityFree

### 1. Create hosting account
- Sign up at [infinityfree.com](https://infinityfree.com)
- Create a website (you get a free subdomain like `yoursite.infinityfreeapp.com`)

### 2. Create the database
- Go to **iFastNet Panel → MySQL Databases**
- Create a new database — note down:
  - **DB Host** (e.g. `sql123.infinityfree.com`)
  - **DB Name** (e.g. `if0_12345678_inventory_db`)
  - **DB User** (e.g. `if0_12345678`)
  - **DB Password**

### 3. Create `config/env.php`
Copy `config/env.example.php` → `config/env.php` and fill in your credentials:

```php
<?php
define('DB_HOST',    'sql123.infinityfree.com');
define('DB_NAME',    'if0_12345678_inventory_db');
define('DB_USER',    'if0_12345678');
define('DB_PASS',    'your_password');
define('DB_CHARSET', 'utf8mb4');
define('APP_URL',    'https://yoursite.infinityfreeapp.com');
```

### 4. Upload files
- Use **FileZilla** (FTP) or the **iFastNet File Manager**
- Upload everything to the `htdocs/` folder on InfinityFree
- **Do NOT upload** `.env` — use `config/env.php` instead

### 5. Import the database
- Go to **iFastNet Panel → phpMyAdmin**
- Select your database
- Import `database/schema.sql`
- Import `database/seed_admins.sql`

### 6. Done
Visit `https://yoursite.infinityfreeapp.com` — it should work.

---

## Admin Accounts (default password: `admin123`)

| Email | Name |
|-------|------|
| lorbelleganzan@gmail.com | Lorbelle Ganzan |
| gco@nbsc.edu.ph | GCO Admin |
| jacorpuz@nbsc.edu.ph | Jo Augustine Corpuz |

> Change passwords after first login via **Admin Panel → Profile → Change Password**

---

## Troubleshooting

| Problem | Fix |
|---------|-----|
| Blank page / 500 error | Check `config/env.php` DB credentials |
| Can't connect to DB | Verify DB host from InfinityFree panel (not `localhost`) |
| Images not showing | Make sure `uploads/` folder exists and is writable (chmod 755) |
| Session issues | InfinityFree uses HTTPS — sessions auto-detect this |
| `.env` blocked (403) | Normal — use `config/env.php` instead |
