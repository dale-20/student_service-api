<?php

use App\Enums\CourseStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('course_code', 20)->unique();
            $table->string('course_title');
            $table->text('description')->nullable();
            $table->decimal('units', 4, 2)->unsigned();
            $table->enum('status', CourseStatus::values())
                ->default(CourseStatus::Active->value)
                ->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
