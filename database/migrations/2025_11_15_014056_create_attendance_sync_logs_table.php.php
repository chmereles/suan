<?php

// database/migrations/2025_11_14_000000_create_attendance_sync_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_sync_logs', function (Blueprint $table) {
            $table->id();

            $table->string('source')->default('crosschex'); // futuro: otras fuentes
            $table->string('triggered_by')->default('cron'); // 'cron' | 'manual' | 'other'

            $table->unsignedInteger('window_minutes')->nullable();
            $table->unsignedInteger('inserted_count')->default(0);

            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();

            $table->string('status')->default('running'); // 'running' | 'success' | 'failed'
            $table->text('error_message')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_sync_logs');
    }
};
