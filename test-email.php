#!/usr/bin/env php
<?php

/*
 * Quick Email Test Script
 * This script tests email functionality without going through the UI
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Mail\WelcomeUserMail;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Mail;

echo "\n";
echo "========================================\n";
echo "  Project Tracker - Email Test Script  \n";
echo "========================================\n\n";

// Get the first user
$user = User::first();

if (!$user) {
    echo "❌ No users found in database.\n";
    echo "   Please run: php artisan db:seed\n\n";
    exit(1);
}

echo "✅ Found user: {$user->name} ({$user->email})\n\n";

// Test 1: Welcome Email
echo "Test 1: Sending Welcome Email...\n";
echo "-----------------------------------\n";
try {
    $testPassword = 'TestPass123!';
    Mail::to($user->email)->send(new WelcomeUserMail($user, $testPassword));
    echo "✅ Welcome email sent successfully!\n";
    echo "   Password used: {$testPassword}\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Password Reset Email
echo "Test 2: Sending Password Reset Email...\n";
echo "-----------------------------------\n";
try {
    $newPassword = 'NewPass456!';
    Mail::to($user->email)->send(new PasswordResetMail($user, $newPassword));
    echo "✅ Password reset email sent successfully!\n";
    echo "   New password: {$newPassword}\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Check mail configuration
echo "Email Configuration:\n";
echo "-----------------------------------\n";
echo "MAIL_MAILER: " . config('mail.default') . "\n";
echo "MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
echo "MAIL_FROM: " . config('mail.from.address') . "\n";

echo "\n";

if (config('mail.default') === 'log') {
    echo "📝 Emails are being logged to:\n";
    echo "   " . storage_path('logs/laravel.log') . "\n\n";
    echo "ℹ️  To view emails, open the log file and search for:\n";
    echo "   - 'Welcome to Project Tracker'\n";
    echo "   - 'Password Reset'\n\n";
} else {
    echo "📧 Emails are being sent via SMTP\n";
    echo "   Check the recipient's inbox!\n\n";
}

echo "========================================\n";
echo "  Test Complete!                       \n";
echo "========================================\n\n";
