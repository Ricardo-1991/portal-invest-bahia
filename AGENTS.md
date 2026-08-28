# Repository Guidelines

## Project Structure & Module Organization

PIB is a Laravel 13 multilingual classifieds portal. Application code lives in
`app/`: public routes use `Http/Controllers/PublicController.php`, while the
Filament admin panel is organized under `app/Filament/Resources/`. Keep models,
policies, and reusable support code in `app/Models`, `app/Policies`, and
`app/Support` respectively. Public Blade pages and partials are in
`resources/views/public/`; frontend entry points and styles are in
`resources/js/app.js` and `resources/css/app.css`. Put schema changes in
`database/migrations/` and matching test coverage in `tests/Feature/` or
`tests/Unit/`.

## Build, Test, and Development Commands

Use Laravel Sail so PHP, Composer, and Node run inside the project containers:

```bash
./vendor/bin/sail up -d                  # start app and PostgreSQL
./vendor/bin/sail artisan migrate --seed # apply schema and development data
./vendor/bin/sail npm run dev            # run Vite with hot reload
./vendor/bin/sail npm run build          # generate production assets
./vendor/bin/sail artisan test           # run the PHPUnit suite
./vendor/bin/sail composer test          # clear config, then run tests
./vendor/bin/sail artisan pint           # format PHP changes
```

The public site runs at `http://localhost:8080/{pt|en|es|it}`; admin is
available at `/admin`.

## Coding Style & Naming Conventions

Follow Laravel and PSR-12 conventions: four-space PHP indentation, one class
per PascalCase file, camelCase methods and variables, and `snake_case` database
columns. Use Laravel Pint before committing PHP code. Name migrations with the
generated timestamp plus a clear action, such as
`2026_08_27_120000_add_slug_to_listings_table.php`. Keep Blade components
small and use kebab-case filenames (for example, `listing-card.blade.php`).

Public routes are locale-prefixed. When adding visitor-facing text or fields,
preserve translations for `pt`, `en`, `es`, and `it`, plus the existing
fallback behavior. Authorization changes for listings/events must retain both
policy checks and broker ownership scoping. Media collections must explicitly
use the `public` disk so uploads remain accessible through `storage:link`.

## Testing Guidelines

The suite uses PHPUnit, with `RefreshDatabase` in feature tests and a PostgreSQL
`testing` database. Name tests descriptively, e.g.
`broker_cannot_edit_another_brokers_listing`. Exercise requests, policies, and
published-content behavior in `tests/Feature/`; reserve `tests/Unit/` for
isolated logic. Run the affected test file while developing, then run the full
suite before opening a PR:

```bash
./vendor/bin/sail artisan test tests/Feature/PublicSiteTest.php
```

## Commits & Pull Requests

Recent commits use concise Conventional Commit prefixes, such as `feat:`,
`fix:`, and `refactor:`. Use an imperative, scoped summary: `fix: prevent
untranslated listings from publishing`. PRs should explain the user-visible
change, list migrations/configuration steps, link the relevant issue, and
include screenshots for public-site or Filament UI changes. Do not commit
`.env`, credentials, generated storage files, or build dependencies.
