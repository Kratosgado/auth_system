# PHP User Authentication System

A secure and comprehensive user authentication system built with PHP that includes user registration, login, and password reset functionality.

## Features

### Core Functionality
- **User Registration**: Create new accounts with validation
- **User Login**: Secure login with session management
- **Password Reset**: Forgot password functionality with token-based reset
- **Change Password**: Logged-in users can change their password
- **Dashboard**: User dashboard with account information and activity log
- **Session Management**: Secure session handling with configurable lifetime
- **CSRF Protection**: All forms protected against CSRF attacks

### Security Features
- Password hashing using bcrypt (cost factor: 12)
- Input sanitization and validation
- SQL injection prevention using PDO prepared statements
- CSRF token validation on all forms
- Login attempt tracking and rate limiting
- Session fixation protection
- XSS protection through htmlspecialchars
- Secure session configuration
- Password strength requirements (8+ chars, uppercase, lowercase, number, special character)

### Additional Features
- Responsive design (mobile-friendly)
- Flash messages for user feedback
- Remember me functionality
- Recent login activity tracking
- Account deactivation support
- Professional UI with gradient design

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher / MariaDB 10.2 or higher
- Apache/Nginx web server
- PDO MySQL extension enabled

## Installation

### 1. Clone or Download the Project

```bash
cd /var/www/html
# Or your web server's document root
```

### 2. Database Setup

Import the database schema:

```bash
mysql -u root -p < database.sql
```

Or manually create the database:
```sql
CREATE DATABASE auth_system;
```

Then import the tables from `database.sql` using phpMyAdmin or command line.

### 3. Configure Database Connection

Edit `config.php` and update the database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'auth_system');
define('DB_USER', 'your_username');     // Change this
define('DB_PASS', 'your_password');     // Change this
```

Also update the `SITE_URL`:
```php
define('SITE_URL', 'http://localhost/auth_system');  // Change to your URL
```

### 4. Set Permissions

Ensure the web server has read access to all files:

```bash
chmod -R 755 /path/to/auth_system
```

### 5. Configure Web Server

**Apache (.htaccess):**
Create a `.htaccess` file if needed:

```apache
RewriteEngine On
RewriteBase /auth_system/

# Redirect to HTTPS (uncomment in production)
# RewriteCond %{HTTPS} off
# RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

**Nginx:**
```nginx
location /auth_system {
    try_files $uri $uri/ /auth_system/index.php?$query_string;
}
```

### 6. Test the Installation

1. Navigate to: `http://localhost/auth_system`
2. You should be redirected to the login page
3. Click "Register here" to create a test account

## File Structure

```
auth_system/
├── config.php              # Configuration settings
├── database.php            # Database connection class
├── database.sql            # Database schema
├── utils.php               # Utility functions
├── index.php               # Entry point
├── register.php            # User registration
├── login.php               # User login
├── logout.php              # Logout handler
├── dashboard.php           # User dashboard
├── forgot-password.php     # Request password reset
├── reset-password.php      # Reset password with token
├── change-password.php     # Change password (logged in)
├── style.css               # CSS styles
└── README.md               # This file
```

## Database Schema

### users
- `id`: Primary key
- `username`: Unique username (3-50 chars)
- `email`: Unique email address
- `password`: Bcrypt hashed password
- `created_at`: Account creation timestamp
- `updated_at`: Last update timestamp
- `is_active`: Account status flag

### password_resets
- `id`: Primary key
- `user_id`: Foreign key to users table
- `token`: Unique reset token
- `expires_at`: Token expiration time
- `created_at`: Token creation timestamp
- `used`: Whether token has been used

### login_attempts
- `id`: Primary key
- `email`: Email address of login attempt
- `ip_address`: IP address of attempt
- `attempted_at`: Timestamp of attempt
- `successful`: Whether attempt was successful

## Usage

### User Registration

1. Navigate to `/register.php`
2. Fill in username, email, and password
3. Password must meet strength requirements
4. Submit form to create account

### User Login

1. Navigate to `/login.php`
2. Enter email and password
3. Optionally check "Remember me"
4. Submit to login

### Forgot Password

1. Click "Forgot Password?" on login page
2. Enter your email address
3. **DEMO MODE**: Reset link will be displayed on screen
4. **PRODUCTION**: Link would be sent via email
5. Click link and enter new password

### Change Password

1. Login to your account
2. Click "Change Password" button
3. Enter current password and new password
4. Submit to update

## Security Configuration

### Production Deployment

Before deploying to production:

1. **Disable Error Display** in `config.php`:
```php
error_reporting(0);
ini_set('display_errors', 0);
```

2. **Enable HTTPS** in `config.php`:
```php
ini_set('session.cookie_secure', 1); // Requires HTTPS
```

3. **Configure Email** for password resets:
   - Install PHPMailer: `composer require phpmailer/phpmailer`
   - Update `forgot-password.php` to send emails
   - Remove demo reset link display

4. **Update Site URL** in `config.php`:
```php
define('SITE_URL', 'https://yourdomain.com');
```

5. **Secure config.php**:
   - Move sensitive settings to environment variables
   - Use `.env` file (not tracked in git)

6. **Set Strong Session Settings**:
   - Already configured in `config.php`
   - Review and adjust timeouts as needed

### Password Requirements

Current requirements (can be modified in `utils.php`):
- Minimum 8 characters
- At least 1 uppercase letter
- At least 1 lowercase letter
- At least 1 number
- At least 1 special character

### Rate Limiting

Login attempts are limited to prevent brute force:
- Maximum 5 failed attempts per email/IP combination
- 15-minute lockout window
- Can be adjusted in `config.php`

## Customization

### Changing Password Requirements

Edit `utils.php` - `validatePassword()` function:

```php
public static function validatePassword($password) {
    // Modify validation rules here
}
```

### Adjusting Session Lifetime

Edit `config.php`:

```php
define('SESSION_LIFETIME', 3600); // In seconds
```

### Customizing Styles

Edit `style.css` to change colors, fonts, and layout:

```css
/* Main gradient colors */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

### Email Integration (Production)

Install PHPMailer:
```bash
composer require phpmailer/phpmailer
```

Update `forgot-password.php`:
```php
use PHPMailer\PHPMailer\PHPMailer;

$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'your@email.com';
$mail->Password = 'your-password';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;

$mail->setFrom('noreply@yourdomain.com', 'Auth System');
$mail->addAddress($email);
$mail->Subject = 'Password Reset Request';
$mail->Body = "Click here to reset: " . $resetLink;
$mail->send();
```

## Troubleshooting

### Database Connection Failed
- Check database credentials in `config.php`
- Ensure MySQL server is running
- Verify database exists: `SHOW DATABASES;`

### Session Issues
- Check PHP session configuration
- Ensure session directory is writable
- Verify session cookies are enabled

### Password Reset Not Working
- In demo mode, link appears on screen
- Check token hasn't expired (1 hour default)
- Verify user account is active

### Login Rate Limiting
- Wait 15 minutes after 5 failed attempts
- Clear old attempts: `DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 1 DAY);`

## Contributing

To extend this system:

1. Add new validation rules in `utils.php`
2. Create new pages following existing structure
3. Update database schema in `database.sql`
4. Use prepared statements for all queries
5. Implement CSRF protection on forms
6. Sanitize all user inputs

## Security Notes

- Never store passwords in plain text
- Always use prepared statements
- Validate and sanitize all inputs
- Keep PHP and MySQL updated
- Use HTTPS in production
- Regularly review security logs
- Implement account lockout policies
- Consider adding 2FA for enhanced security

## License

This project is open source and available for educational and commercial use.

## Support

For issues or questions:
1. Check the troubleshooting section
2. Review PHP error logs
3. Verify database connectivity
4. Check file permissions

## Version

Current Version: 1.0.0
Last Updated: February 2026
