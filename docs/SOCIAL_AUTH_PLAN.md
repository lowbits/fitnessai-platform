# Social Sign-In (Google & Apple) — v3 Plan

Add "Sign up / Sign in with Google & Apple" alongside the existing email + password
flow. We are free to shape v3, so we also fix the current awkwardness where
account creation and token issuance are two separate calls.

**Principles:** KISS · thin controllers · lean on packages (no hand-rolled JWT/crypto) ·
one uniform auth contract for every method · signup returns a token.

---

## 1. Current state (why a small rework)

- **No `/register`.** Account creation = `POST /v3/onboarding`
  (`App\Http\Controllers\Api\V3\MobileOnboardingController@store`): creates the user
  (password **required**), profile, trial plan, dispatches plan-generation jobs, sends
  the verify email — and returns **`{ user }` with no token**.
- The app then calls `POST /v2/auth/login`
  (`App\Http\Controllers\Api\V2\AuthController@login`) to get the Sanctum token.
- `users.password` is **already nullable** → social-only accounts fit the schema.
- No `provider` columns, no Socialite, no `verified` middleware (email verification is
  soft, enforced client-side via `email_verified_at`).
- Sanctum tokens: no expiry, no abilities; created as `createToken($device_name)`.
- RevenueCat (`noopstudios/laravel-revenue-cat` `Billable`) keys off `user.id` — no auth
  changes needed.

**Implication:** a first-time social user must run through the same provisioning
(profile + plan + jobs), but has no password and needs a token back. So we make
account-provisioning reusable and make signup return a token for every method.

---

## 2. Packages

Add **one** dependency family — Socialite handles verification for both providers
through the identical `userFromToken` call, so we write no JWT/JWKS code:

```bash
composer require laravel/socialite socialiteproviders/apple
```

- **Google** → `Socialite::driver('google')->userFromToken($token)`
- **Apple**  → `Socialite::driver('apple')->userFromToken($token)`
  (registered via the SocialiteProviders event listener; verifies Apple's JWT against
  Apple's JWKS internally, cached)

Both return a `Laravel\Socialite\Contracts\User` → one code path, one mapping.

> Client sends: **Apple** → the `identityToken` (JWT); **Google** → the OAuth
> `accessToken` from the native Google Sign-In SDK. No nonce plumbing needed in v1
> (Socialite's Apple provider verifies signature + audience). Revisit nonce if we later
> want defence-in-depth against token replay.

---

## 3. Data model

KISS: columns on `users` (each user picks one method for now). A `social_accounts`
join table is the upgrade path if we ever need one user linked to *both* providers —
explicitly YAGNI for v1.

Migration `add_social_columns_to_users_table`:

```php
$table->string('provider')->nullable()->after('password');       // 'google' | 'apple'
$table->string('provider_id')->nullable()->after('provider');
$table->string('avatar')->nullable()->after('provider_id');
$table->unique(['provider', 'provider_id']);
```

`users.password` stays nullable (already is). Add `fillable`: `provider`, `provider_id`,
`avatar` in `App\Models\User`.

`App\Enums\UserSource` is the **registration platform** (`web` / `mobile_apple` /
`mobile_android`) — *not* the login method. The login method lives in the new `provider`
column. Keep them separate: a user can register from the iOS app (`mobile_apple`) yet
sign in with Google (`provider = google`).

---

## 4. The unified auth contract

Every entry point takes the same discriminated `auth` block, so email and social share
one path and the controllers stay tiny:

```jsonc
// password
"auth": { "type": "password", "password": "•••••••" }
// social
"auth": { "type": "google", "token": "<google access token>" }
"auth": { "type": "apple",  "token": "<apple identity token>" }
```

---

## 5. Endpoints (v3)

Two endpoints, both return `{ user, api_token }`. Signup carries onboarding data; login
does not.

### `POST /v3/signup`  (throttle:3,1)
Replaces the token-less `POST /v3/onboarding` for new accounts.

```jsonc
{ ...onboardingFields, "device_name": "...", "auth": { "type": "...", ... } }
→ 201 { "user": {...}, "api_token": "..." }
```

Flow: resolve identity → create user → provision (profile + plan + jobs + verify mail
for password users only) → issue token.

### `POST /v3/auth/login`  (throttle:5,1)
Returning users, any method. Supersedes `POST /v2/auth/login`.

```jsonc
{ "device_name": "...", "auth": { "type": "...", ... } }
→ 200 { "user": {...}, "api_token": "..." }
```

Flow: resolve identity → find user → issue token.

Keep `POST /v2/auth/login` working during transition (§9).

---

## 6. Code structure (small, single-purpose)

Controllers are thin; the reusable logic lives in a few actions/services.

```
app/
  Services/Auth/
    SocialAuthService.php          # verify(string $provider, string $token): SocialUserData
                                   #   -> Socialite::driver($provider)->stateless()->userFromToken($token)
  DataTransferObjects/
    SocialUserData.php             # {provider, providerId, email, name, avatar}
  Actions/Auth/
    ResolveAccountFromCredential.php  # auth block -> User (create or find); owns linking + email_verified_at
    ProvisionOnboardingUser.php       # EXTRACTED from MobileOnboardingController@store:
                                      #   profile + plan + jobs (+ verify mail)
    IssueApiToken.php                 # thin: $user->createToken($deviceName)->plainTextToken
  Http/
    Requests/Api/V3/
      SignupRequest.php            # onboarding rules + auth block rules (password required only when type=password)
      SocialLoginRequest.php       # device_name + auth block
    Controllers/Api/V3/
      SignupController.php         # __invoke: resolve -> provision -> issue -> 201
      LoginController.php          # __invoke: resolve -> issue -> 200
```

- `ResolveAccountFromCredential` is the **only** place that decides create-vs-find,
  links accounts, and stamps `email_verified_at` — one source of truth.
- `ProvisionOnboardingUser` is lifted verbatim from today's `store()` so behaviour is
  identical for email signups and reused for social signups.
- No custom crypto, no per-provider verifier classes — Socialite is the abstraction.

Example controller (the whole thing):

```php
public function __invoke(SignupRequest $request): JsonResponse
{
    $user = $this->resolve->create($request->validated());   // password or social
    $this->provision->handle($user, $request->onboarding()); // profile + plan + jobs
    $token = $this->issueToken->handle($user, $request->deviceName());

    return response()->json(['user' => new UserResource($user), 'api_token' => $token], 201);
}
```

---

## 7. Account resolution & linking

Centralised in `ResolveAccountFromCredential`:

1. **Social, provider_id known** → return that user.
2. **Social, email matches an existing user** → auto-link (set `provider`/`provider_id`
   on it), stamp `email_verified_at = now()`, return it. **Unconditional** — no
   verified-gate: mobile emails are never verified, so a gate would reject every
   collision for no real security gain (accounts only come from the full onboarding
   flow, not a throwaway register form).
3. **Social, unknown** → create user (no password), `email_verified_at = now()`.
4. **Password** → create user with hashed password (signup) / `Auth::attempt` (login).

---

## 8. Email verification

- Google/Apple emails are provider-verified → set `email_verified_at = now()` on create.
- Password signups keep the current behaviour (unverified + `OnboardingCompleteVerifyEmail`).
- No API middleware change (there is no `verified` guard today); the mobile client's
  `email_verified_at` check is satisfied automatically for social users.

---

## 9. Backward compatibility & rollout

- Ship migration + Socialite config + new endpoints. **Do not remove** `POST /v2/auth/login`
  or `POST /v3/onboarding` yet.
- Point the app at `/v3/signup` + `/v3/auth/login`; retire the old two once the app
  version that uses them is the floor.
- `ProvisionOnboardingUser` extraction is behaviour-preserving → existing email signups
  are unaffected.

---

## 10. Mobile client (out of scope for this backend plan, listed for coordination)

- Add `expo-apple-authentication` + `@react-native-google-signin/google-signin` (config
  plugins + native rebuild; SDK 57 prebuild workflow).
- Last onboarding screen: **[Continue with Apple] [Continue with Google] [Sign up with email]**.
  Onboarding data already in memory; tapped method fills the `auth` block; call `/v3/signup`.
- Apple only returns name on first auth — irrelevant, we use the onboarding-entered name.
- **App Store 4.8:** offering Google on iOS makes **Sign in with Apple mandatory**.

---

## 11. Console / credential setup

- **Apple:** Services ID, Sign in with Apple key (.p8), key id, team id → `config/services.php`
  `apple` block (client id = app bundle id for native token verification).
- **Google:** OAuth client IDs (iOS + Android + a "web/server" client id used as the
  audience) → `config/services.php` `google` block.

---

## 12. Testing

- Unit: `SocialAuthService` with a faked Socialite user; `ResolveAccountFromCredential`
  for all four branches incl. the linking decision.
- Feature: `/v3/signup` (password + google + apple) asserts user+profile+plan created,
  jobs dispatched (`Bus::fake`), token returned, `email_verified_at` set for social;
  `/v3/auth/login` for returning users; email-collision path.
- Mock Socialite via `Socialite::shouldReceive('driver->...->userFromToken')` — no real
  network in tests.

---

## 13. Task checklist

- [ ] `composer require laravel/socialite socialiteproviders/apple`
- [ ] Register Apple provider event listener; add `apple` + `google` to `config/services.php`
- [ ] Migration: `provider`, `provider_id` (unique), `avatar`; update `User::$fillable`
- [ ] `SocialUserData` DTO + `SocialAuthService`
- [ ] Extract `ProvisionOnboardingUser` from `MobileOnboardingController@store`
- [ ] `ResolveAccountFromCredential` (+ linking decision) + `IssueApiToken`
- [ ] `SignupRequest`, `SocialLoginRequest`
- [ ] `SignupController`, `LoginController`; routes in `routes/api.php` (v3)
- [ ] Tests (unit + feature, Socialite mocked)
- [ ] Keep v2 login / v3 onboarding until app floor updated

---

## 14. Effort

**Backend ≈ 2 days** (small classes, Socialite does the verification). Plus mobile
native-module work and Apple/Google console setup, tracked separately in the mobile-app
repo doc `docs/SOCIAL_AUTH_MOBILE.md`.

## Decisions (settled)

1. **Email-collision linking** — **auto-link, unconditionally** (§7); stamp
   `email_verified_at` on link.
2. **Web login for social users** — N/A, there are no web users.
3. **Providers / placement** — Apple + Google; login screen + onboarding signup.
