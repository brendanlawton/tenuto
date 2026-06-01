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

## Local setup

```bash
cp .env.example .env
# Edit .env — set DB_DATABASE, DB_USERNAME, DB_PASSWORD

composer install
php artisan key:generate
php artisan migrate
php artisan serve             # http://localhost:8000
```

## Key configuration

| File | What to know |
|------|-------------|
| `config/fortify.php` | `'views' => false` — headless; enabled features listed under `features` |
| `config/sanctum.php` | Stateful domains controlled via `SANCTUM_STATEFUL_DOMAINS` in `.env` |
| `config/cors.php` | `supports_credentials = true`; add client origins to `allowed_origins` |
| `.env` | `SESSION_DOMAIN=localhost` for dev; `.tenuto.com` for production |

## Environment variables

```dotenv
# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=tenuto
DB_USERNAME=postgres
DB_PASSWORD=

# Session / CORS (dev)
SESSION_DOMAIN=localhost
SANCTUM_STATEFUL_DOMAINS=localhost:5173,localhost:3000,localhost:8000

# Session / CORS (production)
# SESSION_DOMAIN=.tenuto.com
# SANCTUM_STATEFUL_DOMAINS=app.tenuto.com
```

## API versioning

All application routes live under `/api/v1/`. Fortify and Sanctum routes are unversioned — they are framework-managed and not subject to application versioning.
