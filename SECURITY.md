# Security Guidelines

## Implemented Security Measures

### 1. Authentication & Authorization
- Bcrypt password hashing with cost factor 12
- Session regeneration on login
- Session timeout (2 hours default)
- Secure session cookies (httponly, samesite)
- Authentication checks on all protected pages

### 2. Input Validation & Sanitization
- All user inputs are sanitized using `htmlspecialchars()`
- Email validation using `filter_var()`
- File upload validation (type, size, MIME)
- CSRF token validation on all forms
- Prepared statements for database queries

### 3. SQL Injection Prevention
- PDO with prepared statements
- Parameter binding for all queries
- No direct SQL string concatenation

### 4. File Upload Security
- File type validation (whitelist)
- MIME type verification
- File size limits (5MB default)
- Unique filename generation
- PHP execution disabled in uploads directory
- `.htaccess` protection

### 5. XSS Prevention
- Output escaping with `htmlspecialchars()`
- ENT_QUOTES flag for attribute protection
- Content Security Policy headers (recommended)

### 6. Session Security
- Secure session configuration
- Session ID regeneration
- Session timeout
- Session fixation prevention

## Production Deployment Checklist

### Before Going Live

- [ ] Change default admin password
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Enable HTTPS/SSL
- [ ] Set `session.cookie_secure=1` for HTTPS
- [ ] Remove or secure phpinfo() files
- [ ] Disable error display (`display_errors=0`)
- [ ] Enable error logging
- [ ] Set restrictive file permissions
- [ ] Configure firewall rules
- [ ] Set up regular backups
- [ ] Review database user permissions
- [ ] Update all dependencies
- [ ] Implement rate limiting
- [ ] Add security headers

### Recommended Security Headers

Add to `.htaccess` or web server config:

```apache
# Security Headers
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"
Header set Referrer-Policy "strict-origin-when-cross-origin"
Header set Permissions-Policy "geolocation=(), microphone=(), camera=()"
```

### File Permissions

```bash
# Directories
chmod 755 admin/ auth/ config/ includes/ process/
chmod 755 uploads/

# Files
chmod 644 *.php
chmod 644 .htaccess
chmod 600 .env

# Uploads
chmod 644 uploads/*
```

### Database Security

1. Use strong database passwords
2. Create dedicated database user with minimal privileges:
   ```sql
   CREATE USER 'inventory_user'@'localhost' IDENTIFIED BY 'strong_password';
   GRANT SELECT, INSERT, UPDATE, DELETE ON inventory_db.* TO 'inventory_user'@'localhost';
   FLUSH PRIVILEGES;
   ```
3. Disable remote database access if not needed
4. Regular backups with encryption

### Password Policy

Enforce strong passwords:
- Minimum 8 characters
- Mix of uppercase, lowercase, numbers, symbols
- No common passwords
- Regular password changes

### Monitoring & Logging

1. Enable error logging
2. Monitor failed login attempts
3. Log suspicious activities
4. Regular security audits
5. Keep logs secure and rotated

### Updates & Maintenance

- Keep PHP updated
- Update MySQL/MariaDB
- Update third-party libraries
- Monitor security advisories
- Regular security testing

## Reporting Security Issues

If you discover a security vulnerability, please email the system administrator immediately. Do not create public issues for security vulnerabilities.

## Additional Resources

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PHP Security Guide](https://www.php.net/manual/en/security.php)
- [MySQL Security](https://dev.mysql.com/doc/refman/8.0/en/security.html)
