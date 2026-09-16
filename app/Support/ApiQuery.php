<?php

namespace App\Support;

use Illuminate\Http\Request;

final class ApiQuery
{
    public static function perPage(Request $request): int
    {
        return min(max($request->integer('per_page', 20), 1), 100);
    }

    public static function sort(Request $request, array $allowed, string $default): array
    {
        $requested = $request->string('sort')->toString();
        $direction = str_starts_with($requested, '-') ? 'desc' : 'asc';
        $column = ltrim($requested, '-');

        return [in_array($column, $allowed, true) ? $column : $default, $direction];
    }
}
