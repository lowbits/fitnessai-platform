# Calorie Calculation Update Notification

## Overview
This feature allows sending email notifications to users informing them about updates to the calorie calculation method.

## Files Created

### 1. Notification Class
**File**: `app/Notifications/CalorieCalculationUpdate.php`

A new notification that sends an email to users explaining the update to the calorie calculation.

### 2. Artisan Command
**File**: `app/Console/Commands/SendCalorieUpdateNotification.php`

A command to send the notification to users who have set a password.

### 3. Translations
**Files**: 
- `lang/en/emails.php`
- `lang/de/emails.php`

Added `calorie_update` translation keys for both English and German.

## Usage

### Send to a specific user by email
```bash
php artisan notification:send-calorie-update --email=user@example.com
```

### Send to a specific user by ID
```bash
php artisan notification:send-calorie-update --user-id=123
```

### Send to all users with password (actual send)
```bash
php artisan notification:send-calorie-update --all-with-password
```

### Dry run (preview without sending)
```bash
php artisan notification:send-calorie-update --all-with-password --dry-run
```

## Email Content

### English Version
- **Subject**: "Update to your calorie calculation"
- **Content**: Explains the improvement to the calorie calculation, what changed, how it affects current meal plans, and why users are being informed.

### German Version
- **Subject**: "Update zu deiner Kalorienberechnung"
- **Content**: Same structure as English, translated to German.

## Target Audience
This notification is designed for users who have:
- Set a password (not null or empty)
- Verified their email address

## Queue Support
The notification implements `ShouldQueue`, so it will be processed asynchronously through the queue system.

## Notes
- The notification respects each user's locale setting (English or German)
- Uses Laravel's MailMessage for clean, consistent email formatting
- Can be sent in batch to all eligible users or targeted to specific users
- Includes dry-run mode for testing before actual sending
