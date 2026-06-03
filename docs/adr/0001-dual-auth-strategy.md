# ADR 0001 — Dual Authentication Strategy (Cookie + Bearer Token)

## Status
Accepted

## Context
The platform has two distinct client types with different authentication needs:
- **Web App** (Vue SPA on `app.tenuto.com`) — same subdomain as the API (`api.tenuto.com`)
- **Mobile Apps** (iOS + Android) — native, no browser cookie jar

Sanctum supports both strategies but they are not interchangeable.

## Decision
Use **cookie-based session authentication** for the Web App and **bearer token authentication** for the Mobile Apps.

The Web App calls `GET /sanctum/csrf-cookie` on load, then authenticates via `POST /login`. All subsequent requests include the session cookie and `X-CSRF-TOKEN` header. `SESSION_DOMAIN=.tenuto.com` ensures the cookie is valid across the subdomain boundary.

Mobile Apps authenticate via `POST /api/v1/auth/token` (email + password) to receive a Sanctum personal access token, store it securely on-device (Keychain / Keystore), and send it as `Authorization: Bearer {token}` on every request. This is a custom versioned endpoint — not Fortify's `POST /login`, which is web-middleware only and returns a session cookie.

Social Login is an alternative entry point into the same downstream paths — the Web App uses a browser redirect flow that terminates in a Sanctum session cookie; Mobile Apps use a token exchange flow that terminates in a Sanctum bearer token. See ADR-0003 (social identity model) and ADR-0004 (mobile token exchange design).

## Consequences
- The API must handle both authentication guards simultaneously (`sanctum` middleware works for both).
- CORS must be configured to allow `app.tenuto.com` with `supports_credentials = true`.
- Token scopes and expiry for mobile tokens must be designed carefully (future work).
- Switching the Web App to token auth later would require adding token storage logic and removing CSRF handling — non-trivial.
