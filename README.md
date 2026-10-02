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

## Soft deletion and recovery

Run `php artisan migrate` after updating the application. The soft-delete migration adds indexed `deleted_at` columns without removing records.

- `DELETE /api/v1/{type}/{id}` moves a record to Deleted records.
- `GET /api/v1/recycle-bin?type=students&search=...&page=1` lists deleted records with pagination.
- `POST /api/v1/recycle-bin/{type}/{id}/restore` recovers the original record.

Supported types: `users`, `students`, `programs`, `courses`, `academic-terms`, `course-offerings`, `enrollments`, `curriculum`, and `schedules`. User operations require an administrator; other recovery operations allow administrators and registrars. Regular lists and mutations exclude deleted records. Unique identifiers remain reserved so recovery does not require changing record identities.

Grades have no delete or recovery endpoints. Enrollments with grade records cannot be deleted or reassigned. Parent deletion is rejected with `409` when undeleted dependent academic records exist. Recover a deleted parent before its children. Recovering or reactivating an enrollment rechecks capacity, student status, and offering availability.

`PUT /api/v1/enrollments/{id}` accepts `student_id`, `course_offering_id`, `enrollment_date`, and `status`. The existing status endpoint also supports corrections back to `enrolled`. Enrollment history prevents changing an offering's course or academic term.

Curriculum synchronization preserves row IDs and soft-deletes removed entries. Its response field is `curriculum`; semesters are `first`, `second`, or `summer`, and `is_required` supports optional courses. Schedule synchronization also preserves existing IDs and makes removed schedules recoverable.
