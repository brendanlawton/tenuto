# Tenuto Android

Kotlin / Jetpack Compose Android app for the Tenuto piano practice platform.

## Stack

- **Kotlin / Jetpack Compose** — Android 8.0+ (API 26) minimum
- **Retrofit + OkHttp** — API communication
- **kotlinx.serialization** — JSON serialization
- **EncryptedSharedPreferences** — secure token storage
- **Kotlin Coroutines** — async operations

## Architecture

Feature-based vertical slices mirroring the iOS app:

```
app/src/main/java/com/tenuto/android/
├── auth/         — AuthViewModel, TokenStore, LoginScreen, RegisterScreen
├── home/         — HomeScreen
├── core/         — ApiClient, Config
└── MainActivity.kt
```

- **Auth state** — `AuthViewModel` (`AndroidViewModel`) exposing a `StateFlow<String?>` token, injected via `by viewModels()`
- **Token storage** — `TokenStore` wrapping `EncryptedSharedPreferences`
- **API base URL** — `BuildConfig.DEBUG` flag in `Config.kt`; debug points to `10.0.2.2` (emulator alias for host localhost)
- **Navigation** — root conditional in `MainActivity` driven by `authViewModel.token`

## Prerequisites

- Android Studio (latest stable)
- Android emulator or physical device (API 26+)
- Laravel API running locally via Sail (`http://localhost:80`)

## Local setup

1. Open `android/` in Android Studio
2. Let Gradle sync complete
3. Create an emulator via Device Manager if needed (Pixel 8, API 26+ system image)
4. Press ▶ Run

The debug build points to `http://10.0.2.2/api/v1`. Make sure Sail is running — see the repo root `README.md`.

## Google Sign-In

Google Sign-In uses the Android Credential Manager SDK (`androidx.credentials`). The flow:

1. App calls `CredentialManager.getCredential()` with `GetSignInWithGoogleOption`, passing the **web application client ID** as `serverClientId`
2. User selects a Google account from the system prompt
3. Google returns an ID token with `aud` = web client ID
4. App posts the ID token to `POST /api/v1/auth/google/token`
5. API validates the token and returns a Sanctum bearer token

**Important**: The Android OAuth client ID (from Google Cloud Console, registered with package name + SHA-1) is not embedded in the app. It authorises the app with Google at the OS level. The web client ID (`Config.googleWebClientId`) is what the app uses at runtime.

To test Google Sign-In in the emulator, add a Google account first: Settings → Accounts → Add account → Google.

To set up credentials:

| Credential | Where used |
|---|---|
| Android OAuth client ID | Google Cloud only (package name + SHA-1 fingerprint) |
| Web client ID | `android/app/src/main/java/com/tenuto/android/core/Config.kt` → `googleWebClientId` |
| Web client ID | `api/.env` → `GOOGLE_CLIENT_ID` |

See ADR-0004 for why Android uses the web client ID rather than the Android client ID.

## Auth flow

- **Password login** — `POST /api/v1/auth/token` → Sanctum bearer token stored in EncryptedSharedPreferences
- **Registration** — `POST /api/v1/auth/register` → verification email sent; login requires verified email
- **Google Sign-In** — Credential Manager → ID token → `POST /api/v1/auth/google/token` → bearer token

See [`docs/adr/`](../docs/adr/) for architectural decisions, particularly ADR-0001 (dual auth strategy), ADR-0004 (mobile token exchange), and ADR-0005 (Android tech stack).
