# ADR 0004 — Mobile Social Login via Token Exchange

## Status
Accepted

## Context
Mobile apps (iOS, Android) need to support social login. Two approaches:

- **Redirect via webview**: reuse the same `GET /auth/{provider}/redirect` + `GET /auth/{provider}/callback` flow the Web App uses, inside a webview. Simpler — one flow for all clients.
- **Token exchange**: mobile authenticates natively with the provider SDK, receives an ID token, and posts it to a dedicated API endpoint which validates it and returns a Sanctum bearer token.

## Decision
Mobile uses token exchange via `POST /api/v1/auth/{provider}/token`. The Web App continues to use the redirect flow (`GET /auth/{provider}/redirect` + callback).

A single `SocialAuthController` dispatches on the `{provider}` route parameter for both flows.

## Platform-specific implementation notes

### iOS
The iOS client ID is passed directly to `GIDSignIn`. The resulting ID token has `aud` = iOS client ID. The API validates against `GOOGLE_IOS_CLIENT_ID`.

### Android
Android's Credential Manager SDK requires a **web application client ID** as the `serverClientId`, not the Android OAuth client ID. The Android OAuth client (registered in Google Cloud with package name + SHA-1 fingerprint) authorises the app with Google but is never embedded in the app's code. The resulting ID token has `aud` = web client ID. The API validates against `GOOGLE_CLIENT_ID` (the web client).

This means `MobileSocialTokenController` accepts two valid audiences: `GOOGLE_IOS_CLIENT_ID` (iOS) and `GOOGLE_CLIENT_ID` (Android). A token with any other `aud` is rejected.

## Consequences
- Native provider SDKs give users system-level prompts and biometrics — required by Apple for Sign in with Apple on iOS.
- The API must validate provider tokens server-side (Google tokeninfo, Apple JWT, Facebook Graph API) — more API surface than the redirect flow alone.
- The mobile API contract (`POST /api/v1/auth/{provider}/token`) is versioned and stable; mobile clients cannot be force-updated so this endpoint must not change shape without a version bump.
- Web redirect flow and mobile token exchange are two separate entry points into the same downstream auth path (Sanctum session vs. bearer token), consistent with ADR-0001.
