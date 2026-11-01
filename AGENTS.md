# AGENTS.md — Silsilah Genealogy Application

> **Purpose:** Context for AI agents and human contributors working on this Laravel 8 genealogy project. Covers architecture, conventions, test strategy, and common workflows. Keep this file up to date as the codebase evolves.

---

## 1. Project Overview

| Item | Detail |
|------|--------|
| **Name** | Silsilah (Genealogy / Family Tree) |
| **Framework** | Laravel 8.x (PHP 7.3+ / 8.0 compatible) |
| **Database** | MySQL/MariaDB (production) · SQLite `:memory:` (tests) |
| **Frontend** | Blade + Bootstrap 3 (via `laravel-mix`) · Leaflet maps for cemetery locations |
| **Auth** | Laravel built-in (`Auth::routes`), registration **disabled** in `routes/web.php` |
| **Admin** | Email-based allow-list in `SYSTEM_ADMIN_EMAILS` env var |
| **UUIDs** | All primary keys are UUID strings (Ramsey/UUID) — no auto-increment |
| **Lang** | EN / ID via `config/app.locale` (middleware `App\Http\Middleware\Lang`) |
| **Timezone** | Config-driven, default UTC |

---

## 2. Repository Layout (Key Directories)

```
app/
├── Console/Kernel.php                # Scheduled commands (currently empty)
├── Exceptions/Handler.php            # Global exception rendering
├── Helpers/functions.php             # Global helpers: formatSizeUnits, userPhoto*, is_system_admin
├── Http/
│   ├── Controllers/
│   │   ├── Auth/                     # Login, Password reset, Change password
│   │   ├── BackupsController.php     # Admin: DB backup/upload/restore/download
│   │   ├── BirthdayController.php    # Upcoming birthdays list
│   │   ├── CouplesController.php     # Marriage CRUD
│   │   ├── FamilyActionsController.php  # Core genealogy actions (set parent, add spouse/child)
│   │   ├── HomeController.php        # Dashboard / profile redirect
│   │   ├── UserMarriagesController.php
│   │   └── UsersController.php       # Search, show, edit, update, delete, chart, tree, death, photo
│   ├── Middleware/
│   │   ├── AdminOnly.php             # Gates admin routes via is_system_admin()
│   │   ├── Lang.php                  # Sets locale from config/session
│   │   └── (Laravel defaults)
│   ├── Requests/
│   │   ├── BackupUploadRequest.php
│   │   └── Users/UpdateRequest.php, UpdatePasswordRequest.php
│   ├── Kernel.php                    # Middleware registration + route middleware (auth, admin, etc.)
│   └── ...
├── Jobs/Users/DeleteAndReplaceUser.php  # Queued job for safe user replacement on delete
├── Models (root of app/)
│   ├── User.php                      # Core entity — self-referential relations, metadata, age/birthday accessors
│   ├── Couple.php                    # Marriage join entity (husband_id, wife_id, marriage_date, manager_id)
│   ├── CouplePivot.php               # Pivot with UUID id cast
│   ├── UserMetadata.php              # EAV table for cemetery lat/lng, etc.
│   └── ...
├── Policies/
│   ├── UserPolicy.php                # edit/delete based on ownership / manager / admin
│   └── CouplePolicy.php              # edit based on manager / admin
├── Providers/                        # Standard Laravel providers
├── Rules/RecaptchaV3.php             # Google reCAPTCHA v3 validation rule
├── View/Components/RecaptchaV3.php   # Blade component for reCAPTCHA
└── ...

database/
├── migrations/                       # 4 migrations: users, password_resets, couples, user_metadata
├── factories/ModelFactory.php        # Faker definitions for User
└── seeds/DatabaseSeeder.php          # Empty (no default data)

tests/
├── CreatesApplication.php            # Boots Laravel with sqlite :memory:, Hash::setRounds(4)
├── TestCase.php                      # Base class + loginAsUser() helper
├── Feature/                          # BrowserKit functional tests
│   ├── ChangePasswordTest.php
│   ├── EditMarriagesTest.php
│   ├── ManageUserFamiliesTest.php    # Heavy: family tree CRUD flows
│   ├── ManageUserMarriagesTest.php
│   ├── UsersDeletionTest.php         # Delete + replace logic
│   └── UsersProfileTest.php
└── Unit/
    ├── CoupleTest.php
    ├── PersonRelationsTest.php       # Siblings, parent/child logic
    ├── Policies/                     # Policy unit tests
    └── UserTest.php                  # Model accessors, relationships

resources/
├── assets/js/app.js, sass/app.scss   # Webpack entry points (Bootstrap 3, jQuery)
├── lang/{en,id}/*.php                # Translation strings
└── views/                            # Blade templates (users.*, couples.*, auth.*, etc.)

config/
├── leaflet.php                       # Map zoom/center from env
├── app.php                           # system_admin_emails from env
└── (standard Laravel configs)
```

---

## 3. Domain Model & Relationships

### Core Entities

| Model | Table | Key | Description |
|-------|-------|-----|-------------|
| `User` | `users` | UUID string | Person node — gender (1=male, 2=female), dates (dob/yob, dod/yod), address, contact, photo |
| `Couple` | `couples` | UUID string | Marriage record linking husband + wife + marriage_date + manager_id |
| `UserMetadata` | `user_metadata` | UUID string | EAV for cemetery location (name, address, lat, lng) |

### User Self-Referential Relations

```php
// Direct parent links
$user->father()   // belongsTo User (father_id)
$user->mother()   // belongsTo User (mother_id)

// Children
$user->childs()   // hasMany User (father_id OR mother_id depending on gender)

// Spouses (many-to-many via couples table)
$user->wifes()     // male → wives
$user->husbands()  // female → husbands
$user->couples()   // unified accessor (gender-aware)

// Marriage records (hasMany Couple)
$user->marriages()

// Siblings (shared father/mother/parent_id)
$user->siblings() → Collection

// Manager (who created/manages this record)
$user->manager()        // belongsTo User
$user->managedUsers()   // hasMany User
$user->managedCouples() // hasMany Couple
```

### Couple Relations

```php
$couple->husband()  // belongsTo User
$couple->wife()     // belongsTo User
$couple->childs()   // hasMany User (parent_id)
$couple->addChild(User $user)  // sets parent_id + father_id + mother_id atomically
```

### Key Business Rules (enforced in controllers/models)

- **Gender constraints:** `setFather` requires `gender_id=1`; `setMother` requires `gender_id=2`
- **Marriage constraints (TODO in code):** Male max 4 concurrent wives; Female max 1 concurrent husband; Widow auto-sets divorce date
- **UUID generation:** All new models get `Ramsey\Uuid\Uuid::uuid4()->toString()` in controller actions
- **Soft delete not used** — hard delete with optional *replace* (see `DeleteAndReplaceUser` job)

---

## 4. Routing & Middleware

| Route Group | Middleware | Notable Routes |
|-------------|------------|----------------|
| **All web** | `web` (EncryptCookies, StartSession, VerifyCsrfToken, **Lang**) | — |
| **Authenticated** | `auth` | All genealogy CRUD (`/family-actions/*`, `/users/*`, `/couples/*`, `/birthdays`) |
| **Admin** | `admin` (`AdminOnly`) | `/backups/*` (resource + upload/restore/download) |
| **Guest** | `guest` | `/login`, `/register` (**disabled**), password reset |

**Auth routes** (`Auth::routes(['register' => false])`) — registration disabled intentionally.

**Route naming convention:** `users.show`, `users.edit`, `family-actions.set-father`, `couples.update`, etc.

---

## 5. Testing Strategy

| Layer | Tool | Config | Notes |
|-------|------|--------|-------|
| **Unit** | PHPUnit 9 | `phpunit.xml` → `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:` | Model accessors, relationships, policies |
| **Feature** | Laravel BrowserKitTesting | Same in-memory SQLite | Full HTTP request/response cycle, auth via `loginAsUser()` |
| **Coverage** | php-coveralls (Travis) | `vendor/bin/phpunit --coverage-clover` | Runs on CI only |

### Running Tests Locally

```bash
# Host (requires PHP + composer deps)
vendor/bin/phpunit

# Docker (recommended — matches CI exactly)
docker build -t silsilah-dev .
docker run --rm -it -v "$(pwd):/app" silsilah-dev \
  sh -c "composer install --no-interaction --no-scripts && vendor/bin/phpunit"
```

**No `.env` or database setup needed** — `phpunit.xml` forces sqlite `:memory:`.

or with Docker and Bash available, simply run `./test.sh`

### Test Helpers

- `Tests\TestCase::loginAsUser(array $overrides = [])` → creates a user with UUID, sets `manager_id = own id`, acts as that user.
- `CreatesApplication` trait boots the app with `Hash::setRounds(4)` for fast hashing in tests.

---

## 6. Common Development Workflows

### 6.1 Local Development (without Docker)

```bash
composer install
cp .env.example .env
# Edit .env → DB_CONNECTION=mysql, credentials, APP_KEY, SYSTEM_ADMIN_EMAILS
php artisan key:generate
php artisan migrate
php artisan storage:link
npm install && npm run dev     # or npm run watch
php artisan serve
```

### 6.2 Adding a Migration

```bash
php artisan make:migration descriptive_name --create=table_name
# Edit migration
php artisan migrate
# **Commit the migration file** — schema changes go directly into migrations (see README)
```

### 6.3 Adding a Feature Test

1. Create `tests/Feature/NewFeatureTest.php` extending `Tests\TestCase`
2. Use `$this->loginAsUser()` to authenticate
3. Use `$this->get()`, `$this->post()`, `$this->assertSee()`, etc.
4. Run `vendor/bin/phpunit --filter NewFeatureTest`

### 6.4 Adding a Policy

1. `php artisan make:policy NewPolicy --model=App\Models\Something`
2. Register in `AuthServiceProvider::$policies`
3. Use `$this->authorize('action', $model)` in controllers
4. Add unit tests in `tests/Unit/Policies/`

### 6.5 Adding a Global Helper

Edit `app/Helpers/functions.php` — auto-loaded via `AppServiceProvider::boot()`.

---

## 7. Configuration Reference

### Required `.env` Variables

| Variable | Default | Description |
|----------|---------|-------------|
| `APP_KEY` | — | **Required** — `php artisan key:generate` |
| `DB_CONNECTION` | `mysql` | `mysql` \| `sqlite` |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | — | MySQL credentials |
| `SYSTEM_ADMIN_EMAILS` | `''` | Semicolon-separated admin emails (grants `/backups/*` access) |
| `LEAFLET_MAP_ZOOM_LEVEL` | `4` | Default map zoom |
| `LEAFLET_MAP_DETAIL_ZOOM_LEVEL` | `18` | Cemetery detail zoom |
| `LEAFLET_MAP_CENTER_LATITUDE` | `-0.87887` | Default map center (Indonesia) |
| `LEAFLET_MAP_CENTER_LONGITUDE` | `117.4863` | Default map center |
| `RECAPTCHA_SITE_KEY`, `RECAPTCHA_SECRET_KEY` | `''` | Optional Google reCAPTCHA v3 |

### Config Files of Interest

- `config/leaflet.php` — reads the four LEAFLET\_* env vars
- `config/app.php` → `system_admin_emails` → reads `SYSTEM_ADMIN_EMAILS`
- `config/auth.php` — standard, uses `users` table with UUID PK

---

## 8. Docker / Deployment

### Dockerfile (multi-stage)

```dockerfile
FROM node:16-buster AS node
FROM php:7.4-fpm
# Copies node, installs PHP extensions (pdo_mysql, mbstring, gd, zip, bcmath, etc.)
# Copies composer binary
EXPOSE 9000
```

**No `docker-compose.yml` exists** — single container for PHP-FPM. For production you would add nginx + mysql containers.

### Heroku / Procfile

```
web: vendor/bin/heroku-php-apache2 public/
```

### Build Assets for Production

```bash
npm run prod   # runs webpack production build via laravel-mix
```

---

## 9. Conventions & Gotchas

| Area | Convention |
|------|------------|
| **Primary Keys** | Always UUID string (`$incrementing=false`, `$keyType='string'`) |
| **Mass Assignment** | Use `$fillable` on all models; controllers use FormRequest validation |
| **Authorization** | Policies + `authorize()` in controllers; `is_system_admin()` bypasses ownership |
| **Dates** | Store `dob` (DATE), `yob` (YEAR only), `dod`, `yod` — accessors compute age |
| **Metadata** | EAV via `UserMetadata` — keys listed in `User::METADATA_KEYS` |
| **File Uploads** | `photo_upload` stores to `storage/app/public/images` → `php artisan storage:link` |
| **Translations** | `resources/lang/{en,id}/*.php` — use `__('key')` or `trans('key')` |
| **Timezone** | `config/app.timezone` — default UTC; user-facing dates via Carbon |
| **CSRF** | Enabled globally; AJAX must send `X-CSRF-TOKEN` header |
| **Registration** | Disabled — only existing users can add family members |

---

## 10. Extending the Application (Common Patterns)

### Add a New Family Relation Type

1. Add column(s) to `users` table via migration
2. Add relationship methods to `User.php` (mirror `father()`/`mother()`)
3. Add controller action in `FamilyActionsController`
4. Add route in `routes/web.php` (POST under `auth` middleware)
5. Add validation + tests

### Add Metadata Field

1. Add key to `User::METADATA_KEYS`
2. Include in `UpdateRequest` validation rules
3. Controller `updateUserMetadata()` auto-handles persistence

### Add Admin-Only Feature

1. Create controller
2. Wrap routes in `Route::group(['middleware' => 'admin'])`
3. `AdminOnly` middleware checks `is_system_admin(auth()->user())`

---

## 11. Running Tests (vendor/bin/phpunit)

### Local Development (without Docker)

```bash
# First time setup (once)
composer install
# .env optional for tests (phpunit.xml forces sqlite :memory:)

# Run all tests
vendor/bin/phpunit

# Run specific test suites
vendor/bin/phpunit --testsuite Feature    # Functional (BrowserKit)
vendor/bin/phpunit --testsuite Unit        # Model/Policy tests

# Run single file
vendor/bin/phpunit tests/Feature/UsersProfileTest.php

# Run with verbose output
vendor/bin/phpunit --verbose

# Run with coverage (requires php-coveralls)
vendor/bin/phpunit --coverage-clover build/logs/clover.xml
```

### Docker (recommended)

```bash
# Build once (multi-stage: node + php-fpm with GD, pdo_mysql, etc.)
docker build -t silsilah-dev .

# Run tests in a clean container
# -v "$(pwd):/app" syncs source code, avoiding a fresh build each run
# composer install --no-interaction --no-scripts builds vendor
# vendor/bin/phpunit uses phpunit.xml (sqlite :memory:, Hash::setRounds(4))
docker run --rm -it \
  --name silsilah-test \
  -v "$(pwd):/app" \
  silsilah-dev \
  sh -c "composer install --no-interaction --no-scripts && vendor/bin/phpunit"

# Interactive shell inside built container (for debugging)
docker run -it --name silsilah-debug -v "$(pwd):/app" silsilah-dev bash
# Inside container:
#   php artisan --version           # Verify PHP/fpm
#   composer list --format=json     # Verify dev deps
#   vendor/bin/phpunit --filter MyTest  # Run tests
```

### Dockerfile Notes

- **Base Image:** `php:7.4-fpm` + `node:16-buster` stages
- **Dependencies:** GD, pdo_mysql, mbstring, bcmath, zip, onig, libpng, jpegoptim, optipng, etc.
- **No MySQL container:** `phpunit.xml` overrides DB settings to use `:memory:` SQLite, so you never need MySQL for testing.
- **No `.env` needed** in Docker because tests don't use Laravel env vars (password hashing rounds forced to 4).

---

## 12. Troubleshooting Quick Reference

| Symptom | Likely Cause | Fix |
|---------|--------------|-----|
| `vendor/bin/phpunit` fails with DB error | Forgot to run `composer install` | `composer install --no-interaction` |
| Tests pass locally but fail in Docker | PHP version mismatch (host ≠ 7.4) | Use Docker command above |
| "Call to undefined function is_system_admin()" | Helper not loaded | Ensure `AppServiceProvider::boot()` has `require_once app_path().'/Helpers/functions.php';` |
| UUID foreign key constraint fails | Migration order / missing index | Check migration `couples` table has `husband_id`, `wife_id` indexed |
| Map not showing | Leaflet JS/CSS not compiled | `npm run dev` + check `webpack.mix.js` copies bootstrap fonts |

---

## 13. Useful Commands Cheat Sheet

```bash
# Tests
vendor/bin/phpunit                    # All tests
vendor/bin/phpunit --filter UserTest  # Single test class
vendor/bin/phpunit --testdox          # Readable output

# Database
php artisan migrate                   # Run migrations
php artisan migrate:fresh --seed      # Reset + seed (no seeders yet)
php artisan make:migration name       # New migration

# Cache / Config
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# Assets
npm run dev        # Development build (with sourcemaps)
npm run watch      # Watch + rebuild
npm run prod       # Production build (minified)

# Queues / Jobs (if using queue worker)
php artisan queue:work

# Docker
docker build -t silsilah-dev .
docker run --rm -it -v "$(pwd):/app" silsilah-dev bash   # Interactive shell
```

---

## 14. Links & References

- **Upstream Laravel 8 Docs:** https://laravel.com/docs/8.x
- **Original Repo:** https://github.com/nafiesl/silsilah
- **Travis CI:** https://travis-ci.org/nafiesl/silsilah
- **Coveralls:** https://coveralls.io/github/nafiesl/silsilah
- **Horizontal Family Tree CSS (used in UI):** https://codepen.io/P233/pen/Kzbsi

---

**Generated on 2026-11-01 by Claude Code**  
The agent cleaned up unused imports (`use Log`, `use Closure`) in the edited files. All .md file sections are correctly numbered from 1 to 14.

*Generated by analyzing the codebase on 2026-11-01. Update this file when architecture, conventions, or workflows change.*