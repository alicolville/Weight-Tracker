# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

Weight Tracker is a WordPress plugin (PHP, jQuery, no build step) that lets logged-in users log weight/measurements/photos and gives site admins tools to manage that data. It ships as a single codebase for both the free and premium ("Premium", formerly split into "Pro" / "Pro Plus") tiers — there is no separate premium build. Almost all premium code is always loaded; individual functions check `WS_LS_IS_PREMIUM` at runtime and return an upgrade notice (`ws_ls_display_pro_upgrade_notice_for_shortcode()`) when unlicensed. `includes/license.php` (`ws_ls_has_a_valid_license()`) decides the license type, and `weight-loss-tracker.php` sets `WS_LS_IS_PREMIUM` from it before requiring everything else.

## Commands

There is no build step (plain JS/CSS, hand-maintained `*.min.js` alongside the source file) and no PHP linter/PHPUnit configured. The only tooling is the Playwright E2E suite in `playwright/`, run against a live local WordPress install.

Prerequisites for running tests:
- The Docker WordPress stack (`docker-wordpress`) must be running and reachable at `http://localhost/`.
- A WP user `playwright` / `password` must exist (used by `playwright/tests/wp.auth.ts` to seed `.auth/user.json`).
- A valid Premium license must be active, since most shortcodes/features short-circuit to an upgrade notice otherwise.

```bash
cd wp-content/plugins/Weight-Tracker   # package.json / node_modules live here
npx playwright test                    # run the whole suite (auto-discovers playwright/playwright.config.js)
npx playwright test shortcode.wt-bmi.test.ts        # one file
npx playwright test -g "shows numeric BMI"          # one test by name (matches across files)
npx playwright test --headed --debug               # step through interactively
npx playwright show-report                          # view the last HTML report
```

The suite is configured with `workers: 1` (`playwright/playwright.config.js`) — tests share one WordPress user's data and are not safe to parallelize. Tests are grouped with `test.describe.configure({ mode: 'serial' })` for the same reason. An `auth` project (`wp.auth.ts`, matched by `*.auth.ts`) runs first and every other test project depends on it for `storageState`.

Running a test leaves `.auth/user.json` and `test-results/.last-run.json` dirty even though they're tracked in git — that's expected churn, not a real change.

## Architecture

### Request/feature layering

- `includes/` — core (free-tier) functionality: DB access, caching, the main `[wt]` shortcode, AJAX dispatch, form/table/chart rendering helpers, admin pages.
- `pro-features/` — premium functionality (BMI/BMR/macronutrient calculators, charts, awards, challenges, groups, webhooks, gamification, etc.). Required unconditionally from `weight-loss-tracker.php` alongside `includes/` (only `gamification.php` is conditionally required on `WS_LS_IS_PREMIUM`); gating happens inside each function/shortcode instead.
- `pro-features/plus/` — a legacy third tier ("Pro Plus") that has since been folded into "Premium" (`WS_LS_IS_PRO_PLUS` is now just an alias for `WS_LS_IS_PREMIUM`). Meta fields/photos, BMR, Harris Benedict, macronutrient calculator, and the barcode reader live here.
- `weight-loss-tracker.php` is the load order for all of the above — when tracing a feature, start there to see what's actually wired in.

### Naming conventions

- Functions: `ws_ls_*`. Constants: mostly `WE_LS_*`/`WS_LS_*` interchangeably (both are used — an existing inconsistency, not a typo to "fix" in unrelated code).
- Shortcodes are registered twice, under a legacy `wlt-*` alias and the current `wt-*` name, pointing at the same callback (e.g. `wlt-bmi` / `wt-bmi` → `ws_ls_shortcode_bmi`). Keep both when adding new shortcodes if precedent in the same file does.

### Data layer

Plugin data lives in dedicated custom tables (created in `includes/db.php::ws_ls_db_create_core_tables()`), not postmeta/usermeta: weight entries, targets, user preferences (height/gender/DOB/activity level/aim/...), per-user stats, a log table, and email templates. `includes/db.php` and `pro-features/db.php` hold the query functions (`ws_ls_db_*`); higher-level reads/writes go through wrappers like `ws_ls_user_preferences_get()` / `ws_ls_set_user_preferences()` (pro-features/functions.php, includes/db.php) rather than raw `$wpdb` calls.

Saving user preferences (`includes/ajax.php::ws_ls_save_preferences_callback`) builds a `$fields` array from POST, then `array_filter`s out any `NULL` entries before merging with the existing row (`wp_parse_args`) — **a field that is entirely absent from the POST body is left untouched, not cleared**. To actually blank a value you must submit it (e.g. as an empty string, which numeric fields like height cast to `0` via `ws_ls_height_validate()`), not simply omit it.

### Caching

`includes/caching.php` provides a per-user cache (`ws_ls_cache_user_get/set/delete`, keyed via `ws_ls_cache_generate_key_from_array`) that many shortcodes use to memoize output for a request/short TTL. `ws_ls_set_user_preferences()` calls `ws_ls_cache_user_delete()` after a successful save — if you add a new write path for user data, make sure it invalidates the same way or reads will serve stale cached output.

### Shortcode/UI structure

`[wt]` (`includes/shortcode-wt.php::ws_ls_shortcode_wt`) is the main tabbed dashboard (History/Add-Edit/Settings/Awards/Photos/Advanced/Messages, plus a "kiosk mode" variant for staff use) and takes a large `shortcode_atts()` config. Shared tab/component markup lives in `includes/components.php`; shared form-field rendering (`ws_ls_form_field_select`, `ws_ls_form_field_date`, etc.) lives in `includes/core-forms.php`. Narrower, single-purpose display shortcodes (`wt-bmi`, `wt-height`, `wt-previous-weight`, ...) live in `includes/shortcode-various.php` / `pro-features/shortcode-various.php` and typically just format one cached value.

### Front-end/AJAX

A single JS config object (built by `ws_ls_config_js()` in `includes/core.php`, localized as `ws_ls_config`) carries the AJAX URL, one shared nonce (`ws-ls-nonce`), and client-side validation copy. `assets/js/ws-ls.js`'s `ws_ls_post(action, data, callback)` posts to `admin-ajax.php`; `includes/ajax.php` (plus a few `pro-features/*.php` files) registers the `wp_ajax_*` handlers.

**Gotcha relevant to tests/automation:** on a successful "Save Settings" AJAX response, the JS does `window.location.replace(...)` itself (`ws_ls_user_preference_callback` in `ws-ls.js`) rather than resolving a promise the caller can await — it's not a normal form submission. Firing another navigation immediately after clicking "Save Settings" can race and abort that in-flight request before the server processes it, so the setting silently fails to save. See the `save_settings()` note below.

## Testing conventions (Playwright)

- `playwright/tests/weight-tracker.ts` exports a `WeightTracker` helper class wrapping common UI flows (`weight_add`, `target_set`, `settings_set_height`, ...) against `data-testid` attributes rendered by the PHP. Prefer adding a method here over duplicating raw locators in a new spec file.
- Any helper that clicks "Save Settings" must wait for the resulting reload (matching `?user-preference-saved=true`) before returning, per the AJAX gotcha above — don't just `.click()` and continue.
- **Tests must restore state.** Every test (or its fixture) should leave shared data back the way it found it — clear/reset whatever it added (weight entries, target, height, other preferences) rather than relying on the next test's `beforeEach` to clean up after it. Existing helpers like `weight_clear_all()`, `target_clear()` follow this already; keep new tests consistent with it, since the suite runs serially against one shared WordPress user's data.

## Workflow

Do work on a feature branch, not directly against `master`.
