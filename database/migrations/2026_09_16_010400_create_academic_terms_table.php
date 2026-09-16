<?php

use App\Enums\AcademicTermStatus;
use App\Enums\AcademicTermType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_terms', function (Blueprint $table) {
            $table->id();
            $table->string('academic_year', 9);
            $table->enum('term', AcademicTermType::values())->index();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', AcademicTermStatus::values())->index();
            $table->timestamps();

            $table->unique(['academic_year', 'term']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_terms');
    }
};
