# Subscription System - Quick Reference

## ✅ Was wurde implementiert

### 1. Subscription Creation Command
```bash
php artisan subscription:create user@example.com
```

**Features:**
- Creates beta subscription (default 1 month)
- Automatically extends existing 7-day plan to 30 days
- Options: `--type=beta|premium` and `--months=1|3|6|12`

### 2. Automatic Plan Update

When you add a subscription, the command:
1. ✅ Creates the subscription
2. ✅ Finds user's active plan
3. ✅ Updates `duration_days` from 7 to 30 (or 30 * months)
4. ✅ Updates `end_date` accordingly
5. ✅ Shows before/after comparison

### 3. Database Models

**Subscription Model:**
```php
$subscription->isActive(); // bool
$subscription->user; // BelongsTo User
```

**User Model:**
```php
$user->subscription; // HasOne (latest)
$user->subscriptions; // HasMany
$user->hasActiveSubscription(); // bool
```

## Usage Examples

### Example 1: Add 1-month subscription
```bash
php artisan subscription:create john@example.com
```

**Result:**
- Subscription: Jan 3 → Feb 3
- Plan updated: 7 days → 30 days
- End date: Jan 10 → Feb 2

### Example 2: Add 3-month subscription
```bash
php artisan subscription:create jane@example.com --months=3
```

**Result:**
- Subscription: Jan 3 → Apr 3
- Plan updated: 7 days → 90 days
- End date: Jan 10 → Apr 3

### Example 3: User without plan
```bash
php artisan subscription:create newuser@example.com
```

**Result:**
- Subscription created
- Warning: "⚠️  No active plan found for this user"
- Plan will be created when they complete onboarding

## Tests (TDD)

### Test 1: Basic subscription creation
```bash
php artisan test --filter="can create beta subscription for user"
```

### Test 2: Plan extension (7 → 30 days)
```bash
php artisan test --filter="subscription extends existing plan from 7 to 30 days"
```

### Test 3: Multiple months
```bash
php artisan test --filter="subscription with multiple months extends plan accordingly"
```

### Test 4: No active plan
```bash
php artisan test --filter="handles user without active plan gracefully"
```

### Test 5: Only active plans updated
```bash
php artisan test --filter="subscription updates only active plan not inactive ones"
```

## Command Output Format

```
✅ Subscription created successfully!

📋 Updating existing plan...
+----------+-----------+-----------+
| Field    | Old Value | New Value |
+----------+-----------+-----------+
| Duration | 7 days    | 30 days   |
| End Date | 2026-01-10| 2026-02-02|
+----------+-----------+-----------+

✅ Plan updated from 7 days to 30 days

+-------------------+------------------------+
| Field             | Value                  |
+-------------------+------------------------+
| User              | user@example.com       |
| Subscription Type | beta                   |
| Status            | active                 |
| Starts At         | 2026-01-03 12:00:00   |
| Ends At           | 2026-02-03 12:00:00   |
| Duration          | 1 month(s)             |
+-------------------+------------------------+
```

## Code Examples

### Check if user has subscription
```php
if ($user->hasActiveSubscription()) {
    // User can generate plans
    $plan = $user->plans()->where('status', 'active')->first();
    echo "Plan runs until: " . $plan->end_date->format('Y-m-d');
}
```

### Get subscription details
```php
$subscription = $user->subscription;

if ($subscription && $subscription->isActive()) {
    echo "Type: " . $subscription->type;
    echo "Ends: " . $subscription->ends_at->format('Y-m-d');
}
```

### Create subscription programmatically
```php
use App\Models\Subscription;

$subscription = Subscription::create([
    'user_id' => $user->id,
    'type' => 'beta',
    'status' => 'active',
    'starts_at' => now(),
    'ends_at' => now()->addMonth(),
]);

// Then manually update plan if needed
$plan = $user->plans()->where('status', 'active')->first();
if ($plan) {
    $plan->update([
        'duration_days' => 30,
        'end_date' => $plan->start_date->copy()->addDays(30),
    ]);
}
```

## What's Next?

After creating subscriptions for beta users:

1. ✅ **Phase 1 DONE**: Subscription system with plan duration update
2. ✅ **Phase 2 DONE**: Weekly plan generation command
3. **Phase 3**: Update OnboardingController to check subscription before plan generation
4. **Phase 4**: Plan renewal after 1 month based on progress
5. **Phase 5**: User notifications for new weekly plans

## Commands Summary

```bash
# Create subscription
php artisan subscription:create user@example.com

# Generate weekly plans (runs every Wednesday automatically)
php artisan plans:generate-weekly

# Force generate on any day
php artisan plans:generate-weekly --force
```

## Weekly Generation Details

The `plans:generate-weekly` command:
- Runs every Wednesday at 00:00 (mid-week)
- Finds users with active subscriptions
- Generates next 7 days if user has < 7 days of future plans
- Respects plan end dates
- Queues jobs for async processing

**See:** `docs/WEEKLY_PLAN_GENERATION_COMPLETE.md` for full details

## Migration

Run migration to create subscriptions table:
```bash
php artisan migrate
```

## Testing

Run all subscription tests:
```bash
php artisan test tests/Feature/OnboardingWithSubscriptionTest.php
```

---

**The subscription system is ready for beta users!** 🚀

