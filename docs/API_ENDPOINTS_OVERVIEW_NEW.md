# API Endpoints Overview

Complete overview of all API endpoints in the Fitness AI application.

## Base URL
- **Production**: `https://fitness-ai.me/api/v2`
- **Local Development**: `http://localhost/api/v2`

---

## Public Endpoints

### Onboarding
- `POST /onboarding` - Create user profile and generate initial plan
  - Rate limit: 3 requests per minute
  - See: [MOBILE_API.md](./MOBILE_API.md)

### Authentication

#### Set Password (Email-based)
- `POST /auth/set-password` - Set password using token from email
- `POST /auth/set-password/request` - Request password reset token
  - Rate limit: 5 requests per minute
  - See: [SET_PASSWORD_FLOW.md](./SET_PASSWORD_FLOW.md), [REACT_NATIVE_SET_PASSWORD.md](./REACT_NATIVE_SET_PASSWORD.md)

#### Login
- `POST /auth/login` - Login with email and password
  - Rate limit: 5 requests per minute
  - See: [SANCTUM_REACT_NATIVE_AUTH.md](./SANCTUM_REACT_NATIVE_AUTH.md)

---

## Authenticated Endpoints
**Authentication Required**: All endpoints below require Sanctum token in `Authorization: Bearer {token}` header.

### Authentication Management
- `GET /auth/me` - Get current user information
- `POST /auth/logout` - Logout current session
- `POST /auth/logout-all` - Logout all sessions
- `POST /auth/refresh-token` - Refresh authentication token

### Plan Endpoints
- `GET /plan/day/{date}` - Get meal and workout plan for specific date
  - Parameters: `date` (YYYY-MM-DD format)
  - Returns: Meals, workout, daily totals, status
  - See: [PLAN_API_ENDPOINTS.md](./PLAN_API_ENDPOINTS.md)

### Meal Endpoints
- `GET /meals/{mealId}` - Get detailed meal information
  - Returns: Full meal details with ingredients, instructions, nutrition
  - See: [PLAN_API_ENDPOINTS.md](./PLAN_API_ENDPOINTS.md)

### Workout Endpoints
- `GET /workouts/{workoutId}` - Get detailed workout information
  - Returns: Full workout with exercises, sets, reps, form cues
  - See: [PLAN_API_ENDPOINTS.md](./PLAN_API_ENDPOINTS.md)

---

## Web Routes (Inertia.js)

### Public Pages
- `GET /` - Homepage
- `GET /{locale}/impressum` (DE) - Imprint page
- `GET /{locale}/imprint` (EN) - Imprint page
- `GET /{locale}/workout-plans` - Workout plans overview
- `GET /{locale}/workout-plans/{type}` - Specific workout plan type

### Email Verification
- `GET /{locale}/verify-email/{id}/{hash}` - Verify email from link
  - Middleware: `signed`
  - See: [EMAIL_VERIFICATION_WORKFLOW.md](./EMAIL_VERIFICATION_WORKFLOW.md)

---

## Development-Only Endpoints

These endpoints are only available in local environment (`APP_ENV=local`):

- `GET /api/v2/me` - Mock user data endpoint for testing
  - Returns complete user object with profile, plan, subscription, stats

---

## Response Formats

### Success Response
```json
{
  "data": { ... },
  "message": "Optional success message"
}
```

### Error Response
```json
{
  "error": "Error type",
  "message": "Human-readable error message"
}
```

---

## Authentication Flow

1. **Onboarding**: `POST /onboarding`
   - Creates user, profile, and starts plan generation
   - Returns temporary access or email verification required

2. **Set Password**: Receive email with deep link
   - Mobile app opens: `fitnessai://set-password?email=...&token=...`
   - Submit password: `POST /auth/set-password`

3. **Login**: `POST /auth/login`
   - Returns Sanctum token
   - Use token for all authenticated requests

4. **Access Plan**: `GET /plan/day/{date}`
   - Get meals and workouts for specific day
   - Access meal/workout details via respective endpoints

---

## Rate Limiting

| Endpoint Group | Rate Limit |
|---------------|------------|
| Onboarding | 3/minute |
| Auth (login, password) | 5/minute |
| Plan/Meal/Workout | 60/minute |
| General API | 60/minute |

---

## Related Documentation

- [MOBILE_API.md](./MOBILE_API.md) - Mobile app integration guide
- [PLAN_API_ENDPOINTS.md](./PLAN_API_ENDPOINTS.md) - Detailed plan/meal/workout endpoints
- [SANCTUM_REACT_NATIVE_AUTH.md](./SANCTUM_REACT_NATIVE_AUTH.md) - Authentication implementation
- [SET_PASSWORD_FLOW.md](./SET_PASSWORD_FLOW.md) - Password setup flow
- [EMAIL_VERIFICATION_WORKFLOW.md](./EMAIL_VERIFICATION_WORKFLOW.md) - Email verification
- [WORKOUT_PLAN_GENERATION.md](./WORKOUT_PLAN_GENERATION.md) - Workout plan generation
- [MEAL_PLAN_GENERATION.md](./MEAL_PLAN_GENERATION.md) - Meal plan generation

