# Changelog

All notable changes to the Guidance Office Inventory System.

## [2.0.0] - Professional System Upgrade

### Added
- Environment-based configuration with `.env` support
- PDO database connection with singleton pattern
- Comprehensive helper functions library
- Secure session management
- CSRF protection on all forms
- Password hashing with bcrypt
- Input validation and sanitization
- Secure file upload with validation
- Flash message system
- Professional UI with Bootstrap 5 and Bootstrap Icons
- Responsive navigation bar
- Dashboard with statistics
- Search functionality with pagination
- Edit student functionality
- Delete student functionality
- Image preview on upload
- Activity logging structure
- Comprehensive documentation (README, SECURITY)
- Database schema with proper indexes
- `.htaccess` security configurations
- `.gitignore` for version control

### Changed
- Migrated from mysqli to PDO
- Replaced MD5 with bcrypt password hashing
- Converted all SQL queries to prepared statements
- Improved UI/UX across all pages
- Enhanced error handling
- Restructured file organization
- Improved PDF export with better formatting

### Security
- Fixed SQL injection vulnerabilities
- Fixed XSS vulnerabilities
- Added CSRF token validation
- Implemented secure file uploads
- Added session security measures
- Protected uploads directory from PHP execution
- Added input sanitization
- Implemented proper authentication checks

### Removed
- Direct SQL string concatenation
- Insecure MD5 password hashing
- Inline HTML/PHP mixing (improved separation)

## [1.0.0] - Initial Release

### Features
- Basic login system
- Student management (add, view)
- Photo upload
- PDF export
- Simple dashboard
