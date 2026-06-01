# Tenuto Web

Vue 3 SPA — the browser client for the Tenuto piano practice platform.

## Stack

- **Vue 3** with Composition API
- **TypeScript**
- **Vite 6** + `@tailwindcss/vite` (Tailwind v4)
- **Pinia** — state management
- **Vue Router 4** — client-side routing with auth guards
- **axios** — HTTP client with CSRF and credential handling

## Local setup

```bash
# from repo root
pnpm install
cp .env.example .env          # set VITE_API_URL if needed
pnpm -F web dev               # http://localhost:5173
```

The API must be running at `VITE_API_URL` (default: `http://localhost:8000`).

## Environment variables

```dotenv
VITE_API_URL=http://localhost:8000
```

## Project structure

```
src/
├── api/
│   └── client.ts        Axios instance (withCredentials, X-Requested-With)
├── stores/
│   └── auth.ts          Pinia auth store — user state, login/register/logout/etc.
├── router/
│   └── index.ts         Routes + navigation guards (requiresAuth, requiresVerification, guestOnly)
└── views/
    ├── auth/
    │   ├── LoginView.vue
    │   ├── RegisterView.vue
    │   ├── ForgotPasswordView.vue
    │   ├── ResetPasswordView.vue
    │   └── VerifyEmailView.vue
    └── DashboardView.vue
```

## Auth flow

1. **SPA init** — router guard calls `auth.fetchUser()` on first navigation; sets `initialized = true`.
2. **Login / Register** — store calls `GET /sanctum/csrf-cookie` first (sets `XSRF-TOKEN` cookie), then the auth endpoint. Axios forwards the token automatically as `X-XSRF-TOKEN`.
3. **Protected routes** — `requiresAuth` redirects unauthenticated users to `/login`; `requiresVerification` redirects unverified users to `/verify-email`.
4. **Guest-only routes** — redirect authenticated users to `/dashboard`.

## Type checking

```bash
pnpm -F web type-check
```

## Build

```bash
pnpm -F web build
```
