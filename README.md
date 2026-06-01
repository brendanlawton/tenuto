# Tenuto

A cross-platform piano practice app. Connect a MIDI piano, work through practice routines, and receive real-time assessment of your playing. Progress is tracked over time.

## Repository layout

```
tenuto/
├── api/          Laravel 13 — REST API, authentication, persistence
├── web/          Vue 3 SPA — web client
├── core/         Shared C++ — playability assessment logic (WASM + native)
└── mobile/
    ├── ios/      Swift / Xcode
    └── android/  Kotlin / Android Studio
```

> `core/` and `mobile/` are not yet scaffolded.

## Architecture

- **Web client** authenticates via Sanctum **cookie-based sessions** (`app.tenuto.com` ↔ `api.tenuto.com`, `SESSION_DOMAIN=.tenuto.com`).
- **Mobile clients** authenticate via Sanctum **bearer tokens** stored on-device.
- **Playability assessment** lives entirely in the C++ core — compiled to WebAssembly for the web, linked as a static library for iOS/Android. No assessment logic is duplicated.
- All application routes are versioned under `/api/v1/`.

See [`docs/adr/`](docs/adr/) for architectural decisions and [`CONTEXT.md`](CONTEXT.md) for domain terminology.

## Prerequisites

| Tool | Version |
|------|---------|
| PHP | 8.4+ |
| Composer | 2.x |
| Node | 20+ |
| pnpm | 9+ |
| PostgreSQL | 15+ |

## Local setup

### 1. API

```bash
cd api
cp .env.example .env          # then fill in DB_* credentials
composer install
php artisan key:generate
php artisan migrate
php artisan serve             # http://localhost:8000
```

### 2. Web

```bash
# from repo root
pnpm install
pnpm -F web dev               # http://localhost:5173
```

## Running both together

Open two terminals — one for `php artisan serve` and one for `pnpm -F web dev`. The Vue app proxies auth requests to `http://localhost:8000` via the `VITE_API_URL` env var.

## Packages

| Package | Purpose |
|---------|---------|
| `laravel/sanctum` | Cookie session auth (SPA) + bearer token auth (mobile) |
| `laravel/fortify` | Headless auth backend — register, login, password reset, email verification |
