# Tenuto — Domain Glossary

## What is Tenuto?

A cross-platform piano practice app. Users connect a MIDI piano, work through practice routines, and receive assessment of their playing. Progress is tracked over time.

## Terms

### Platform
The full product: a Vue web app, iOS app, and Android app, all backed by a single Laravel API.

### Web App
The Vue 3 SPA served from `app.tenuto.com`. Authenticates via Sanctum cookie-based sessions (same subdomain as the API).

### API
The Laravel backend served from `api.tenuto.com`. Provides headless authentication (Sanctum + Fortify) and all application endpoints.

### Mobile App
The iOS and Android native apps. Authenticate with the API via Sanctum bearer tokens (not cookies).

### Session Authentication
Cookie-based authentication used by the Web App. Requires `SESSION_DOMAIN=.tenuto.com` so cookies are shared across subdomains. Managed by Laravel Sanctum + Fortify.

### Core
The shared C++ library that implements playability assessment logic. Compiles to WebAssembly for the Web App, and to a native static library for the iOS and Android apps. Ensures assessment behavior is identical across all platforms.

### MIDI Input
The mechanism by which a user's piano playing is captured and passed to the Core. On web: WebMIDI API. On iOS: CoreMIDI. On Android: Android MIDI API.

### Practice Routine
A structured set of exercises a user works through in a session.

### Playability Assessment
The evaluation of how well a user executed a Practice Routine. Computed entirely by the Core from MIDI Input data.

### Progress Tracking
The longitudinal record of a user's Playability Assessment results across sessions. Stored in the API.

### Token Authentication
Bearer-token authentication used by the Mobile Apps. Tokens are issued by the API via Sanctum's token API and stored securely on-device.

### Social Login
Authentication via a third-party OAuth provider (Google, Apple, Facebook) instead of email + password. Supported on all platforms. Produces a single Tenuto account regardless of which platform the user first authenticated on.

### Social Identity
A record linking a Tenuto `User` to a specific OAuth provider account. Stored in the `social_identities` table as a `(user_id, provider, provider_user_id)` tuple. One user may have multiple Social Identities (e.g., both Google and Apple linked).

### Identity Linking
The act of associating a Social Identity with an existing Tenuto account. Happens automatically (silently) when a social login returns an email that matches an existing account. Also available explicitly from account settings.

### Token Exchange
The mobile-specific social login flow. The Mobile App authenticates with the provider natively (using the provider's SDK), receives an ID token, and sends it to `POST /api/v1/auth/{provider}/token`. The API validates the token with the provider and returns a Sanctum bearer token. Contrast with the Web redirect flow.

### Password Enrollment
The act of adding an email/password login method to an account that was created via Social Login only. Available from Account Settings when the user has no password set (`has_password = false`). Does not remove or replace existing Social Identities — the user can continue to log in via both methods after enrollment.

### Account Settings
The authenticated area of the Web App where users manage their account. Initially contains Password Enrollment. Future sections: change password, linked providers, profile.
