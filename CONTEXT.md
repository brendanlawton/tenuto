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
