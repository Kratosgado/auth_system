# Quick Setup Guide

Follow these steps to get the authentication system up and running:

## Step 1: Database Setup

```bash
# Login to MySQL
mysql -u root -p

# Create the database
CREATE DATABASE auth_system_db;

# Import the schema
USE auth_system_db;
SOURCE database.sql;

# Or import via command line
mysql -u root -p auth_system_db < database.sql
```

## Step 2: Configure the Application

```bash
# Copy the config template
cp config.template.php config.php

# Edit config.php with your settings
nano config.php  # or use your preferred editor
```

Update these values in `config.php`:

- `DB_USER`: Your MySQL username
- `DB_PASS`: Your MySQL password
- `SITE_URL`: Your site URL (e.g., <http://localhost/auth_system_db>)

## Step 3: Set Permissions

```bash
# Make sure files are readable by web server
chmod 644 *.php *.css
chmod 755 .
```

## Step 4: Test the System

1. Open your browser and navigate to:

   ```
   http://localhost/auth_system_db
   ```

2. You should be redirected to the login page

3. Click "Register here" to create a test account:
   - Username: testuser
   - Email: <test@example.com>
   - Password: Test123!@# (meets all requirements)

4. After registration, login with your credentials

5. Test the password reset:
   - Logout
   - Click "Forgot Password?"
   - Enter your email
   - Copy the reset link shown (DEMO MODE)
   - Reset your password

## Common Issues

### "Database connection failed"

- Check MySQL is running: `sudo service mysql status`
- Verify credentials in `config.php`
- Ensure database exists: `SHOW DATABASES;`

### "Page not found" or 404 errors

- Check your web server is running
- Verify SITE_URL in config.php matches your actual URL
- Ensure files are in correct directory

### Session issues

- Check PHP sessions are enabled: `php -i | grep session`
- Verify session directory is writable

## Next Steps

- Review README.md for detailed documentation
- Customize styles in style.css
- Set up email for password resets (production)
- Enable HTTPS for production deployment

## Testing Checklist

- [ ] Register new account
- [ ] Login with created account
- [ ] View dashboard
- [ ] Check recent login activity
- [ ] Change password
- [ ] Logout
- [ ] Test "Remember me" checkbox
- [ ] Request password reset
- [ ] Use reset link to change password
- [ ] Test invalid login (should lock after 5 attempts)

## Security Reminders

For Production:

- [ ] Set `display_errors` to 0 in config.php
- [ ] Enable HTTPS
- [ ] Set `session.cookie_secure` to 1
- [ ] Configure real email sending
- [ ] Change default database credentials
- [ ] Regular security audits
- [ ] Keep PHP and MySQL updated

Happy coding!
