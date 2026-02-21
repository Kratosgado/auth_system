# Quick Reference Guide

## Directory Structure at a Glance

```
auth_system/
├── config/                 # Database & app settings
├── database/               # SQL schema
├── includes/               # Core PHP (database.php, utils.php)
├── pages/
│   ├── auth/              # Login, register, password reset
│   └── user/              # Dashboard, settings
├── public/assets/css/      # Stylesheets
└── docs/                   # Documentation
```

## File Locations Quick Lookup

| Need to... | Go to... |
|-----------|----------|
| Change database credentials | `config/config.php` |
| Modify database schema | `database/database.sql` |
| Update login logic | `pages/auth/login.php` |
| Modify registration | `pages/auth/register.php` |
| Change dashboard | `pages/user/dashboard.php` |
| Update colors/styling | `public/assets/css/style.css` |
| Add validation rules | `includes/utils.php` |
| Modify DB connection | `includes/database.php` |

## Common Tasks

### Add New Auth Page
1. Create file in `pages/auth/`
2. Include: `require_once __DIR__ . '/../../includes/database.php';`
3. Link CSS: `<link rel="stylesheet" href="../../public/assets/css/style.css">`

### Add New User Page
1. Create file in `pages/user/`
2. Add: `Utils::requireLogin();` after session_start
3. Same includes as auth pages

### Change Colors
Edit `public/assets/css/style.css`:
- Line 10: Background gradient
- Line 74: Input focus color
- Line 105: Link color
- Line 130: Button gradient
- Line 307: Table header color

### Update Redirects
| From | To | Code |
|------|-----|------|
| Auth to User | Login → Dashboard | `header('Location: ../user/dashboard.php');` |
| User to Auth | Dashboard → Login | `header('Location: ../auth/login.php');` |
| Within Auth | Login → Register | `header('Location: register.php');` |
| Within User | Dashboard → Settings | `header('Location: change-password.php');` |

## Path Templates

### From `/pages/auth/` or `/pages/user/`
```php
// Includes
require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/utils.php';

// CSS
<link rel="stylesheet" href="../../public/assets/css/style.css">

// Navigate to auth page (from user page)
header('Location: ../auth/login.php');

// Navigate to user page (from auth page)
header('Location: ../user/dashboard.php');
```

### From Root (`index.php`)
```php
require_once __DIR__ . '/includes/utils.php';
header('Location: pages/auth/login.php');
header('Location: pages/user/dashboard.php');
```

## Color Codes

| Element | Color | Hex |
|---------|-------|-----|
| Background Start | Navy Blue | `#1e3a8a` |
| Background End | Royal Blue | `#2563eb` |
| Links/Accents | Royal Blue | `#2563eb` |
| Button Hover | Royal Blue 40% | `rgba(37, 99, 235, 0.4)` |

## Configuration Options

### Session Settings
```php
define('SESSION_LIFETIME', 3600);      // 1 hour
define('PASSWORD_RESET_EXPIRY', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);       // 5 attempts
define('LOGIN_ATTEMPT_WINDOW', 900);   // 15 minutes
```

### Security Settings
```php
ini_set('session.cookie_httponly', 1);  // JavaScript access
ini_set('session.cookie_secure', 0);    // HTTPS only (set to 1)
ini_set('session.cookie_samesite', 'Strict'); // CSRF protection
```

## Database Tables

| Table | Primary Key | Purpose |
|-------|-------------|---------|
| `users` | id | User accounts |
| `password_resets` | id | Reset tokens |
| `login_attempts` | id | Login history |

## Key Functions (utils.php)

### Validation
- `validateEmail($email)` - Email validation
- `validatePassword($password)` - Password strength
- `validateUsername($username)` - Username format

### Security
- `hashPassword($password)` - Bcrypt hashing
- `verifyPassword($password, $hash)` - Password verification
- `generateToken($length)` - Secure random tokens
- `sanitizeInput($data)` - XSS prevention

### Session
- `isLoggedIn()` - Check auth status
- `requireLogin()` - Force authentication
- `setFlashMessage($type, $message)` - Set message
- `getFlashMessage()` - Get and clear message

## URLs After Refactoring

| Page | Old URL | New URL |
|------|---------|---------|
| Index | `/` | `/` (unchanged) |
| Register | `/register.php` | `/pages/auth/register.php` |
| Login | `/login.php` | `/pages/auth/login.php` |
| Dashboard | `/dashboard.php` | `/pages/user/dashboard.php` |
| Logout | `/logout.php` | `/pages/auth/logout.php` |
| Forgot Password | `/forgot-password.php` | `/pages/auth/forgot-password.php` |
| Reset Password | `/reset-password.php` | `/pages/auth/reset-password.php` |
| Change Password | `/change-password.php` | `/pages/user/change-password.php` |

## Documentation Files

| File | Purpose |
|------|---------|
| `README.md` | Main documentation |
| `STRUCTURE.md` | Directory structure details |
| `COLORS.md` | Color theme reference |
| `REFACTORING.md` | Refactoring summary |
| `QUICK_REFERENCE.md` | This file |
| `docs/SETUP.md` | Setup instructions |
| `docs/PROJECT_OVERVIEW.md` | Technical overview |

## Git Commands

```bash
# Initial setup
git init
git add .
git commit -m "Initial commit - v2.0.0"

# Update config (don't commit config.php!)
cp config/config.template.php config/config.php
# Edit config.php (it's in .gitignore)

# Make changes
git add .
git commit -m "Your message"
git push origin main
```

## Testing Checklist

Quick tests after refactoring:

```bash
# 1. Check all files exist
ls config/config.php
ls pages/auth/login.php
ls pages/user/dashboard.php
ls public/assets/css/style.css

# 2. Verify database
mysql -u root -p auth_system -e "SHOW TABLES;"

# 3. Test in browser
# Visit: http://localhost/auth_system
# Should redirect to /pages/auth/login.php

# 4. Test registration
# Visit: /pages/auth/register.php
# Create test account

# 5. Test login
# Visit: /pages/auth/login.php
# Login with test account
```

## Troubleshooting

| Problem | Solution |
|---------|----------|
| CSS not loading | Check path: `../../public/assets/css/style.css` |
| Include errors | Verify path: `__DIR__ . '/../../includes/file.php'` |
| Redirect loops | Check `Utils::isLoggedIn()` logic |
| Database error | Verify `config/config.php` credentials |
| Session not working | Check PHP session configuration |

## Version Info

- **Version**: 2.0.0
- **Structure**: MVC-Inspired
- **Theme**: Royal Blue
- **PHP**: 7.4+
- **Database**: MySQL 5.7+

---

**Last Updated**: February 2026  
**For detailed info**: See README.md or docs/
