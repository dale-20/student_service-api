<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = ['users', 'students', 'programs', 'courses', 'academic_terms', 'course_offerings', 'enrollments', 'program_courses', 'course_offering_schedules'];

    public function up(): void
    {
        foreach ($this->tables as $name) {
            Schema::table($name, function (Blueprint $table): void {
                $table->softDeletes()->index();
            });
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->tables) as $name) {
            Schema::table($name, function (Blueprint $table): void {
                $table->dropSoftDeletes();
            });
        }
    }
};
