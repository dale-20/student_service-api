# Student Service API Rules

These rules apply to every change in this Laravel application. The project uses Laravel 13, PHP 8.3 or newer, Pest 5, and Laravel Pint. Laravel Boost guidance is available under `.claude/skills/` for tools that support it.

## Required quality gate

- Run `composer quality` before considering a change complete.
- Run `composer lint` after changing PHP files, then rerun the quality gate.
- Use pnpm exclusively for Node/Vite dependencies and scripts. Do not create npm or Yarn lockfiles.
- Add or update tests whenever behavior changes or a defect is fixed.
- Never commit `.env`, credentials, runtime data, vendor dependencies, or generated caches.

## API architecture

- Keep controllers thin: accept a validated request, delegate non-trivial work, and return a response.
- Use Form Request classes for validation and request-level authorization. Consume only validated input.
- Use policies or gates for protected operations. Client-side checks never replace backend authorization.
- Return Eloquent models through API Resource classes. Keep response shapes stable and intentional.
- Place public JSON endpoints in `routes/api.php` under a versioned `/api/v1` contract unless the project explicitly adopts another versioning policy.
- Use correct HTTP status codes and Laravel's standard `422` validation response shape.
- Introduce action or service classes for multi-step business workflows, not for trivial CRUD solely to add layers.

## Data and performance

- Eager-load required relationships and avoid N+1 queries.
- Paginate collection endpoints; use cursor pagination for large sequential datasets when appropriate.
- Use transactions for operations that must succeed or fail as one unit.
- Add database constraints and indexes that enforce integrity and support actual query patterns.
- Queue slow, retryable, or external work. Jobs must be idempotent where retries are possible.
- Keep migrations reversible and safe for databases that already contain data.

## Security

- Treat all request data as untrusted. Validate types, formats, ranges, nested keys, and uploaded files.
- Use Sanctum's stateful cookie authentication for the first-party Nuxt application; do not issue first-party bearer tokens merely for convenience.
- Apply rate limits to authentication and expensive endpoints.
- Keep secrets in environment variables and read them through configuration files, never direct `env()` calls outside configuration.
- Avoid logging secrets, session identifiers, personal information, or full sensitive payloads.
- Keep `APP_DEBUG=false` in production and never expose exception traces through the API.

## Testing

- Prefer feature tests for endpoints, validation, authorization, persistence, resources, and side effects.
- Use unit tests for isolated domain logic with meaningful branching.
- Feature tests use `RefreshDatabase`; create data with factories rather than shared fixtures.
- Test success, unauthenticated, unauthorized, invalid, missing-resource, and relevant conflict paths.
- Fake queues, mail, notifications, storage, and external HTTP calls at the boundary, then assert the intended interaction.
- Do not delete or weaken a failing test to make a change pass.

## Style

- Follow existing Laravel conventions and use framework features before creating custom infrastructure.
- Use strict, meaningful types and explicit return types wherever practical.
- Use braces for control structures and descriptive names rather than abbreviations.
- Avoid comments that restate the code; document decisions and non-obvious constraints instead.
