<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();

            $table->string('device_user_id')->index();   // ID empleado según dispositivo
            $table->string('device_serial')->nullable()->index();
            $table->string('record_type')->nullable();   // entrada, salida, verificación, etc.

            $table->timestamp('recorded_at')->index();   // hora exacta del registro
            $table->string('raw_id')->unique();          // ID único del registro en el origen

            $table->json('raw_payload')->nullable();     // JSON completo para auditoría

            $table->timestamps();

            $table->index(['device_user_id', 'recorded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
