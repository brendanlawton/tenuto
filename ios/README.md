# Tenuto iOS

Swift / SwiftUI iOS app for the Tenuto piano practice platform.

## Stack

- **Swift / SwiftUI** — iOS 17+ minimum deployment target
- **SwiftData** — local persistence (reserved for future use)
- **GoogleSignIn-iOS** — native Google Sign-In via SPM
- **URLSession** — API communication (no third-party networking library)

## Architecture

Feature-based vertical slices mirroring the web app:

```
ios/ios/
├── Auth/         — AuthState, KeychainStore, LoginView, RegisterView
├── Home/         — HomeView
├── Core/         — APIClient, Config
└── iosApp.swift
```

- **Auth state** — `@MainActor @Observable` class (`AuthState`) injected via SwiftUI environment
- **Token storage** — `KeychainStore` wrapping the Security framework directly
- **API base URL** — compile-time `#if DEBUG` flag in `Config.swift`
- **Navigation** — root view switch in `iosApp.swift` driven by `authState.isAuthenticated`

## Prerequisites

- Xcode 16+
- iOS 17+ simulator or physical device
- Laravel API running locally via Sail (`http://localhost:80`)

## Local setup

1. Open `ios/ios.xcodeproj` in Xcode
2. Select a simulator or connected device
3. Press `Cmd+R` to build and run

The debug build points to `http://localhost:80/api/v1`. Make sure Sail is running — see the repo root `README.md`.

## Google Sign-In

Google Sign-In requires credentials from Google Cloud Console:

| Credential | Where used |
|------------|-----------|
| iOS OAuth client ID | `ios/ios/Core/Config.swift` → `googleClientID` |
| iOS OAuth client ID | `api/.env` → `GOOGLE_IOS_CLIENT_ID` |
| URL scheme (reversed client ID) | Xcode → ios target → Info tab → URL Types |

To set up your own Google Cloud credentials:
1. Go to Google Cloud Console → APIs & Services → Credentials
2. Create an OAuth 2.0 Client ID with application type **iOS**
3. Set the bundle ID to match `PRODUCT_BUNDLE_IDENTIFIER` in `ios.xcodeproj`
4. Add the reversed client ID as a URL scheme in Xcode (target → Info → URL Types)
5. Update `Config.googleClientID` in `Core/Config.swift`
6. Add `GOOGLE_IOS_CLIENT_ID` to `api/.env`

## Testing

```bash
# Unit tests (Swift Testing)
Cmd+U in Xcode

# API tests (must run from api/ with Sail up)
cd api && ./vendor/bin/sail artisan test
```

## Auth flow

- **Password login** — `POST /api/v1/auth/token` → Sanctum bearer token stored in Keychain
- **Registration** — `POST /api/v1/auth/register` → verification email sent; login requires verified email
- **Google Sign-In** — native SDK → ID token → `POST /api/v1/auth/google/token` → bearer token

See [`docs/adr/`](../docs/adr/) for architectural decisions, particularly ADR-0001 (dual auth strategy) and ADR-0004 (mobile social token exchange).
