<?php

use App\Enums\Semester;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->restrictOnDelete();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('year_level')->nullable()->index();
            $table->enum('semester', Semester::values())->nullable()->index();
            $table->boolean('is_required')->default(true);
            $table->timestamps();

            $table->unique(['program_id', 'course_id']);
            $table->index('course_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_courses');
    }
};
