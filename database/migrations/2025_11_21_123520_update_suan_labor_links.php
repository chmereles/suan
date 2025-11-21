<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('suan_labor_links', function (Blueprint $table) {
            $table->foreignId('schedule_id')
                ->nullable()
                ->after('active')
                ->constrained('suan_schedules');

            $table->date('schedule_rotation_start_date')
                ->nullable()
                ->after('schedule_id');
        });
    }

    public function down(): void
    {
        Schema::table('suan_labor_links', function (Blueprint $table) {
            $table->dropConstrainedForeignId('schedule_id');
            $table->dropColumn('schedule_rotation_start_date');
        });
    }
};

