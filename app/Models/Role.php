<?php

namespace App\Models;

use App\Enums\RoleSlug;
use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description'])]
class Role extends Model
{
    /** @use HasFactory<RoleFactory> */
    use HasFactory;

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    protected function casts(): array
    {
        return [
            'slug' => RoleSlug::class,
        ];
    }
}
