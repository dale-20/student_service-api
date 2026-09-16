# Backend development standards

## Setup

```powershell
composer install
pnpm install --frozen-lockfile
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate
```

Use the local `.env` only for environment configuration. Never commit credentials or production values.

## Quality commands

```powershell
composer lint:test
composer test
```

Run the complete required gate with:

```powershell
composer quality
```

Use `composer lint` to apply Laravel Pint formatting. The parent repository's CI must install Composer dependencies, run `pnpm install --frozen-lockfile`, and then run `composer quality` from this directory. pnpm is the only supported JavaScript package manager.

## Definition of done

A backend change is complete only when:

- Pint and all Pest tests pass;
- changed behavior has feature or unit coverage at the appropriate boundary;
- input is validated through a Form Request;
- protected operations are authorized through policies or gates;
- JSON output uses an API Resource rather than exposing a model directly;
- multi-record writes are transactional;
- collection endpoints are paginated and relationships are eager-loaded;
- slow or retryable work is queued when appropriate;
- migrations are reversible and safe for existing data; and
- configuration contains no hard-coded environment values or secrets.

See `AGENTS.md` for the architecture and implementation rules that apply to every change.
