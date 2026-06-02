# Tenuto API

Laravel 13 REST API powering the Tenuto piano practice platform.

## Stack

- **Laravel 13** / PHP 8.4+
- **PostgreSQL** (SQLite for local development)
- **Laravel Sanctum** — dual authentication: cookie sessions for the SPA, bearer tokens for mobile
- **Laravel Fortify** — headless auth backend (no Blade views)

## Auth endpoints

These are registered by Fortify/Sanctum — no custom controllers needed.

| Method | Path | Description |
|--------|------|-------------|
| `GET` | `/sanctum/csrf-cookie` | Initialise CSRF cookie (SPA must call before any POST) |
| `POST` | `/login` | Authenticate — returns session cookie or bearer token |
| `POST` | `/logout` | End session / revoke token |
| `POST` | `/register` | Create account |
| `POST` | `/forgot-password` | Send password reset email |
| `POST` | `/reset-password` | Set new password via reset token |
| `POST` | `/email/verification-notification` | Resend verification email |
| `GET` | `/api/v1/user` | Authenticated user — requires `auth:sanctum` |

## Local setup (Sail)

```bash
cp .env.example .env
composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
```

API: `http://localhost` (port 80). Run the web app separately — see repo root `README.md`.

## Local setup (without Sail)

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan serve             # http://localhost:8000
```

Adjust `SANCTUM_STATEFUL_DOMAINS` to include `localhost:8000` and set `VITE_API_URL=http://localhost:8000` in `web/.env` (no Vite proxy).

## Key configuration

| File | What to know |
|------|-------------|
| `config/fortify.php` | `'views' => false` — headless; enabled features listed under `features` |
| `config/sanctum.php` | Stateful domains controlled via `SANCTUM_STATEFUL_DOMAINS` in `.env` |
| `config/cors.php` | `supports_credentials = true`; add client origins to `allowed_origins` |
| `.env` | `SESSION_DOMAIN=null` for Sail; `.tenuto.com` for production |

## Environment variables

```dotenv
# Session / CORS (Sail + Vite on :5173)
SESSION_DOMAIN=null
SANCTUM_STATEFUL_DOMAINS=localhost:5173,localhost:3000,localhost

# Session / CORS (production)
# SESSION_DOMAIN=.tenuto.com
# SANCTUM_STATEFUL_DOMAINS=app.tenuto.com
```

With Sail, leave `web/.env` `VITE_API_URL` empty so the Vite dev proxy forwards auth routes to `http://localhost` (avoids cross-origin CSRF cookie issues).

## API versioning

All application routes live under `/api/v1/`. Fortify and Sanctum routes are unversioned — they are framework-managed and not subject to application versioning.
