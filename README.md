# Guidance Office Inventory System

A professional student management system for guidance offices with secure authentication, file uploads, and PDF export capabilities.

## Features

- Secure authentication with master key for admin
- Student self-registration with photo upload
- Separate login pages for Admin and Students
- Student login with Student ID
- Separate dashboards for Admin and Students
- Student record management (Add, Edit, Delete, View)
- QR code generation for students
- QR code scanner for quick student lookup
- Photo upload with validation
- Search and pagination
- PDF export functionality
- CSRF protection
- SQL injection prevention
- Session management
- Responsive Bootstrap UI

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- FPDF library (for PDF generation)

## Installation

1. Clone or download this repository to your web server directory

2. Create a `.env` file by copying `.env.example`:
   ```bash
   cp .env.example .env
   ```

3. Update `.env` with your database credentials:
   ```
   DB_HOST=localhost
   DB_NAME=inventory_db
   DB_USER=your_username
   DB_PASS=your_password
   ```

4. Import the database schema:
   ```bash
   mysql -u your_username -p < database/schema.sql
   ```

5. Download FPDF library:
   - Download from: http://www.fpdf.org/
   - Extract to `libs/fpdf/` directory
   - Ensure `libs/fpdf/fpdf.php` exists

6. Set proper permissions:
   ```bash
   chmod 755 uploads/
   chmod 644 uploads/.htaccess
   ```

7. Access the application:
   ```
   http://localhost/your-project-folder/
   ```

## Default Login Credentials

### Master Admin (Only Admin Access)
- Master Key: `GuidanceAdmin2024!`
- **CRITICAL:** Change the master key in `config/master_key.php` immediately!

### Students
- Students can self-register through the Student Portal
- Or admins can create student accounts with default passwords

**IMPORTANT:** Change the master key immediately after installation!

## Security Features

- Password hashing using bcrypt
- Prepared statements to prevent SQL injection
- CSRF token validation
- Secure file upload validation
- Session security with httponly cookies
- Input sanitization and validation
- Protected uploads directory

## Directory Structure

```
├── admin/              # Admin pages
├── auth/               # Authentication pages
├── config/             # Configuration files
├── database/           # Database schema
├── includes/           # Reusable components
├── libs/               # Third-party libraries
├── process/            # Form processing handlers
├── uploads/            # Uploaded files (secured)
├── .env                # Environment configuration
└── index.php           # Entry point
```

## Usage

### Student Registration
1. Go to Student Portal from home page
2. Click "Register here" on the login page
3. Fill in your information:
   - Student ID (unique identifier)
   - Personal information (name, email)
   - Upload your photo
   - Create a password
4. Submit registration
5. Login with your Student ID and password

### Student Features
- View personal information
- Generate QR code with your data
- Download/Print QR code
- Show QR code to guidance office for quick access
- Change password

### Admin Features
### Admin Features
1. Navigate to Dashboard
2. Click "Add New Student"
3. Fill in student information
4. Upload photo (JPG, PNG, or GIF, max 5MB)
5. Set default password for student
6. Click "Save Student"

### Viewing Students
- View all students with pagination
- Search by ID, name, or email
- Edit or delete student records

### Exporting to PDF
- Click "Export PDF" to generate a report
- Includes all student records
- Supports search filters

## Configuration

Edit `config/config.php` or `.env` file to customize:
- Database settings
- Upload limits
- Session timeout
- Allowed file types
- Application environment

## Troubleshooting

### Database Connection Error
- Verify database credentials in `.env`
- Ensure MySQL service is running
- Check database exists

### File Upload Issues
- Check `uploads/` directory permissions
- Verify PHP `upload_max_filesize` setting
- Ensure `.htaccess` is in uploads directory

### PDF Export Not Working
- Verify FPDF library is installed in `libs/fpdf/`
- Check file permissions

## Security Recommendations

1. Change default admin password
2. Use HTTPS in production
3. Set `APP_ENV=production` in `.env`
4. Keep PHP and MySQL updated
5. Regular database backups
6. Restrict database user permissions
7. Enable error logging

## License

This project is open source and available for educational purposes.

## Support

For issues or questions, please contact your system administrator.
