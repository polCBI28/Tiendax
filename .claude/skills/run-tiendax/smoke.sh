#!/usr/bin/env bash
# Smoke test / launcher for tiendax (Sublimar Yamer - Laravel + Livewire/Volt + Flux POS app).
# Run from repo root with Git Bash: bash .claude/skills/run-tiendax/smoke.sh
#
# What it does:
#   1. Verifies MySQL (XAMPP) is reachable via `php artisan migrate:status`.
#   2. Ensures a dedicated driver/test user exists (does not touch real users' passwords).
#   3. Starts `php artisan serve` in the background (no-op if already running).
#   4. Curls key routes to confirm the app boots and auth-gated pages redirect correctly.
#   5. Confirms Vite assets are built (public/build/manifest.json).
set -euo pipefail
cd "$(dirname "$0")/../../.."

HOST=127.0.0.1
PORT=8000
BASE="http://$HOST:$PORT"

echo "==> Checking DB connectivity (XAMPP MySQL must be running)"
if ! php artisan migrate:status > /tmp/tiendax-migrate-status.log 2>&1; then
  echo "DB not reachable. Start MySQL in XAMPP Control Panel first."
  cat /tmp/tiendax-migrate-status.log
  exit 1
fi
echo "  OK"

echo "==> Ensuring driver test user exists (agent-driver@test.local)"
php artisan tinker --execute '
$u = App\Models\User::firstOrCreate(
    ["email" => "agent-driver@test.local"],
    ["name" => "Agent Driver", "password" => bcrypt("agent-driver-pass")]
);
echo "user id: " . $u->id;
'

echo "==> Checking built frontend assets"
if [ -f public/build/manifest.json ]; then
  echo "  OK (public/build/manifest.json present)"
else
  echo "  MISSING - run: npm run build"
  exit 1
fi

if netstat -an | grep -q "$HOST:$PORT.*LISTENING"; then
  echo "==> Server already running at $BASE"
else
  echo "==> Starting php artisan serve on $BASE"
  nohup php artisan serve --host=$HOST --port=$PORT > /tmp/tiendax-serve.log 2>&1 &
  disown
  sleep 2
fi

echo "==> Checking key endpoints"
code_login=$(curl -s -o /dev/null -w "%{http_code}" "$BASE/login")
echo "  GET /login -> $code_login"
code_dash=$(curl -s -o /dev/null -w "%{http_code}" "$BASE/dashboard")
echo "  GET /dashboard (unauthenticated, expect redirect) -> $code_dash"

echo
echo "Done. App running at $BASE"
echo "Login with: agent-driver@test.local / agent-driver-pass (test user, safe to reuse)"
echo "Drive it visually with the Browser tool (mcp__Claude_Browser__*) or Playwright/chromium-cli against $BASE/login."
