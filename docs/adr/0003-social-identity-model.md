# ADR 0003 — Social Identity Model (Separate Table)

## Status
Accepted

## Context
Adding social login (Google, Apple, Facebook) requires associating OAuth provider accounts with Tenuto users. The two structural options were:

- **Columns on `users`** (`google_id`, `apple_id`, `facebook_id`): simple but requires a migration per new provider and can't naturally express a user linking multiple providers.
- **Separate `social_identities` table**: one row per linked provider account, keyed by `(user_id, provider, provider_user_id)`.

## Decision
Use a separate `social_identities` table. Each row records one link between a Tenuto user and an OAuth provider account.

When a social login returns an email matching an existing Tenuto account, the Social Identity is silently linked to that account (auto-link). No user action required.

Social-login-only users have a nullable `password`. A "set password" flow is available in account settings so they can add email/password login later.

## Consequences
- Adding a fourth provider requires no schema change — only application code.
- A user can link multiple providers to one account without schema changes.
- The `password` column on `users` must be nullable; the forgot-password flow must guard against social-only users.
- Auto-linking by email means that owning a provider account with a matching email grants access to the Tenuto account. This is standard industry behaviour and an accepted trade-off.
