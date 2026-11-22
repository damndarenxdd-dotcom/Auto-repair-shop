# Auto Repair Shop - Project Structure Guide

This project is a Laravel app. To keep it easy to read and maintain, all app code should stay inside the `autorepairshop/` folder.

## Main Folder Layout

```text
auto-repair-shop/
├── .vscode/                   # VS Code settings
├── autorepairshop/            # Laravel application root
│   ├── app/                   # Business logic and application code
│   │   ├── Http/
│   │   │   ├── Controllers/   # Request handlers
│   │   │   ├── Middleware/    # Route/request filters
│   │   │   └── Resources/    # API resource classes
│   │   ├── Jobs/             # Background jobs
│   │   ├── Models/           # Eloquent models
│   │   ├── Policies/        # Authorization rules
│   │   └── Providers/       # App service providers
│   ├── bootstrap/            # Laravel bootstrap files
│   ├── config/               # App configuration files
│   ├── database/
│   │   ├── factories/        # Model factories
│   │   ├── migrations/       # Database schema files
│   │   └── seeders/          # Data seeding logic
│   ├── public/               # Public entry point and built assets
│   ├── resources/
│   │   ├── css/               # CSS files
│   │   ├── js/               # JS front-end files
│   │   └── views/            # Blade templates
│   ├── routes/               # Route definitions
│   │   ├── api.php
│   │   ├── auth.php
│   │   ├── console.php
│   │   └── web.php
│   ├── storage/              # Logs, cache, compiled files
│   ├── tests/                # Automated tests
│   ├── vendor/               # Composer dependencies (generated)
│   ├── .env                  # Local environment config
│   ├── .env.example         # Example environment file
│   ├── artisan               # Laravel CLI entry point
│   ├── composer.json         # PHP dependencies
│   ├── package.json          # JS/Vite dependencies
│   ├── phpunit.xml           # PHPUnit config
│   ├── README.md             # Project overview
│   ├── vite.config.js        # Vite build config
│   └── PROJECT_STRUCTURE.md # This guide
├── composer.json             # Root-level dependency file (if used by workspace)
├── composer.lock
├── vendor/                   # Root-level vendor folder (if present)
├── tempCodeRunnerFile.python
└── README.md                # Optional workspace-level note
```

## Where to Put Code

### Backend logic
- Controllers: `app/Http/Controllers/`
- Models: `app/Models/`
- Policies: `app/Policies/`
- Jobs: `app/Jobs/`
- Providers: `app/Providers/`
- Middleware: `app/Http/Middleware/`

### Routes
- Web routes: `routes/web.php`
- API routes: `routes/api.php`
- Auth routes: `routes/auth.php`
- Console routes: `routes/console.php`

### Database
- Migrations: `database/migrations/`
- Seeders: `database/seeders/`
- Factories: `database/factories/`

### Frontend/UI
- CSS: `resources/css/`
- JS: `resources/js/`
- Blade views: `resources/views/`

### Configuration
- App config: `config/`
- Local environment: `.env`
- Example env: `.env.example`

### Tests
- Feature tests: `tests/Feature/`
- Unit tests: `tests/Unit/`

## Check Rules for Good Organization

1. Do not place Laravel app code outside `autorepairshop/`.
2. Keep each feature grouped by responsibility.
3. Put business rules in `app/`, not in routes or views.
4. Keep database schema changes in `database/migrations/`.
5. Put UI code in `resources/` and keep controllers thin.
6. Keep environment variables in `.env` only; do not hardcode API keys in code.
7. Use `tests/` for verification of each feature.

## Quick Project Map

If you are checking the app, start here:

- App logic: `autorepairshop/app/`
- Routes: `autorepairshop/routes/`
- Views: `autorepairshop/resources/views/`
- Database: `autorepairshop/database/`
- Config: `autorepairshop/config/`
- Env file: `autorepairshop/.env`
- Public entry: `autorepairshop/public/`
- Tests: `autorepairshop/tests/`

## Recommended Practice

When creating a new feature, follow this pattern:

```text
FeatureName/
├── Model
├── Controller
├── Policy
├── Request
├── migration
├── view files
└── tests
```

This keeps the project clean and predictable.
