// Minimal REPL-style driver for driving ShopMaster Pro (tiendax) in a real
// browser. Uses playwright-core against the system-installed Chrome (no
// bundled Chromium download needed). Reads newline-delimited commands from
// stdin so it can be piped a heredoc or driven interactively under tmux.
//
// Commands:
//   nav <path>                 goto http://127.0.0.1:8000<path>
//   click <selector>           click a selector (CSS or text=... )
//   fill <selector> <text...>  fill an input
//   press <key>                press a key on the focused element
//   wait-for <selector>        wait for selector to be visible (10s timeout)
//   screenshot [name]          save PNG to ./screenshots/<name|auto>.png
//   eval <js>                  page.evaluate arbitrary JS, prints result
//   console                    print collected console errors so far
//   login                      convenience: log in as the seeded test user
//   quit                       close browser and exit
//
// Env:
//   BASE_URL   default http://127.0.0.1:8000
//   HEADLESS   "0" to show the window (default headless)

import { chromium } from 'playwright-core';
import * as readline from 'node:readline';
import { mkdirSync } from 'node:fs';
import path from 'node:path';

const BASE_URL = process.env.BASE_URL || 'http://127.0.0.1:8000';
const CHROME_PATH =
  process.env.CHROME_PATH ||
  'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const SHOT_DIR = path.join(process.cwd(), 'screenshots');
mkdirSync(SHOT_DIR, { recursive: true });

const browser = await chromium.launch({
  executablePath: CHROME_PATH,
  headless: process.env.HEADLESS !== '0',
});
const page = await (await browser.newContext()).newPage();

const consoleErrors = [];
page.on('console', (msg) => {
  if (msg.type() === 'error') consoleErrors.push(msg.text());
});
page.on('pageerror', (err) => consoleErrors.push(String(err)));

let shotCounter = 0;
function log(...args) {
  console.log(...args);
}

async function handle(line) {
  const trimmed = line.trim();
  if (!trimmed) return;
  const [cmd, ...rest] = trimmed.split(' ');
  const arg = rest.join(' ');

  try {
    switch (cmd) {
      case 'nav': {
        const url = arg.startsWith('http') ? arg : BASE_URL + arg;
        await page.goto(url, { waitUntil: 'domcontentloaded' });
        log('OK nav', page.url());
        break;
      }
      case 'click':
        await page.click(arg, { timeout: 10000 });
        log('OK click', arg);
        break;
      case 'fill': {
        const sp = arg.indexOf(' ');
        const selector = arg.slice(0, sp);
        const text = arg.slice(sp + 1);
        await page.fill(selector, text, { timeout: 10000 });
        log('OK fill', selector);
        break;
      }
      case 'press':
        await page.keyboard.press(arg);
        log('OK press', arg);
        break;
      case 'wait-for':
        await page.waitForSelector(arg, { timeout: 10000, state: 'visible' });
        log('OK wait-for', arg);
        break;
      case 'reload':
        await page.reload({ waitUntil: 'networkidle', timeout: 15000 });
        log('OK reload', page.url());
        break;
      case 'screenshot': {
        const name = arg || `shot-${++shotCounter}`;
        const file = path.join(SHOT_DIR, `${name}.png`);
        await page.screenshot({ path: file, fullPage: true });
        log('OK screenshot', file);
        break;
      }
      case 'eval': {
        const result = await page.evaluate(arg);
        log('OK eval', JSON.stringify(result));
        break;
      }
      case 'console':
        log('CONSOLE_ERRORS', JSON.stringify(consoleErrors));
        break;
      case 'login':
        await page.goto(BASE_URL + '/login', { waitUntil: 'domcontentloaded' });
        await page.fill('input[type="email"]', 'agent@tiendax.test');
        await page.fill('input[type="password"]', 'agent-test-1234');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/dashboard', { timeout: 10000 });
        // First paint of any x-layouts.app page races the external Tailwind
        // CDN <script> + Google Fonts (Material Symbols) — icons can render
        // as literal ligature text and the console can show a spurious
        // "tailwind is not defined". A reload after the page is idle is the
        // reliable fix (see SKILL.md Gotchas).
        await page.waitForLoadState('networkidle', { timeout: 15000 });
        await page.reload({ waitUntil: 'networkidle', timeout: 15000 });
        log('OK login', page.url());
        break;
      case 'quit':
        await browser.close();
        log('OK quit');
        process.exit(0);
        break;
      default:
        log('ERR unknown command', cmd);
    }
  } catch (err) {
    log('ERR', cmd, err.message.split('\n')[0]);
  }
}

// readline emits 'line' synchronously as input arrives (and 'close' fires
// right after, on EOF, without waiting), but `handle` is async — without a
// single serialized chain, a piped heredoc would have 'close' race ahead
// and close the browser mid-command. Chain every line (and the final
// close) onto one promise so they run strictly in order.
let chain = Promise.resolve();
const rl = readline.createInterface({ input: process.stdin });
rl.on('line', (line) => {
  chain = chain.then(() => handle(line));
});
rl.on('close', () => {
  chain = chain.then(async () => {
    await browser.close();
    process.exit(0);
  });
});
