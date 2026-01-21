# RevenueCat Renewal Webhook Implementation

## Overview
This document describes the implementation of the RevenueCat `RENEWAL` webhook event handler.

## Components Created

### 1. HandleRenewalAction
**File:** `app/Actions/RevenueCat/HandleRenewalAction.php`

**Purpose:** Processes renewal webhook events from RevenueCat.

**Functionality:**
- Finds the user based on `app_user_id`
- Updates the subscription with new period dates
- Dispatches `SubscriptionRenewed` event
- Logs renewal processing

**Key Methods:**
- `execute(array $payload)` - Main entry point
- `findUser(array $event)` - Finds user by app_user_id
- `syncSubscription(User $user, array $event)` - Updates subscription dates
- `extractSubscriptionData(array $event)` - Extracts subscription data from webhook payload
- `parseMilliseconds(?int $milliseconds)` - Converts millisecond timestamps to Carbon dates

### 2. SubscriptionRenewed Event
**File:** `app/Events/RevenueCat/SubscriptionRenewed.php`

**Purpose:** Event dispatched when a subscription is renewed.

**Properties:**
- `User $user` - The user whose subscription was renewed
- `array $eventData` - The webhook event data from RevenueCat

### 3. AdjustPlanAfterRenewal Listener
**File:** `app/Listeners/AdjustPlanAfterRenewal.php`

**Purpose:** Listens to `SubscriptionRenewed` event and adjusts the user's active plan.

**Functionality:**
- Calls `AdjustActivePlanAction` to update plan duration based on subscription
- Runs as a queued job (`ShouldQueue`)

### 4. Updated HandleRevenueCatWebhook Listener
**File:** `app/Listeners/HandleRevenueCatWebhook.php`

**Changes:**
- Added `HandleRenewalAction` injection in constructor
- Updated `handleRenewal()` method to execute the renewal action

### 5. AppServiceProvider Registration
**File:** `app/Providers/AppServiceProvider.php`

**Changes:**
- Registered `SubscriptionRenewed` event with `AdjustPlanAfterRenewal` listener

## Webhook Flow

```
RevenueCat RENEWAL Webhook
    ↓
HandleRevenueCatWebhook::handle()
    ↓
HandleRevenueCatWebhook::handleRenewal()
    ↓
HandleRenewalAction::execute()
    ↓
1. Find User
2. Update Subscription (dates, status)
3. Dispatch SubscriptionRenewed Event
    ↓
AdjustPlanAfterRenewal::handle()
    ↓
AdjustActivePlanAction::execute()
    ↓
Update Plan Duration & End Date
```

## Webhook Payload

The renewal webhook contains:

```json
{
  "event": {
    "type": "RENEWAL",
    "app_user_id": "user_id",
    "product_id": "monthly_premium",
    "period_start_at_ms": 1234567890000,
    "expiration_at_ms": 1234567890000,
    "purchased_at_ms": 1234567890000,
    "price_in_purchased_currency": 9.99,
    "currency": "EUR",
    "store": "APP_STORE",
    ...
  }
}
```

## What Happens on Renewal

1. **Subscription Update:**
   - `current_period_started_at` is updated to new period start
   - `current_period_ended_at` is updated to new expiration date
   - `status` is set to `ACTIVE`
   - Price and currency are updated

2. **Plan Adjustment:**
   - The user's active plan duration is extended
   - Plan end date is updated based on subscription period
   - This ensures users have continuous access to meal/workout plans

3. **Logging:**
   - All renewal events are logged with user_id, product_id, and new expiration date
   - Errors (e.g., user not found) are logged with ERROR level

## Testing

To test the renewal webhook:

1. Use RevenueCat's webhook testing tool or send a test webhook:
```bash
POST /api/webhook/revenuecat
```

2. Check logs for:
```
"RevenueCat webhook received" with type: "RENEWAL"
"Subscription renewal processed successfully"
"Plan adjusted after renewal"
```

3. Verify in database:
   - `subscriptions` table has updated `current_period_ended_at`
   - `plans` table has updated `end_date`

## Related Files

- `app/Actions/RevenueCat/AdjustActivePlanAction.php` - Adjusts plan duration
- `app/Actions/RevenueCat/HandleInitialPurchaseAction.php` - Similar flow for initial purchases
- `app/Listeners/AdjustPlanAfterPurchase.php` - Adjusts plan after initial purchase
- `vendor/noopstudios/laravel-revenuecat` - RevenueCat package

## Notes

- Renewal handling is similar to initial purchase but only updates subscription dates
- If subscription doesn't exist, it will be created (handles edge cases)
- Runs in queue for better performance
- All timestamps are in milliseconds from RevenueCat and converted to Carbon dates

