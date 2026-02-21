# Directory Structure

## Complete Project Structure

```
auth_system/
│
├── config/                          # Configuration Directory
│   ├── config.php                  # Main configuration (GITIGNORED)
│   └── config.template.php         # Configuration template
│
├── database/                        # Database Directory
│   └── database.sql                # MySQL schema (3 tables)
│
├── includes/                        # Core PHP Classes
│   ├── database.php                # Database connection singleton
│   └── utils.php                   # Utility functions & validation
│
├── pages/                           # Application Pages
│   │
│   ├── auth/                       # Authentication Pages
│   │   ├── register.php            # User registration form & handler
│   │   ├── login.php               # Login form & authentication
│   │   ├── logout.php              # Logout handler
│   │   ├── forgot-password.php     # Request password reset
│   │   └── reset-password.php      # Reset password with token
│   │
│   └── user/                       # User Dashboard Pages (Protected)
│       ├── dashboard.php           # Main user dashboard
│       └── change-password.php     # Change password page
│
├── public/                          # Public Assets Directory
│   └── assets/                     # Static Assets
│       └── css/
│           └── style.css           # Royal blue themed CSS
│
├── docs/                            # Documentation Directory
│   ├── README.md                   # Comprehensive documentation
│   ├── SETUP.md                    # Quick setup guide
│   └── PROJECT_OVERVIEW.md         # Technical overview
│
├── index.php                        # Application entry point
├── test.php                         # Installation test script
├── .gitignore                      # Git ignore rules
└── README.md                       # Main README (this file)
```

## Directory Purposes

### `/config`
**Purpose**: Application configuration and settings  
**Contents**:
- Database credentials
- Application settings
- Security configuration
- Session parameters

**Security**: `config.php` is gitignored and should never be committed

### `/database`
**Purpose**: Database schema and migration files  
**Contents**:
- SQL schema definitions
- Database structure

**Tables**:
- `users` - User accounts
- `password_resets` - Password reset tokens
- `login_attempts` - Login history and rate limiting

### `/includes`
**Purpose**: Reusable PHP classes and utilities  
**Contents**:
- Core application logic
- Database connection management
- Utility functions

**Key Files**:
- `database.php` - Singleton database connection with PDO
- `utils.php` - Validation, security, and helper functions

### `/pages`
**Purpose**: Application pages organized by function  

#### `/pages/auth`
**Access**: Public (unauthenticated users)  
**Pages**:
- Registration
- Login
- Logout
- Password reset flow

#### `/pages/user`
**Access**: Protected (requires authentication)  
**Pages**:
- User dashboard
- Account management
- Password change

### `/public`
**Purpose**: Static assets served by web server  
**Contents**:
- CSS stylesheets
- JavaScript files (if added)
- Images and icons (if added)

**Note**: This directory should be web-accessible

### `/docs`
**Purpose**: Project documentation  
**Contents**:
- Setup guides
- API documentation (if applicable)
- Technical specifications

## File Naming Conventions

- **Kebab case** for PHP page files: `forgot-password.php`
- **Camel case** for PHP class files: `Database.php`
- **Lowercase** for directories: `pages/auth/`
- **Kebab case** for CSS files: `style.css`

## Path Conventions

### From Root Level (`index.php`)
```php
require_once __DIR__ . '/includes/utils.php';
```

### From `/pages/auth/` Level
```php
require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/utils.php';
```

### From `/pages/user/` Level
```php
require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/utils.php';
```

### CSS Path from Pages
```html
<!-- From /pages/auth/ or /pages/user/ -->
<link rel="stylesheet" href="../../public/assets/css/style.css">
```

## Routing Structure

### URL Patterns

```
/                                    → index.php (redirects)
/pages/auth/register.php             → Registration
/pages/auth/login.php                → Login
/pages/auth/logout.php               → Logout
/pages/auth/forgot-password.php      → Request reset
/pages/auth/reset-password.php       → Reset with token
/pages/user/dashboard.php            → User dashboard
/pages/user/change-password.php      → Change password
```

### Redirect Flow

```
User not logged in:
index.php → pages/auth/login.php

User logged in:
index.php → pages/user/dashboard.php

After registration:
pages/auth/register.php → pages/auth/login.php

After login:
pages/auth/login.php → pages/user/dashboard.php

After logout:
pages/auth/logout.php → pages/auth/login.php
```

## Database Schema

### users
```sql
id              INT PRIMARY KEY AUTO_INCREMENT
username        VARCHAR(50) UNIQUE NOT NULL
email           VARCHAR(100) UNIQUE NOT NULL
password        VARCHAR(255) NOT NULL
created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
updated_at      TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
is_active       TINYINT(1) DEFAULT 1
```

### password_resets
```sql
id              INT PRIMARY KEY AUTO_INCREMENT
user_id         INT FOREIGN KEY → users(id)
token           VARCHAR(255) UNIQUE NOT NULL
expires_at      DATETIME NOT NULL
created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
used            TINYINT(1) DEFAULT 0
```

### login_attempts
```sql
id              INT PRIMARY KEY AUTO_INCREMENT
email           VARCHAR(100) NOT NULL
ip_address      VARCHAR(45) NOT NULL
attempted_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
successful      TINYINT(1) DEFAULT 0
```

## Security Structure

### File-Level Security

```
config/config.php          → 600 (read/write owner only)
includes/*.php             → 644 (readable by all, writable by owner)
pages/**/*.php             → 644
public/assets/css/*.css    → 644
```

### Code-Level Security

- **Input Validation**: All user inputs validated in `utils.php`
- **SQL Injection**: PDO prepared statements in `database.php`
- **XSS Protection**: htmlspecialchars() on all output
- **CSRF Protection**: Token validation on all forms
- **Session Security**: Secure cookie settings in `config/config.php`

## Adding New Features

### Adding a New Public Page

1. Create file in `/pages/auth/`
2. Include required files:
   ```php
   require_once __DIR__ . '/../../includes/database.php';
   require_once __DIR__ . '/../../includes/utils.php';
   ```
3. Link CSS: `../../public/assets/css/style.css`

### Adding a New Protected Page

1. Create file in `/pages/user/`
2. Add authentication check:
   ```php
   Utils::requireLogin();
   ```
3. Follow same include pattern as public pages

### Adding New Styles

1. Edit `/public/assets/css/style.css`
2. Follow existing royal blue color scheme
3. Maintain responsive design patterns

## Version History

**v2.0.0** - Refactored Structure
- MVC-inspired directory organization
- Royal blue design theme
- Improved security practices
- Better code organization

**v1.0.0** - Initial Release
- Basic authentication functionality
- Flat file structure
- Purple/violet color scheme
