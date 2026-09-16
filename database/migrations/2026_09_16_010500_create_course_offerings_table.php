<?php

use App\Enums\CourseOfferingStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->foreignId('academic_term_id')->constrained()->restrictOnDelete();
            $table->foreignId('instructor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('section', 50)->index();
            $table->string('room', 50)->nullable();
            $table->string('schedule')->nullable();
            $table->unsignedSmallInteger('capacity');
            $table->enum('status', CourseOfferingStatus::values())->index();
            $table->timestamps();

            $table->unique(['course_id', 'academic_term_id', 'section']);
            $table->index('academic_term_id');
            $table->index('instructor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_offerings');
    }
};
