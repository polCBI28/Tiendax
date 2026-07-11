---
name: run-tiendax
description: Build, launch, and drive the tiendax / "Sublimar Yamer" Laravel 12 + Livewire/Volt + Flux retail POS app. Use when asked to run, start, serve, launch, test, or screenshot this app, or to verify a change works end-to-end in the real UI (not just tests).
---

Laravel 12 + Livewire 4 / Volt (auth pages) + Flux UI retail management app
("Sublimar Yamer" / ShopMaster Pro). Blade + vanilla JS for the main app
(dashboard, productos, categorias, clientes, ventas, movimientos, reportes),
Livewire/Volt only for login/register/settings. Runs on native PHP via XAMPP
(no Docker/Sail in this environment). All paths below are relative to the
repo root (`<repo>/`), not to this skill directory.

## Agent path (primary) — `smoke.sh`

Run the driver from the repo root with Git Bash:

```bash
bash .claude/skills/run-tiendax/smoke.sh
```

It: checks MySQL is reachable, creates/reuses a dedicated test user
(`agent-driver@test.local` / `agent-driver-pass` — does not touch real
users' passwords), confirms `public/build/manifest.json` exists, starts
`php artisan serve` on `http://127.0.0.1:8000` in the background if not
already running (safe to re-run — detects an already-listening server),
and curls `/login` and `/dashboard` to confirm the app boots.

Then drive it visually with the Browser tool (`mcp__Claude_Browser__*` in
this environment; Playwright/`chromium-cli` elsewhere) against
`http://127.0.0.1:8000/login`:

1. `navigate` to `http://127.0.0.1:8000/login`.
2. `read_page` (filter `interactive`) to get refs for the email/password
   inputs and submit button.
3. `form_input` the email/password fields with the driver credentials
   above, then `computer` `left_click` the submit button ref.
4. `screenshot` to confirm you landed on `/dashboard` ("Panel de Control").

This exact sequence was run and verified in this session — login succeeds
and the dashboard renders with real KPI cards (ventas hoy, bajo stock,
pedidos pendientes).

To stop the server: find the listening PIDs and kill them (`php artisan
serve` with `PHP_CLI_SERVER_WORKERS=4` in `.env` spawns multiple worker
processes on the same port):

```bash
for pid in $(netstat -ano | grep "127.0.0.1:8000" | grep LISTENING | awk '{print $5}' | sort -u); do
  taskkill //F //PID "$pid"
done
```

## Prerequisites

- PHP 8.2 (XAMPP's `C:\xampp\php\php.exe`), Composer, Node 22 / npm 10.
- **MySQL running** (XAMPP Control Panel → start MySQL). The app connects to
  `DB_DATABASE=tienda` on `127.0.0.1:3306`, user `root`, no password
  (see `.env`). `smoke.sh` fails fast with the actual error if MySQL isn't up.
- `vendor/`, `node_modules/`, and `public/build/` (compiled Vite assets) were
  already present in this checkout — if missing, run `composer install` and
  `npm install && npm run build`.

## Build

Already built in this checkout. If you need to rebuild frontend assets:

```bash
npm run build
```

If you see `Illuminate\Foundation\ViteException: Unable to locate file in
Vite manifest`, that's the signal `public/build/` is stale/missing — rerun
the above.

## Run (human path)

```bash
composer run dev
```

Runs `php artisan serve` + `php artisan queue:listen` + `npm run dev`
concurrently (see `composer.json`). Useful for live frontend editing; not
needed for a one-off headless verification (`smoke.sh` + `php artisan
serve` alone is enough).

## Test

```bash
php artisan test --compact
```

Pest, 21 passing / 6 pre-existing failures in `tests/Feature/Settings/*`
(missing `settings.profile` named route — unrelated to app runtime, exists
before any of your changes). Don't be alarmed by those 6; check `php
artisan route:list` if you need to confirm a failure is pre-existing vs.
something you introduced.

Style check (does not auto-fix): `vendor/bin/pint --test --format agent`.
Per `AGENTS.md`, only run `vendor/bin/pint --dirty --format agent` (no
`--test`) after you've actually edited PHP files.

## Gotchas

- **Login is a Livewire/Volt component**, not a plain form POST — you
  can't fake a login with a single `curl -d`. Drive it through a real
  browser (see Agent path above); `smoke.sh` only curl-checks status codes,
  it doesn't authenticate.
- **`php artisan serve` spawns multiple worker processes** on the same
  port (`PHP_CLI_SERVER_WORKERS=4` in `.env`) — killing one PID from
  `netstat` isn't enough to actually free the port; kill all of them (see
  stop instructions above).
- **Material Symbols icons render as literal text** (e.g. "dashboard",
  "category") on a cold/offline load of the login page — they depend on
  `fonts.bunny.net` / Material Symbols webfont over the network. This is
  cosmetic and resolves once the font loads (confirmed: `/productos`
  rendered icons correctly with the exact same session).
- **Session persists across navigations** (`SESSION_DRIVER=database`) —
  once logged in via the browser, subsequent `navigate` calls to
  `/dashboard` or any authed route stay logged in without re-submitting
  the login form.
- Root `/` redirects to `/dashboard`, which requires auth — expect a `302`
  from `smoke.sh`'s unauthenticated curl check, not a `200`.
