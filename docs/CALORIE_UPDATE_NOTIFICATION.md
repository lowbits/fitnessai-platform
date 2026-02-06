# Calorie Calculation Update Notification

## Overview
This feature allows sending email notifications to users informing them about updates to the calorie calculation method. It uses Laravel best practices including cache-based deduplication to prevent sending duplicate emails.

## Files Created

### 1. Notification Class
**File**: `app/Notifications/CalorieCalculationUpdate.php`

A queued notification that sends an email to users explaining the update to the calorie calculation.

**Key Features:**
- Uses `preferredLocale()` for proper language detection
- Implements `ShouldQueue` for asynchronous processing
- Includes unique `CAMPAIGN_ID` constant for tracking
- Provides static `getCacheKey()` method for deduplication

### 2. Artisan Command
**File**: `app/Console/Commands/SendCalorieUpdateNotification.php`

A command to send the notification to users who have set a password.

**Key Features:**
- Cache-based deduplication (prevents duplicate sends)
- Rate limiting protection (0.5s delay between emails)
- Batch limiting for controlled sending
- Force resend option
- Comprehensive dry-run mode

### 3. Translations
**Files**: 
- `lang/en/emails.php`
- `lang/de/emails.php`

Added `calorie_update` translation keys for both English and German.

## Laravel Best Practices Implemented

✅ **Locale Handling**: Uses `preferredLocale()` from User model  
✅ **Queue Support**: Implements `ShouldQueue` for async processing  
✅ **Cache-based Deduplication**: Prevents duplicate sends using Laravel Cache  
✅ **Rate Limiting**: Built-in throttling to respect API limits  
✅ **Command Options**: Comprehensive CLI options for flexibility  
✅ **Error Handling**: Proper exception handling and reporting  
✅ **Dry-Run Mode**: Safe testing before actual sending  

## Deduplication System

The notification uses Laravel Cache to track which users have already received the email:

- **Cache Key Format**: `notification:sent:calorie_update_2026_02:user:{user_id}`
- **Cache Duration**: 1 year (prevents re-sending)
- **Override**: Use `--force` flag to resend to already-notified users

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

### Send to a limited number of users (e.g., for testing)
```bash
php artisan notification:send-calorie-update --all-with-password --limit=10
```

### Force resend to users who already received it
```bash
php artisan notification:send-calorie-update --all-with-password --force
```

### Dry run (preview without sending)
```bash
php artisan notification:send-calorie-update --all-with-password --dry-run
```

### Combine options (limit + dry-run)
```bash
php artisan notification:send-calorie-update --all-with-password --limit=5 --dry-run
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

## Rate Limit Protection
To prevent hitting email service rate limits (2 requests per second), the command automatically:
- Adds a 0.5-second delay between each queued notification
- Staggers the sending so emails are sent at a safe rate
- Shows progress with delay information when sending

For example, sending to 35 users will take approximately 17.5 seconds to queue all notifications with proper spacing.

## Best Practices

### Before Sending to All Users
1. **Test with a single user first:**
   ```bash
   php artisan notification:send-calorie-update --email=your-test-email@example.com
   ```

2. **Test with a small batch:**
   ```bash
   php artisan notification:send-calorie-update --all-with-password --limit=5
   ```

3. **Use dry-run to preview (shows who already received it):**
   ```bash
   php artisan notification:send-calorie-update --all-with-password --dry-run
   ```

4. **Send to all users:**
   ```bash
   php artisan notification:send-calorie-update --all-with-password
   ```

### Handling Duplicates
- **First run**: All eligible users will receive the email
- **Second run**: Users who already received it will be automatically skipped
- **Force resend**: Use `--force` flag to override deduplication (use with caution!)

### Monitoring
- The command shows real-time progress with delay information
- Each notification is queued with incremental delays (0s, 0.5s, 1s, 1.5s, etc.)
- Status indicators:
  - `✓` Successfully queued
  - `⊘` Skipped (already sent)
  - `✗` Error occurred
- Dry-run mode shows `(new)` or `(already sent)` status
- Check your queue worker logs to ensure notifications are being processed
- Monitor error counts in the command output

### Changing the Campaign
If you need to send a new version of this email in the future:
1. Update the `CAMPAIGN_ID` constant in `CalorieCalculationUpdate.php` (e.g., `calorie_update_2026_03`)
2. This creates a new cache namespace, allowing you to send to all users again

## Notes
- The notification respects each user's locale setting (English or German) using `preferredLocale()`
- Uses Laravel's MailMessage for clean, consistent email formatting
- Can be sent in batch to all eligible users or targeted to specific users
- Includes dry-run mode for testing before actual sending
- **Automatic deduplication**: Users cannot receive the same campaign email twice (unless forced)
- Cache-based tracking: No database overhead, fast lookups
- Cache entries expire after 1 year (can be adjusted in the code)

## Technical Details

### Cache Key Format
```
notification:sent:{CAMPAIGN_ID}:user:{user_id}
```

Example: `notification:sent:calorie_update_2026_02:user:123`

### Campaign ID
Current campaign: `calorie_update_2026_02`

To create a new campaign (e.g., for future calorie updates), simply change the `CAMPAIGN_ID` constant in the notification class.

## Troubleshooting

### User receives duplicate emails
- Check if `--force` flag was used
- Verify cache is working: `php artisan cache:clear` then check again
- Confirm the `CAMPAIGN_ID` hasn't been changed

### Emails not being sent
- Ensure queue workers are running: `php artisan queue:work`
- Check queue failed jobs: `php artisan queue:failed`
- Verify email configuration in `.env`

### Want to reset and resend to everyone
- Option 1: Use `--force` flag
- Option 2: Change the `CAMPAIGN_ID` in the notification class
- Option 3: Clear specific cache keys (for testing only)
