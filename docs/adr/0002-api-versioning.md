# ADR 0002 — API Versioned from Day One

## Status
Accepted

## Context
Three clients (Web App, iOS, Android) will consume the same API. Native mobile apps cannot be force-updated — older installs will continue hitting the API after new versions ship.

## Decision
All application routes are prefixed `/api/v1/`. Auth routes managed by Fortify and Sanctum remain unversioned (e.g., `/login`, `/sanctum/csrf-cookie`) as these are framework-owned paths not subject to application-level versioning.

## Consequences
- Breaking changes can be shipped as `/api/v2/` without disrupting older mobile clients.
- Slightly more boilerplate in route files from day one.
- The Web App must always target a specific version prefix — no implicit "latest".
