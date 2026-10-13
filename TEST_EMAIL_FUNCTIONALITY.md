# Test Email Functionality Guide

## Current Email Configuration

Your email is currently set to **LOG mode** in `.env`:
```
MAIL_MAILER=log
```

This means all emails will be **written to the log file** instead of being sent. This is perfect for testing!

---

## How to Test Email Features

### Test 1: Create a New User (Welcome Email)

1. **Login as Admin**
   - Go to: http://127.0.0.1:8000/login
   - Email: `admin@projecttracker.com`
   - Password: `password`

2. **Create a New User**
   - Navigate to: Users Management → "Add New User"
   - Fill in the form:
     - Name: Test User
     - Email: testuser@example.com
     - Select at least one role (e.g., Developer)
     - Check "Active"
   - Click "Create User"

3. **Check the Result**
   - You should see a success message: "User created successfully. Welcome email sent to testuser@example.com"

4. **View the Email in Log**
   - Open: `C:\xampp\htdocs\ga_tracker\project-tracker\storage\logs\laravel.log`
   - Search for "Welcome to Project Tracker"
   - You'll see the full email content including the temporary password

---

### Test 2: Reset Password (Admin Resend)

1. **Go to Users Management**
   - Navigate to: Users Management
   - Find the user you just created

2. **Click the Key Icon (🔑)**
   - This is the "Reset Password" button
   - Confirm the action

3. **Check the Result**
   - You should see: "Password reset successfully. Email sent to testuser@example.com"

4. **View the Email in Log**
   - Open: `storage/logs/laravel.log`
   - Search for "Password Reset"
   - You'll see the password reset email with the new password

---

### Test 3: Forgot Password (User Self-Service)

1. **Logout** (if logged in)

2. **Go to Login Page**
   - http://127.0.0.1:8000/login

3. **Click "Forgot your password?"**
   - Should take you to the password reset request page

4. **Enter an Existing Email**
   - Use: `admin@projecttracker.com` (or any other existing user)
   - Click "Send Password Reset Link"

5. **Check the Result**
   - You should see: "We have emailed your password reset link!"

6. **View the Email in Log**
   - Open: `storage/logs/laravel.log`
   - Search for "password/reset"
   - You'll see a reset link like: `http://127.0.0.1:8000/password/reset/[token]?email=admin%40projecttracker.com`

7. **Copy and Visit the Reset Link**
   - Copy the full URL from the log
   - Paste it in your browser
   - You'll see the "Set New Password" form with Green Apple branding

8. **Set New Password**
   - Email will be pre-filled
   - Enter new password (minimum 8 characters)
   - Confirm password
   - Click "Reset Password"

9. **Login with New Password**
   - Go back to login page
   - Use the new password

---

## Understanding the Log File

When you open `storage/logs/laravel.log`, emails will look like this:

```
[2026-10-13 12:34:56] local.INFO: Message-ID: <abc123@127.0.0.1>
Date: Mon, 13 Oct 2026 12:34:56 +0000
Subject: Welcome to Project Tracker!
From: Project Tracker <noreply@projecttracker.com>
To: testuser@example.com

Hello **Test User**,

Your account has been created successfully by an administrator.

## Your Login Credentials

**Email:** testuser@example.com
**Temporary Password:** `AbCd1234EfGh`

[Login to Your Account]

**Important:** Please change your password after your first login for security purposes.
```

---

## Quick Test Commands

### View Last 50 Lines of Log
```bash
cd C:\xampp\htdocs\ga_tracker\project-tracker
type storage\logs\laravel.log | more
```

### Clear Log File (Start Fresh)
```bash
cd C:\xampp\htdocs\ga_tracker\project-tracker
echo. > storage\logs\laravel.log
```

### Test Email Sending via Tinker
```bash
cd C:\xampp\htdocs\ga_tracker\project-tracker
php artisan tinker
```

Then run:
```php
use App\Models\User;
use App\Mail\WelcomeUserMail;
use Illuminate\Support\Facades\Mail;

$user = User::first();
Mail::to($user->email)->send(new WelcomeUserMail($user, 'TestPassword123'));
exit
```

Check the log file - you should see the email!

---

## Switching to Real Email Sending

When you're ready to send actual emails:

### Option 1: Gmail (Quick Setup)

1. **Create Gmail App Password**
   - Go to: https://myaccount.google.com/security
   - Enable 2-Step Verification
   - Go to: https://myaccount.google.com/apppasswords
   - Create password for "Mail" on "Windows Computer"
   - Copy the 16-character password

2. **Update `.env`**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Project Tracker"
```

3. **Test Again**
   - Create a user with YOUR real email
   - You should receive the actual email!

---

## Troubleshooting

### Issue: "User created but email could not be sent"

**Cause:** Email configuration is wrong or service is unavailable

**Solution:**
1. Check `.env` MAIL settings
2. For testing, use `MAIL_MAILER=log`
3. Check `storage/logs/laravel.log` for error details

### Issue: Can't find emails in log

**Cause:** Log file is too large or emails aren't being logged

**Solution:**
1. Clear the log: `echo. > storage\logs\laravel.log`
2. Try creating a user again
3. Immediately check the log file

### Issue: Forgot Password link doesn't work

**Cause:** Password reset token expired (valid for 60 minutes)

**Solution:**
1. Request a new password reset
2. Use the link within 60 minutes
3. Check `password_resets` table in database

---

## Email Templates

You can customize email templates here:

- **Welcome Email:** `resources/views/emails/welcome-user.blade.php`
- **Password Reset:** `resources/views/emails/password-reset.blade.php`
- **Forgot Password Notification:** Laravel's default (can be customized)

---

## What's Working Now

✅ **New User Creation**
- Generates random 12-character password
- Sends professional welcome email
- Includes login credentials and role information

✅ **Admin Password Reset**
- Key icon (🔑) in Users Management
- Generates new random password
- Sends password reset notification

✅ **User Forgot Password**
- "Forgot your password?" link on login
- Branded reset request page
- Branded password reset form
- Secure token-based reset

✅ **Green Apple Branding**
- All email pages match your theme
- Professional gradient backgrounds
- Logo integration
- Mobile responsive

---

## Testing Checklist

- [ ] Create new user - check log for welcome email
- [ ] Extract temporary password from log
- [ ] Login as new user with temporary password
- [ ] Admin reset password - check log for reset email
- [ ] User forgot password - request reset
- [ ] Copy reset link from log
- [ ] Visit reset link and set new password
- [ ] Login with new password

---

## Need Help?

1. Check `storage/logs/laravel.log` for email content
2. Verify .env MAIL_MAILER is set to "log" for testing
3. Make sure you're logged in as Admin to create users
4. All email features log to the file even if they fail to send

---

## Production Deployment

Before going live:

1. Change `MAIL_MAILER=smtp` in .env
2. Configure real SMTP credentials (Gmail, SendGrid, etc.)
3. Update `APP_URL` to production domain
4. Test with real email addresses
5. Monitor bounce rates and deliverability

See `EMAIL_SETUP_GUIDE.md` for detailed SMTP configuration instructions.

---

**Current Status:** ✅ All email features are functional and logging to file for testing!
