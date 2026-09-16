# Student Service API

The Laravel 13 JSON API for the Student Service application. The Nuxt frontend is maintained separately in `../student_service`.

Laravel is the source of truth for data, validation, authentication, authorization, and business rules.

## Local development

```powershell
composer install
pnpm install --frozen-lockfile
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Set `FRONTEND_URLS` and `SANCTUM_STATEFUL_DOMAINS` to the exact Nuxt origins. The API uses credentialed first-party Sanctum session cookies and is exposed under `/api/v1`.

## Development database

The database is reproducible from migrations, factories, and seeders:

```powershell
php artisan migrate:fresh --seed
```

The seed data includes roles, users, programs, students, curricula, courses, academic terms, course offerings, structured schedules, enrollments, and grades.

Development-only accounts all use the password `password`:

| Role | Email |
| --- | --- |
| Administrator | `admin@example.com` |
| Registrar / Staff | `registrar@example.com` |
| Instructor | `instructor1@example.com` |
| Instructor | `instructor2@example.com` |
| Student | `student@example.com` |

These credentials are for local demonstration only and must not be used in production.

## Quality gate

```powershell
composer quality
```

This checks Laravel Pint formatting and runs the Pest test suite, including architecture and security rules. All checks must pass before a change is complete.

Development conventions and the definition of done are documented in [CONTRIBUTING.md](CONTRIBUTING.md). Rules for coding agents are in [AGENTS.md](AGENTS.md).
