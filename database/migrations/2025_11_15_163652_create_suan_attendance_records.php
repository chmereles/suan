<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suan_attendance_records', function (Blueprint $table) {

            $table->id();

            // Empleado
            $table->foreignId('labor_link_id')
                ->constrained('suan_labor_links')
                ->cascadeOnDelete();

            // Fecha normalizada (solo yyyy-mm-dd)
            $table->date('date')->index();

            // Timestamp real de la marcación (de CrossChex)
            $table->timestamp('recorded_at')->index();

            // Tipo interpretado por SUAN
            // Ej: in_morning, out_morning, in_afternoon, out_afternoon
            $table->string('type', 50)->nullable()->index();

            // Relación con attendance_logs (crudos)
            $table->unsignedBigInteger('attendance_log_id')->nullable()->index();

            // raw_id del registro original (CrossChex 'raw_id')
            $table->string('raw_id')->nullable()->index();

            // Payload crudo para auditoría
            $table->json('raw_payload')->nullable();

            // Metadata del procesamiento
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Evitar duplicados exactos de marcación
            $table->unique(
                ['labor_link_id', 'recorded_at'],
                'unique_labor_link_recorded_at'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suan_attendance_records');
    }
};
