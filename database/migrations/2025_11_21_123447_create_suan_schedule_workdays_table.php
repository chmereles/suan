<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('suan_schedule_workdays', function (Blueprint $table) {
            $table->id();

            $table->foreignId('schedule_id')
                ->constrained('suan_schedules')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('weekday'); // 0–6 (L–D)
            $table->unsignedTinyInteger('pattern_index')->nullable(); // rotativo
            $table->unsignedTinyInteger('segment_index')->default(1); // horario partido

            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            $table->unsignedTinyInteger('tolerance_in_minutes')->default(0);
            $table->unsignedTinyInteger('tolerance_out_minutes')->default(0);

            $table->boolean('is_working_day')->default(true);

            $table->timestamps();

            $table->index(['schedule_id', 'weekday', 'pattern_index', 'segment_index'], 'suan_schedule_workdays_schedule_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suan_schedule_workdays');
    }
};
