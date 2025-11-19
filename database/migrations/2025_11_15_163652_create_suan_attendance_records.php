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

            // Relación laboral vigente del empleado
            $table->foreignId('labor_link_id')
                ->constrained('suan_labor_links')
                ->cascadeOnDelete();

            // Día al que pertenece la marca (normalizado)
            $table->date('date')->index();

            // Timestamp final normalizado (zona horaria local)
            $table->timestamp('recorded_at')->index();

            // Tipo interpretado por SUAN: in, out, unknown, etc.
            $table->string('type', 50)->nullable()->index();

            // Relación con attendance_logs (crudos)
            $table->unsignedBigInteger('attendance_log_id')->nullable()->index();

            // Información ligera del procesamiento
            // Ej: { "normalized": true, "duplicate": false }
            $table->json('metadata')->nullable();

            $table->string('source', 30)->default('device')->index();

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
