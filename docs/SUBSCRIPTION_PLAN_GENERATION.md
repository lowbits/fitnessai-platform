# Beta Subscription & Continuous Plan Generation - Implementation Plan

## Requirements

1. ✅ **Subscription System** 
   - Beta subscriptions for early users
   - Command to create subscriptions: `php artisan subscription:create {email}`

2. **Plan Generation Logic**
   - Only for users with: Password + Active Subscription
   - Generate plans in 7-day cycles
   - Start mid-week (Wednesday/Thursday)
   - Plan duration: 1 month (renewable)

3. **Continuous Generation**
   - Check mid-week: Generate next 7 days
   - For existing users: Catch up and add next 7 days
   - After 1 month: Start new plan based on goals achieved

## Database Schema

### Subscriptions Table
```sql
CREATE TABLE subscriptions (
    id BIGINT PRIMARY KEY,
    user_id BIGINT (FK to users),
    type VARCHAR(255) DEFAULT 'beta',
    status VARCHAR(255) DEFAULT 'active',
    starts_at TIMESTAMP,
    ends_at TIMESTAMP NULL,
    cancelled_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## Implementation Steps

### ✅ Phase 1: Subscription System (DONE)
- [x] Migration for subscriptions
- [x] Subscription model with `isActive()` method
- [x] User has One subscription (latestOfMany)
- [x] User `hasActiveSubscription()` method
- [x] Command: `subscription:create {email}`
- [x] **Plan Duration Update**: Extends existing 7-day plans to 30 days

### Phase 2: Onboarding Changes
- [ ] Update OnboardingController
  - If user has password + subscription → trigger plan generation
  - If user has password but no subscription → wait for subscription
  - If user has no password → send email verification (existing)

### Phase 3: Plan Generation Service
- [ ] Create `PlanGenerationService`
  - Check if mid-week (Wednesday/Thursday)
  - Generate next 7 days of workouts + meals
  - Handle edge cases (existing plans, gaps)

### Phase 4: Scheduled Command
- [ ] Command: `plans:generate-weekly`
  - Runs mid-week
  - For all users with active subscription
  - Generates next 7 days

### Phase 5: Plan Renewal
- [ ] After 1 month: Create new plan
  - Based on user progress/goals
  - Reset cycle

## Usage Examples

### Create Beta Subscription
```bash
# Create 1-month beta subscription
php artisan subscription:create user@example.com

# Create 3-month subscription
php artisan subscription:create user@example.com --months=3

# Create premium subscription
php artisan subscription:create user@example.com --type=premium
```

**Example Output:**
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

### Check User Subscription
```php
$user = User::find(1);

if ($user->hasActiveSubscription()) {
    // Generate plans
}
```

### Onboarding Flow

#### Scenario 1: User with Password + Subscription
```
1. User completes onboarding with password
2. Check: hasActiveSubscription() → true
3. → Trigger immediate plan generation
4. → Generate first 7 days
```

#### Scenario 2: User with Password, No Subscription
```
1. User completes onboarding with password
2. Check: hasActiveSubscription() → false
3. → Send email: "Activate subscription to start"
4. Admin creates subscription
5. → Trigger plan generation
```

#### Scenario 3: User without Password (existing)
```
1. User completes onboarding without password
2. → Send email verification
3. User verifies email
4. User sets password
5. Check subscription → generate plans
```

## Plan Generation Timing

### Mid-Week Check (Wednesday 00:00)
```
Current Week: Mon | Tue | Wed | Thu | Fri | Sat | Sun
Action: Generate → Thu | Fri | Sat | Sun | Mon | Tue | Wed
```

### For New Users (any day)
```
User subscribes on Friday
→ Generate next 7 days starting Saturday
```

## Testing Strategy

### Test 1: Create Subscription
```php
test('can create beta subscription for user', function () {
    $user = User::factory()->create();
    
    $this->artisan('subscription:create', ['email' => $user->email])
        ->assertSuccessful();
    
    expect($user->fresh()->hasActiveSubscription())->toBeTrue();
});
```

### Test 2: Onboarding with Subscription
```php
test('generates plans for user with password and subscription', function () {
    $user = User::factory()->create();
    Subscription::create([
        'user_id' => $user->id,
        'type' => 'beta',
        'status' => 'active',
        'starts_at' => now(),
        'ends_at' => now()->addMonth(),
    ]);
    
    // Onboarding request...
    // Assert: Plans are generated
});
```

### Test 3: Weekly Generation
```php
test('generates next 7 days mid-week for subscribed users', function () {
    // Set current date to Wednesday
    // Create user with subscription + existing 7-day plan
    // Run command
    // Assert: Next 7 days are generated
});
```

## Configuration

### Plan Duration (config/plans.php)
```php
return [
    'duration_days' => 30, // 1 month for subscribed users
    'generation_cycle_days' => 7, // Generate in 7-day cycles
    'mid_week_day' => 3, // Wednesday (1=Monday, 7=Sunday)
];
```

## Commands Summary

```bash
# Create subscription
php artisan subscription:create user@example.com

# Generate weekly plans (scheduled)
php artisan plans:generate-weekly

# Check user subscription status
php artisan subscription:check user@example.com
```

## Next Steps

1. Implement `PlanGenerationService` with tests
2. Update `OnboardingController` with subscription check
3. Create `plans:generate-weekly` command
4. Add to schedule (Wednesday 00:00)
5. Write integration tests

---

**This is a complete system for beta subscriptions and continuous plan generation!** 🚀

