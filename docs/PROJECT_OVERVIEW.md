# PHP Authentication System - Project Overview

## Project Summary

A production-ready user authentication system built with PHP and MySQL that provides secure user registration, login, password reset, and account management functionality.

## Key Features Implemented

### 1. User Registration (register.php)
- Username validation (3-50 characters, alphanumeric + underscore)
- Email validation and uniqueness check
- Strong password requirements enforcement
- Duplicate username/email prevention
- CSRF protection
- Input sanitization
- Bcrypt password hashing

### 2. User Login (login.php)
- Secure authentication with prepared statements
- Session management with regeneration
- "Remember me" functionality (30-day cookie)
- Rate limiting (5 attempts per 15 minutes)
- Login attempt tracking
- Account status verification
- Flash message feedback

### 3. Password Reset (forgot-password.php, reset-password.php)
- Token-based password reset system
- Time-limited reset tokens (1 hour expiry)
- Secure token generation (64-char random)
- One-time use tokens
- Email obfuscation (doesn't reveal if email exists)
- Demo mode with visible reset link

### 4. Change Password (change-password.php)
- Current password verification
- New password validation
- Prevents reusing current password
- Requires authentication
- Secure password update with transaction

### 5. User Dashboard (dashboard.php)
- User profile information display
- Account creation/update timestamps
- Recent login activity (last 5 attempts)
- Success/failure status for each attempt
- IP address tracking
- Quick access to password change

### 6. Security Features
- **SQL Injection Prevention**: PDO prepared statements throughout
- **XSS Protection**: htmlspecialchars on all output
- **CSRF Protection**: Token validation on all forms
- **Password Security**: Bcrypt with cost factor 12
- **Session Security**: HTTPOnly cookies, regeneration, configurable lifetime
- **Input Validation**: Server-side validation on all inputs
- **Rate Limiting**: Login attempt tracking and throttling
- **Session Fixation**: Prevention via regeneration on login

## File Structure

```
auth_system/
│
├── Core Files
│   ├── config.php              # Application configuration (DO NOT COMMIT)
│   ├── config.template.php     # Configuration template (safe to commit)
│   ├── database.php            # Database connection singleton class
│   ├── utils.php               # Utility functions (security, validation)
│   └── index.php               # Entry point (redirects based on auth status)
│
├── Database
│   └── database.sql            # MySQL schema (users, password_resets, login_attempts)
│
├── Authentication Pages
│   ├── register.php            # User registration form and handler
│   ├── login.php               # User login form and handler
│   ├── logout.php              # Session destruction and cleanup
│   ├── forgot-password.php     # Request password reset token
│   ├── reset-password.php      # Reset password with token
│   └── change-password.php     # Change password (authenticated users)
│
├── User Pages
│   └── dashboard.php           # User dashboard with account info
│
├── Assets
│   └── style.css               # Responsive CSS with gradient design
│
├── Documentation
│   ├── README.md               # Comprehensive documentation
│   ├── SETUP.md                # Quick setup guide
│   └── .gitignore              # Git ignore rules
│
└── Testing
    └── test.php                # Installation verification script
```

## Database Schema

### Table: users
```sql
id              INT PRIMARY KEY AUTO_INCREMENT
username        VARCHAR(50) UNIQUE NOT NULL
email           VARCHAR(100) UNIQUE NOT NULL
password        VARCHAR(255) NOT NULL (bcrypt hash)
created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
is_active       TINYINT(1) DEFAULT 1
```

### Table: password_resets
```sql
id              INT PRIMARY KEY AUTO_INCREMENT
user_id         INT FOREIGN KEY (users.id)
token           VARCHAR(255) UNIQUE NOT NULL
expires_at      DATETIME NOT NULL
created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
used            TINYINT(1) DEFAULT 0
```

### Table: login_attempts
```sql
id              INT PRIMARY KEY AUTO_INCREMENT
email           VARCHAR(100) NOT NULL
ip_address      VARCHAR(45) NOT NULL
attempted_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
successful      TINYINT(1) DEFAULT 0
```

## Security Implementation Details

### Password Hashing
- Algorithm: bcrypt (PASSWORD_BCRYPT)
- Cost factor: 12 (2^12 = 4096 iterations)
- Auto-salting per password
- Future-proof with password_needs_rehash()

### Password Requirements
- Minimum 8 characters
- At least 1 uppercase letter (A-Z)
- At least 1 lowercase letter (a-z)
- At least 1 number (0-9)
- At least 1 special character (!@#$%^&*...)

### Session Configuration
```php
session.cookie_httponly = 1     # Prevent JavaScript access
session.use_only_cookies = 1    # No session ID in URLs
session.cookie_secure = 0       # Set to 1 for HTTPS
session.cookie_samesite = Strict # CSRF protection
```

### CSRF Protection
- Unique token per session
- Validated on all POST requests
- Token regeneration on login
- Timing-safe comparison (hash_equals)

### Rate Limiting
- Max 5 failed login attempts
- Per email/IP combination
- 15-minute lockout window
- Automatic cleanup of old attempts

## User Workflow

### Registration Flow
1. User fills registration form
2. Client-side HTML5 validation
3. Server-side validation (username, email, password)
4. Check for duplicate username/email
5. Hash password with bcrypt
6. Insert user into database
7. Redirect to login with success message

### Login Flow
1. User submits credentials
2. Check rate limiting (IP + email)
3. Fetch user from database by email
4. Verify password with password_verify()
5. Check account is_active status
6. Regenerate session ID
7. Set session variables
8. Log successful attempt
9. Redirect to dashboard

### Password Reset Flow
1. User requests reset with email
2. Verify user exists and is active
3. Generate secure 64-character token
4. Store token with 1-hour expiry
5. Send reset link (demo: display on screen)
6. User clicks link with token
7. Verify token is valid and not expired
8. User sets new password
9. Update password in database
10. Mark token as used
11. Redirect to login

### Change Password Flow
1. Verify user is authenticated
2. Validate current password
3. Validate new password strength
4. Ensure new ≠ current password
5. Update password in database
6. Redirect with success message

## Configuration Options

### config.php Settings
```php
DB_HOST                    # Database host (default: localhost)
DB_NAME                    # Database name (default: auth_system)
DB_USER                    # Database username
DB_PASS                    # Database password
SITE_URL                   # Full site URL
SESSION_LIFETIME           # Session duration in seconds (default: 3600)
PASSWORD_RESET_EXPIRY      # Reset token validity (default: 3600)
MAX_LOGIN_ATTEMPTS         # Max failed logins (default: 5)
LOGIN_ATTEMPT_WINDOW       # Lockout duration (default: 900)
```

## Utility Functions (utils.php)

### Validation
- `validateEmail()` - RFC-compliant email validation
- `validatePassword()` - Password strength checker
- `validateUsername()` - Username format validation
- `sanitizeInput()` - XSS prevention through sanitization

### Security
- `hashPassword()` - Bcrypt password hashing
- `verifyPassword()` - Password verification
- `generateToken()` - Cryptographically secure tokens
- `generateCsrfToken()` - CSRF token generation
- `verifyCsrfToken()` - CSRF token validation

### Session Management
- `isLoggedIn()` - Check authentication status
- `requireLogin()` - Force authentication
- `setFlashMessage()` - Store one-time messages
- `getFlashMessage()` - Retrieve and clear messages

### Helpers
- `getIpAddress()` - Get client IP (proxy-aware)
- `redirect()` - Safe redirection

## Responsive Design

The CSS provides:
- Mobile-first approach
- Breakpoints: 768px (tablet), 480px (mobile)
- Gradient background (purple/blue)
- Card-based layouts
- Accessible color contrast
- Touch-friendly buttons (44px+ tap targets)

## Testing Checklist

Run test.php to verify:
- [x] PHP version ≥ 7.4
- [x] PDO MySQL extension loaded
- [x] config.php exists
- [x] Database connection successful
- [x] All tables created
- [x] Session support enabled
- [x] All required files present
- [x] Proper file permissions

Manual testing:
- [ ] Register new account
- [ ] Login with credentials
- [ ] Test "Remember me"
- [ ] View dashboard
- [ ] Check login history
- [ ] Change password
- [ ] Logout
- [ ] Forgot password flow
- [ ] Reset password with token
- [ ] Test rate limiting (5+ failed logins)
- [ ] Test CSRF protection (modify token)
- [ ] Test SQL injection (try ' OR '1'='1)
- [ ] Test XSS (try <script>alert('xss')</script>)

## Production Deployment Checklist

Before going live:
- [ ] Change all database credentials
- [ ] Set SITE_URL to production domain
- [ ] Enable HTTPS (session.cookie_secure = 1)
- [ ] Disable error display (display_errors = 0)
- [ ] Configure real email sending (PHPMailer)
- [ ] Remove demo reset link display
- [ ] Delete test.php file
- [ ] Set up SSL certificate
- [ ] Configure server firewall
- [ ] Set up regular database backups
- [ ] Enable error logging to file
- [ ] Review and restrict file permissions
- [ ] Set up monitoring/alerts
- [ ] Perform security audit
- [ ] Load testing
- [ ] Update .htaccess for production

## Extending the System

### Add Email Verification
1. Add `email_verified` column to users table
2. Create verification_tokens table
3. Send verification email on registration
4. Create email verification handler
5. Require verification before login

### Add Two-Factor Authentication (2FA)
1. Add `two_factor_secret` column to users
2. Install Google Authenticator library
3. Create 2FA setup page
4. Add 2FA verification to login flow
5. Store backup codes

### Add Social Login (OAuth)
1. Install OAuth library (e.g., league/oauth2-client)
2. Register apps with providers
3. Create OAuth callback handlers
4. Link social accounts to users table
5. Allow unlinking accounts

### Add User Roles/Permissions
1. Create roles and permissions tables
2. Implement role assignment
3. Add middleware for authorization
4. Create admin dashboard
5. Implement permission checking

## Common Issues & Solutions

### "Database connection failed"
Solution: Check MySQL is running, verify credentials in config.php

### "Too many login attempts"
Solution: Wait 15 minutes or clear login_attempts table

### Session not persisting
Solution: Check session directory permissions, verify cookies enabled

### Password reset not working
Solution: Check token hasn't expired (1 hour), verify email exists

### Styling not loading
Solution: Check style.css path, verify web server serving static files

## Performance Considerations

- Database connection pooling via PDO persistent connections
- Indexed columns (email, username, token) for fast lookups
- Minimal queries per page (1-3 typical)
- Session storage in files (consider Redis for scale)
- CSS minification for production
- Enable gzip compression
- Cache static assets
- Consider CDN for CSS/JS

## Browser Compatibility

Tested on:
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- Mobile browsers (iOS Safari, Chrome Mobile)

## License

Open source - free for educational and commercial use.

## Credits

Built with security best practices from:
- OWASP Top 10
- PHP Security Guide
- MySQL Security Best Practices
- Session Management Guidelines

## Support

For issues:
1. Check test.php results
2. Review README.md
3. Check PHP error logs
4. Verify database connectivity
5. Review SETUP.md

---

**Version:** 1.0.0  
**Last Updated:** February 2026  
**PHP Requirement:** 7.4+  
**MySQL Requirement:** 5.7+
