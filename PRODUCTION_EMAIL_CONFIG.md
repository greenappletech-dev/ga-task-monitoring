# Production Email Configuration Guide

## ✅ EMAIL IS WORKING!

Your Gmail SMTP configuration has been tested and is working perfectly!

---

## Production Server Configuration

### Update Your Production .env File

On your production server at `gatsi-task-monitoring.greenappletechph.com`, update the `.env` file with these email settings:

```env
# Gmail SMTP Configuration (TESTED AND WORKING!)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=gatsi.ai02@gmail.com
MAIL_PASSWORD=ajqqvneekojmdnpp
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=gatsi.ai02@gmail.com
MAIL_FROM_NAME="Project Tracker"
```

### Important Changes from Your Current Config:

**BEFORE (Not Working):**
```env
MAIL_HOST=mailhog          ❌ Wrong - mailhog is for local testing only
MAIL_PORT=1025             ❌ Wrong - this is mailhog's port
```

**AFTER (Working):**
```env
MAIL_HOST=smtp.gmail.com   ✅ Correct - Gmail's SMTP server
MAIL_PORT=587              ✅ Correct - Gmail's TLS port
```

---

## Deployment Steps

### 1. SSH to Your Production Server

```bash
ssh forge@gatsi-task-monitoring.greenappletechph.com
```

### 2. Navigate to Your Project

```bash
cd /home/forge/gatsi-task-monitoring.greenappletechph.com
```

### 3. Backup Current .env

```bash
cp .env .env.backup
```

### 4. Edit .env File

```bash
nano .env
```

Find these lines:
```env
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
```

Change to:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
```

**Note:** Keep your existing `MAIL_USERNAME` and `MAIL_PASSWORD` - they are correct!

Press `Ctrl+O` to save, then `Ctrl+X` to exit.

### 5. Clear Laravel Cache

```bash
php artisan config:clear
php artisan cache:clear
```

### 6. Test Email Sending

Option A - Quick test via tinker:
```bash
php artisan tinker
```

Then run:
```php
Mail::raw('Test email from production', function($message) {
    $message->to('gatsi.ai02@gmail.com')
            ->subject('Production Email Test');
});
exit
```

Check your email inbox!

Option B - Test by creating a user:
1. Go to: http://gatsi-task-monitoring.greenappletechph.com/admin/users
2. Create a new test user
3. You should see: ✅ User created successfully! Welcome email with login credentials has been sent to {email}
4. Check the user's email inbox for the welcome email!

---

## What's Different Now

### ✅ Enhanced Notifications

**When Creating a User:**
- Success: "✅ User created successfully! Welcome email with login credentials has been sent to user@example.com"
- Warning (if email fails): "⚠️ User created successfully, but email could not be sent. Temporary password: **AbCd1234**"

**When Resetting Password:**
- Success: "✅ Password reset successfully! Email with new login credentials has been sent to user@example.com"
- Warning (if email fails): "⚠️ Password reset successfully, but email could not be sent. New password: **AbCd1234**"

### ✅ Logging Added

All email activities are now logged:
- Success: Logs to `storage/logs/laravel.log` with info level
- Failure: Logs to `storage/logs/laravel.log` with error level and full error message

### ✅ Warning Messages Support HTML

If email fails, the temporary password is displayed in bold with proper formatting.

---

## Email Features Working

1. **Welcome Email** - Sent when admin creates new user
   - Contains login credentials
   - Professional Green Apple branding
   - Lists assigned roles
   - Login button link

2. **Admin Password Reset** - Key icon (🔑) in Users Management
   - Generates new random password
   - Sends professional email
   - Shows success notification

3. **Forgot Password** - User self-service
   - "Forgot your password?" link on login
   - Green Apple branded form
   - Secure token-based reset
   - 60-minute expiry

---

## Monitoring Email Sending

### Check Logs

```bash
tail -f storage/logs/laravel.log | grep -i "email\|mail"
```

You'll see:
- `INFO: Welcome email sent successfully to user@example.com`
- `ERROR: Failed to send welcome email to user@example.com: Connection refused`

### Gmail Sending Limits

Free Gmail accounts have limits:
- **500 emails per day** (rolling 24-hour period)
- If you exceed this, Gmail will temporarily block sending
- For higher volume, consider:
  - Google Workspace (2000 emails/day)
  - SendGrid (100 emails/day free, up to 100k/month paid)
  - Amazon SES (62,000 emails/month free first year)

---

## Troubleshooting

### Error: "Failed to authenticate on SMTP server"

**Cause:** Gmail credentials are incorrect or app password expired

**Solution:**
1. Verify `MAIL_USERNAME` is exactly: `gatsi.ai02@gmail.com`
2. Verify `MAIL_PASSWORD` is exactly: `ajqqvneekojmdnpp`
3. If still failing, generate new app password at: https://myaccount.google.com/apppasswords

### Error: "Connection could not be established with host smtp.gmail.com"

**Cause:** Firewall blocking outbound SMTP or incorrect port

**Solution:**
1. Check firewall allows outbound port 587
2. Try alternative port 465 with SSL:
   ```env
   MAIL_PORT=465
   MAIL_ENCRYPTION=ssl
   ```

### Emails Not Arriving

**Cause:** Emails might be in spam folder

**Solution:**
1. Check spam/junk folder
2. Add `gatsi.ai02@gmail.com` to contacts
3. For production, set up SPF/DKIM records for your domain

### "User created but email could not be sent"

**Cause:** Email configuration issue

**Solution:**
1. Check `storage/logs/laravel.log` for exact error
2. Run: `php artisan config:clear`
3. Verify `.env` settings are correct
4. Test with tinker (see Step 6 above)

---

## Security Notes

1. **App Password Used** ✅
   - Using Gmail app password (ajqqvneekojmdnpp)
   - More secure than regular password
   - Can be revoked without affecting Gmail login

2. **TLS Encryption** ✅
   - All emails sent over encrypted connection
   - `MAIL_ENCRYPTION=tls` ensures security

3. **Production Mode** ✅
   - `APP_ENV=production`
   - `APP_DEBUG=false` (errors not shown to users)

---

## Complete .env Email Section

Copy this entire section to your production server:

```env
# Email Configuration - Gmail SMTP
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=gatsi.ai02@gmail.com
MAIL_PASSWORD=ajqqvneekojmdnpp
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=gatsi.ai02@gmail.com
MAIL_FROM_NAME="Project Tracker"
```

---

## Files Modified

These files have been updated with email functionality:

1. `app/Http/Controllers/Admin/UserController.php` - Enhanced notifications
2. `resources/views/layouts/admin.blade.php` - Added warning message support
3. `app/Mail/WelcomeUserMail.php` - Welcome email handler
4. `app/Mail/PasswordResetMail.php` - Password reset email handler
5. `resources/views/emails/welcome-user.blade.php` - Welcome email template
6. `resources/views/emails/password-reset.blade.php` - Password reset template
7. `resources/views/auth/passwords/email.blade.php` - Forgot password page
8. `resources/views/auth/passwords/reset.blade.php` - Reset password page
9. `resources/views/admin/users/index.blade.php` - Added reset password button
10. `routes/web.php` - Added password reset route

---

## Quick Deployment Checklist

- [ ] SSH to production server
- [ ] Backup current .env file
- [ ] Update MAIL_HOST to `smtp.gmail.com`
- [ ] Update MAIL_PORT to `587`
- [ ] Run `php artisan config:clear`
- [ ] Run `php artisan cache:clear`
- [ ] Test email sending (tinker or create user)
- [ ] Check inbox for test email
- [ ] Monitor `storage/logs/laravel.log`

---

## Support

If issues persist after deployment:

1. Check Laravel logs: `tail -50 storage/logs/laravel.log`
2. Test SMTP connection: `telnet smtp.gmail.com 587`
3. Verify Gmail credentials are correct
4. Check if Gmail blocked the sending (check email from Google)
5. Try regenerating app password if needed

---

**STATUS:** ✅ All email features tested and working locally!

Ready to deploy to production! 🚀
