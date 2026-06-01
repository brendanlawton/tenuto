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

Mobile Apps call `POST /login` (or a dedicated token endpoint) to receive a Sanctum personal access token, store it securely on-device (Keychain / Keystore), and send it as `Authorization: Bearer {token}` on every request.

## Consequences
- The API must handle both authentication guards simultaneously (`sanctum` middleware works for both).
- CORS must be configured to allow `app.tenuto.com` with `supports_credentials = true`.
- Token scopes and expiry for mobile tokens must be designed carefully (future work).
- Switching the Web App to token auth later would require adding token storage logic and removing CSRF handling — non-trivial.
