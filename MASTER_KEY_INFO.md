# Master Key Information

## What is the Master Key?

The Master Key is the ONLY way to access the admin portal. This simplified approach ensures:

- Single point of access control
- No need to manage multiple admin accounts
- Easier to secure and maintain
- Quick access for authorized personnel

## Default Master Key

**Master Key:** `GuidanceAdmin2024!`

## CRITICAL SECURITY NOTICE

⚠️ **YOU MUST CHANGE THE MASTER KEY IMMEDIATELY IN PRODUCTION!**

## How to Change the Master Key

1. Open the file: `config/master_key.php`

2. Locate this line:
```php
define('MASTER_KEY', 'GuidanceAdmin2024!');
```

3. Change to your own secure master key:
```php
define('MASTER_KEY', 'YourVeryStrongPassword123!@#');
```

4. Save the file

## Master Key Best Practices

1. **Use a Strong Password**
   - Minimum 16 characters
   - Mix of uppercase, lowercase, numbers, and symbols
   - Avoid common words or patterns
   - Example: `Gd@nc3$yst3m#2024!Secure`

2. **Keep it Secret**
   - Never share the master key publicly
   - Don't store it in version control
   - Keep it in a secure password manager
   - Only share with authorized personnel

3. **Change Regularly**
   - Update the master key every 90 days
   - Change immediately if compromised
   - Document the change date

4. **Limit Knowledge**
   - Only authorized staff should know the master key
   - Keep a secure backup in case of emergency
   - Document who has access

## How to Login as Admin

1. Go to the home page
2. Click "Admin Portal"
3. Enter the master key
4. Click "Sign In"

That's it! No username needed.

## Security Recommendations

- Change the master key immediately after installation
- Store the master key in a secure location (password manager, encrypted file, safe)
- Never write it down in plain text
- Never send it via email or unsecured messaging
- Consider using a password manager to generate a strong key
- Audit who has access to the master key regularly

## Lost Master Key?

If you lose the master key:

1. Access the server file system directly
2. Open `config/master_key.php`
3. Set a new master key
4. Save the file
5. Use the new master key to login

## Advantages of Master Key Only

- **Simplicity**: One key to remember
- **Security**: No user accounts to manage or hack
- **Speed**: Quick access for authorized personnel
- **Maintenance**: No password resets or account management
- **Control**: Single point of access control
