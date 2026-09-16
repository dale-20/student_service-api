<?php

use App\Enums\DayOfWeek;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_offering_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_offering_id')->constrained()->cascadeOnDelete();
            $table->enum('day_of_week', DayOfWeek::values())->index();
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room', 50)->nullable();
            $table->timestamps();

            $table->unique([
                'course_offering_id',
                'day_of_week',
                'start_time',
                'end_time',
            ], 'offering_schedule_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_offering_schedules');
    }
};
