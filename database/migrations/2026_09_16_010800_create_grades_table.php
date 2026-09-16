<?php

use App\Enums\GradeRemark;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('midterm_grade', 4, 2)->unsigned()->nullable();
            $table->decimal('final_grade', 4, 2)->unsigned()->nullable();
            $table->enum('remarks', GradeRemark::values())->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
