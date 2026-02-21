# PHP User Authentication System

A secure and comprehensive user authentication system built with PHP and MySQL, featuring a clean MVC-inspired directory structure and royal blue design theme.

## Features

### Core Functionality
- **User Registration**: Create new accounts with comprehensive validation
- **User Login**: Secure authentication with session management
- **Password Reset**: Token-based forgot password functionality
- **Change Password**: Logged-in users can update their password
- **User Dashboard**: View account information and login activity
- **Session Management**: Secure session handling with configurable lifetime
- **CSRF Protection**: All forms protected against CSRF attacks

### Security Features
- Password hashing using bcrypt (cost factor: 12)
- Input sanitization and validation
- SQL injection prevention using PDO prepared statements
- CSRF token validation on all forms
- Login attempt tracking and rate limiting (5 attempts / 15 min)
- Session fixation protection
- XSS protection through output encoding
- Secure session configuration

## Directory Structure

```
auth_system/
│
├── config/                      # Configuration files
│   ├── config.php              # Database & app configuration (gitignored)
│   └── config.template.php     # Configuration template
│
├── database/                    # Database schema
│   └── database.sql            # MySQL schema file
│
├── includes/                    # Core classes and utilities
│   ├── database.php            # Database connection class
│   └── utils.php               # Utility functions
│
├── pages/                       # Application pages
│   ├── auth/                   # Authentication pages
│   │   ├── register.php        # User registration
│   │   ├── login.php           # User login
│   │   ├── logout.php          # Logout handler
│   │   ├── forgot-password.php # Request password reset
│   │   └── reset-password.php  # Reset password with token
│   └── user/                   # User dashboard pages
│       ├── dashboard.php       # User dashboard
│       └── change-password.php # Change password
│
├── public/                      # Publicly accessible files
│   └── assets/                 # Static assets
│       └── css/
│           └── style.css       # Royal blue themed stylesheet
│
├── docs/                        # Documentation
│   ├── README.md               # Main documentation
│   ├── SETUP.md                # Setup instructions
│   └── PROJECT_OVERVIEW.md     # Detailed overview
│
├── index.php                    # Entry point (redirects based on auth)
└── .gitignore                  # Git ignore rules
```

## Requirements

- PHP 7.4 or higher
- MySQL 5.7+ / MariaDB 10.2+
- Apache/Nginx web server
- PDO MySQL extension enabled

## Quick Start

### 1. Database Setup

```bash
# Import the database schema
mysql -u root -p < database/database.sql
```

### 2. Configuration

```bash
# Copy the config template
cp config/config.template.php config/config.php

# Edit with your database credentials
nano config/config.php
```

Update these values:
```php
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('SITE_URL', 'http://localhost/auth_system');
```

### 3. Access the Application

Navigate to: `http://localhost/auth_system`

You'll be redirected to the login page. Click "Register here" to create an account.

## Design Theme

The application features a modern royal blue color scheme:
- **Primary Gradient**: `#1e3a8a` to `#2563eb` (Navy Blue to Royal Blue)
- **Accent Color**: `#2563eb` (Royal Blue)
- **Background**: Gradient blue backdrop
- Responsive design optimized for mobile and desktop

## Security Best Practices

### Password Requirements
- Minimum 8 characters
- At least 1 uppercase letter
- At least 1 lowercase letter
- At least 1 number
- At least 1 special character

### Rate Limiting
- Maximum 5 failed login attempts
- 15-minute lockout per email/IP combination

### Session Security
- HTTPOnly cookies
- SameSite: Strict
- Session regeneration on login
- Configurable session lifetime

## File Structure Explained

### `/config`
Contains application configuration. The actual `config.php` is gitignored for security.

### `/database`
SQL schema files for database setup.

### `/includes`
Core PHP classes and utilities that are reused across the application.

### `/pages`
- **`/auth`**: Public authentication pages (login, register, password reset)
- **`/user`**: Protected user pages (dashboard, settings)

### `/public`
Static assets served directly by the web server. CSS, JS, and images go here.

### `/docs`
Comprehensive documentation including setup guides and technical overview.

## Usage Examples

### Registration
1. Navigate to `/pages/auth/register.php`
2. Fill in username, email, and password
3. Submit to create account

### Login
1. Navigate to `/pages/auth/login.php`
2. Enter email and password
3. Optionally check "Remember me"
4. Submit to login

### Password Reset
1. Click "Forgot Password?" on login page
2. Enter your email address
3. **Demo Mode**: Copy the displayed reset link
4. **Production**: Check your email for the reset link
5. Follow link and enter new password

## Production Deployment

Before deploying to production:

1. **Update `config/config.php`**:
   ```php
   error_reporting(0);
   ini_set('display_errors', 0);
   ini_set('session.cookie_secure', 1); // Requires HTTPS
   define('SITE_URL', 'https://yourdomain.com');
   ```

2. **Configure Email**: Install PHPMailer and update `forgot-password.php`

3. **Enable HTTPS**: Use SSL/TLS certificate

4. **Secure Permissions**:
   ```bash
   chmod 644 pages/**/*.php
   chmod 644 public/assets/css/*.css
   chmod 600 config/config.php
   ```

5. **Remove Demo Features**: Delete demo reset link display in `forgot-password.php`

## Customization

### Changing Colors
Edit `/public/assets/css/style.css`:
```css
/* Change primary gradient */
background: linear-gradient(135deg, #YourColor1 0%, #YourColor2 100%);

/* Change accent color */
color: #YourAccentColor;
```

### Adjusting Security Settings
Edit `/config/config.php`:
```php
define('SESSION_LIFETIME', 7200); // 2 hours
define('MAX_LOGIN_ATTEMPTS', 3);  // 3 attempts
define('LOGIN_ATTEMPT_WINDOW', 600); // 10 minutes
```

## Troubleshooting

### Database Connection Error
- Verify MySQL is running
- Check credentials in `config/config.php`
- Ensure database exists

### CSS Not Loading
- Check file path in PHP files: `../../public/assets/css/style.css`
- Verify web server is serving static files from `/public`

### Session Issues
- Check PHP session directory permissions
- Verify cookies are enabled in browser

## Documentation

- **Setup Guide**: `docs/SETUP.md` - Quick setup instructions
- **Overview**: `docs/PROJECT_OVERVIEW.md` - Detailed technical overview
- **Main Docs**: `docs/README.md` - Complete documentation

## License

Open source - free for educational and commercial use.

## Version

**Current Version**: 2.0.0  
**Last Updated**: February 2026  
**Architecture**: MVC-inspired structure  
**Design Theme**: Royal Blue  

---

**Note**: This is a refactored version with improved directory structure and updated design theme.
