<?php

use App\Enums\StudentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->foreignId('program_id')->constrained()->restrictOnDelete();
            $table->string('student_number', 20)->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name')->index();
            $table->string('suffix', 20)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('contact_number', 30)->nullable();
            $table->text('address')->nullable();
            $table->unsignedTinyInteger('year_level')->index();
            $table->enum('status', StudentStatus::values())
                ->default(StudentStatus::Active->value)
                ->index();
            $table->timestamps();

            $table->index(['program_id', 'year_level', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
