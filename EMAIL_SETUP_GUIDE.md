# Email Setup Guide for Project Tracker

This guide explains how to configure email sending functionality for the Project Tracker application.

## Email Features

The application now sends emails for:
1. **Welcome emails** - When a new user is created, they receive their login credentials
2. **Password reset emails** - When an admin resets a user's password

## Configuration Steps

### 1. Choose an Email Service

You have several options:

#### Option A: Gmail (Recommended for testing/small teams)
#### Option B: Mailtrap (Recommended for development/testing)
#### Option C: SendGrid (Recommended for production)
#### Option D: Amazon SES (Enterprise solution)

---

## Option A: Gmail Setup

### Step 1: Enable 2-Step Verification
1. Go to your Google Account: https://myaccount.google.com/
2. Click on "Security" in the left menu
3. Enable "2-Step Verification"

### Step 2: Create App Password
1. Still in Security, click "2-Step Verification"
2. Scroll to bottom and click "App passwords"
3. Select "Mail" and "Windows Computer" (or Other)
4. Click "Generate"
5. Copy the 16-character password

### Step 3: Update .env File
Open `C:\xampp\htdocs\ga_tracker\project-tracker\.env` and update:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Important Notes:**
- Use the 16-character app password, NOT your regular Gmail password
- Gmail has a daily sending limit of 500 emails per day for free accounts

---

## Option B: Mailtrap Setup (Best for Testing)

Mailtrap captures all emails in a test inbox - perfect for development.

### Step 1: Create Mailtrap Account
1. Go to https://mailtrap.io/
2. Sign up for a free account
3. Click on "Email Testing" → "Inboxes"
4. Click on your inbox

### Step 2: Get SMTP Credentials
1. In your inbox, click "SMTP Settings"
2. Copy the credentials shown

### Step 3: Update .env File
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@projecttracker.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Advantages:**
- Free tier available
- All emails caught in test inbox
- View HTML and text versions
- Check spam score
- Perfect for development

---

## Option C: SendGrid Setup (Production)

SendGrid is reliable for production with better deliverability.

### Step 1: Create SendGrid Account
1. Go to https://sendgrid.com/
2. Sign up (free tier: 100 emails/day)
3. Verify your email address

### Step 2: Create API Key
1. Go to Settings → API Keys
2. Click "Create API Key"
3. Choose "Full Access"
4. Copy the API key (you won't see it again!)

### Step 3: Update .env File
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=verified-email@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Important:**
- MAIL_USERNAME must be exactly "apikey"
- You must verify your sender email in SendGrid
- Free tier: 100 emails/day

---

## Option D: Amazon SES Setup (Enterprise)

For high-volume email sending with excellent deliverability.

### Step 1: AWS Account Setup
1. Create AWS account at https://aws.amazon.com/
2. Go to Amazon SES console
3. Verify your domain or email address

### Step 2: Create SMTP Credentials
1. In SES console, go to "SMTP Settings"
2. Click "Create My SMTP Credentials"
3. Download the credentials

### Step 3: Update .env File
```env
MAIL_MAILER=smtp
MAIL_HOST=email-smtp.us-east-1.amazonaws.com
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=verified@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Notes:**
- Start in sandbox mode (verify recipients)
- Request production access for unrestricted sending
- Very cost-effective for high volume

---

## Testing Email Configuration

### Method 1: Create a Test User
1. Log in as admin
2. Go to Users Management
3. Click "Add New User"
4. Fill in details with a real email you can check
5. Submit - check if welcome email arrives

### Method 2: Reset Password
1. Go to Users Management
2. Click the key icon (🔑) next to any user
3. Confirm the reset
4. Check if password reset email arrives

### Method 3: Test via Tinker
```bash
cd C:\xampp\htdocs\ga_tracker\project-tracker
php artisan tinker
```

Then run:
```php
Mail::raw('Test email from Project Tracker', function($message) {
    $message->to('your-email@example.com')
            ->subject('Test Email');
});
```

If successful, you'll see no errors. Check your inbox!

---

## Troubleshooting

### Error: "Connection could not be established with host"

**Solution:**
1. Check MAIL_HOST is correct
2. Verify MAIL_PORT (587 for TLS, 465 for SSL)
3. Check your firewall isn't blocking outbound SMTP
4. For Gmail, ensure app password is correct

### Error: "Authentication failed"

**Solution:**
1. Verify MAIL_USERNAME is correct
2. For Gmail, use app password not regular password
3. For SendGrid, ensure username is "apikey"

### Emails not arriving (but no errors)

**Solution:**
1. Check spam/junk folder
2. Verify MAIL_FROM_ADDRESS is verified with your provider
3. For production, verify your domain's SPF/DKIM records

### Gmail "Less secure apps" error

**Solution:**
- Don't use "less secure apps" option
- Use App Passwords (2-Step Verification required)
- This is the proper secure way

---

## Current Email Status

After setup, the system will:

✅ **When creating a user:**
- Generate random 12-character password
- Send welcome email with credentials
- Show success message with email address

✅ **When resetting password:**
- Generate new random 12-character password
- Send password reset email
- Show success message

⚠️ **If email fails:**
- User is still created/updated successfully
- Admin sees warning message with the password
- Admin must manually send password to user

---

## Email Templates Location

You can customize email templates here:
- Welcome email: `resources/views/emails/welcome-user.blade.php`
- Password reset: `resources/views/emails/password-reset.blade.php`

Templates use Laravel Markdown Mail format for responsive, professional emails.

---

## Security Best Practices

1. **Never commit .env file** - Already in .gitignore
2. **Use app passwords** - Not regular account passwords
3. **Verify sender domains** - Improves deliverability
4. **Monitor sending limits** - Don't exceed provider limits
5. **Use HTTPS** - Login links in emails should use HTTPS

---

## Production Checklist

Before going live:

- [ ] Switch from Mailtrap to production email service
- [ ] Verify sender domain in email provider
- [ ] Set up SPF and DKIM records for domain
- [ ] Update APP_URL in .env to production domain (https://)
- [ ] Test email delivery to different providers (Gmail, Outlook, etc.)
- [ ] Monitor email bounce rates
- [ ] Set up email logging/tracking if needed

---

## Support

If you continue having issues:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Enable mail logging in .env: `LOG_CHANNEL=stack`
3. Test with Mailtrap first (easiest to debug)
4. Check provider documentation
5. Verify all credentials are correct

---

## Quick Reference

**Current Email Features:**
- ✅ User creation welcome email
- ✅ Admin password reset
- ✅ HTML formatted emails
- ✅ Graceful fallback if email fails
- ✅ Role information in welcome email

**Email Content Includes:**
- User's name and email
- Temporary password
- Login button
- Security reminder
- Role assignments (welcome email)

---

## Example: Gmail Setup (Step by Step)

1. Open `.env` file
2. Find the MAIL section
3. Replace these lines:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=projecttracker@gmail.com
MAIL_PASSWORD=abcd efgh ijkl mnop
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=projecttracker@gmail.com
MAIL_FROM_NAME="Project Tracker"
```
4. Save file
5. No need to restart - Laravel picks up changes
6. Test by creating a user

That's it! 🎉
