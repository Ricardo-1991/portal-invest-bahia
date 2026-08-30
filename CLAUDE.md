# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

PIB — Portal Invest Bahia: a multilingual (PT/EN/ES/IT) classifieds portal for real-estate
brokers listing rural properties (fazendas), assets, and services in Bahia. Public site +
admin panel (Filament).

**Stack:** Laravel 13 + Filament 5 (admin) · Blade + Tailwind v4 + Alpine.js (public site) ·
PostgreSQL · Docker via Laravel Sail (dev).

## Commands

All `artisan`/`composer`/`npm` commands must run inside Sail (the container has the PHP/Node
toolchain; the host may have none installed):

```bash
./vendor/bin/sail up -d              # start containers (app on :8080, db on :5433)
./vendor/bin/sail down                # stop containers
./vendor/bin/sail artisan tinker      # REPL
./vendor/bin/sail artisan test        # run the full test suite
./vendor/bin/sail artisan test --filter=test_name_or_class
./vendor/bin/sail artisan test tests/Feature/PublicSiteTest.php
./vendor/bin/sail composer require ...
./vendor/bin/sail npm run dev|build
```

If the container gets stuck restarting after a broken state, check
`docker logs portal-invest-bahia-laravel.test-1` and `docker restart portal-invest-bahia-laravel.test-1`
once the underlying issue is fixed.

If `sail` reports `Docker or Podman is not running`, the Docker context may be pointed at
Docker Desktop; switch back with `docker context use default`.

**Default dev credentials** (from `DatabaseSeeder`, overridable via `PIB_ADMIN_PASSWORD` /
`PIB_BROKER_PASSWORD` env vars — the seeder refuses to run in production with the default
`password`):
- Admin: `admin@pib.com.br` / `password` — `/admin`
- Broker: `corretor@pib.com.br` / `password`

Public site: `http://localhost:8080/{pt|en|es|it}`.

## Architecture

**Locale-prefixed routing.** All public routes live under `routes/web.php`'s
`Route::prefix('{locale}')` group, constrained to the keys of `config('pib.locales')` and
running through the `setlocale` middleware (`App\Http\Middleware\SetLocale`), which calls
`App::setLocale()` from the URL segment. `/` redirects to the browser's preferred locale (or
`pt`). There is a single controller, `App\Http\Controllers\PublicController`, handling
home/category/informacoes/contatos/show — the category action also serves an Alpine
live-search fragment when the `X-PIB-Partial: true` header is present, returning
`public.partials.category-results` instead of the full page.

**Translatable content via `spatie/laravel-translatable`**, not a Filament plugin — Filament 5
has no official translation plugin yet, so language tabs are hand-built. `App\Filament\Support\LocaleTabs`
generates one `Tab` per locale (`pt`/`en`/`es`/`it`) with nested state paths (e.g. `title.pt`);
`App\Filament\Concerns\LoadsTranslations` fills the edit form with full translation arrays via
`getTranslations()`; Eloquent saves them back automatically through `$translatable`. Fallback
behavior (visitor's locale → `pt` → any filled locale) is configured once in
`AppServiceProvider::boot()` via `Translatable::fallback(fallbackAny: true)`. Applies to
`Listing` (title/subtitle/description), `Event` (title/subtitle/description), and
`PageContent` (title/body — one row per fixed page key, see `PageContent::KEYS`).

**Ownership/authorization pattern**, consistent across both listings and events: a broker (role
`broker`) only sees/edits their own records; an admin (role `admin`) sees everything. This is
enforced in three places that must stay in sync when touched:
1. `App\Filament\Resources\Listings\ListingResource::getEloquentQuery()` (and the Events
   equivalent) scopes the admin table listing by `user_id`.
2. `App\Policies\ListingPolicy` / `EventPolicy` gate `view`/`update`/`delete`.
3. `App\Filament\Concerns\GuardsListingPublication` / `EnforcesEventOwnership` traits (used in
   the resources' Create/Edit pages) force `user_id` to the authenticated broker on save — a
   hidden form field is not a security boundary, since Filament doesn't stop a hidden field's
   value from being submitted in the payload.

`GuardsListingPublication` additionally blocks publishing (`status = published`) unless title
+ description are both filled in *at least one* locale (`Halt` + a Filament notification) —
drafts are exempt.

**Media** always goes through `spatie/laravel-medialibrary` with an explicit `->useDisk('public')`
on every collection (`Listing::main`/`gallery`, `Event::image`). Without the explicit disk,
Filament falls back to `filament.default_filesystem_disk` (`local`, private) and uploaded
images become unreachable from the public site — this bit has already caused a live bug once,
so don't drop the disk on a new collection.

**Config lives in `config/pib.php`**: supported locales, category labels, and seeder
credentials. `TRUSTED_PROXIES` is read directly via `env()` in `bootstrap/app.php` rather than
through `config()`, because config isn't available yet at that point in the boot sequence.

## Testing

PHPUnit (not Pest). `RefreshDatabase` trait, Postgres `testing` DB. Feature tests exercise
real HTTP requests against the locale-prefixed routes rather than mocking. See
`tests/Feature/PublicSiteTest.php` for the established pattern of building a published
`Listing` fixture (with a role-assigned broker) and asserting on rendered Blade output.

## Deploy

`compose.prod.yaml` + `docker/production/` — a self-sufficient multi-stage build (Nginx +
PHP-FPM + Postgres) unlike the dev `compose.yaml`, which mounts `vendor/`/`node_modules/` from
the host instead of baking them into the image. See `docker/production/README.md` for the full
deploy walkthrough.
