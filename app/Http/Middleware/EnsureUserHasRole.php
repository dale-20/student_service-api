<?php

namespace App\Http\Middleware;

use App\Enums\RoleSlug;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $allowed = array_map(static fn (string $role): RoleSlug => RoleSlug::from($role), $roles);
        abort_unless($request->user()?->hasRole(...$allowed), 403);

        return $next($request);
    }
}
