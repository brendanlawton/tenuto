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

## Auth flow

- **Password login** — `POST /api/v1/auth/token` → Sanctum bearer token stored in EncryptedSharedPreferences
- **Registration** — `POST /api/v1/auth/register` → verification email sent; login requires verified email

See [`docs/adr/`](../docs/adr/) for architectural decisions, particularly ADR-0001 (dual auth strategy) and ADR-0005 (Android tech stack).
