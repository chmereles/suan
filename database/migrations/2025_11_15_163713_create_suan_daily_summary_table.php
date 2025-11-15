<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suan_daily_summary', function (Blueprint $table) {

            $table->id();

            // Empleado
            $table->foreignId('employee_id')
                ->constrained('suan_employees')
                ->cascadeOnDelete();

            // Fecha
            $table->date('date')->index();

            // Estado final del día
            $table->enum('status', [
                'present',              // estuvo presente según fichadas
                'absent_unjustified',   // no asistió y no tiene justificativo
                'absent_justified',     // no asistió pero hay justificación
                'license',              // licencia formal (día completo)
                'partial',              // trabajó parte del día
                'holiday',              // feriado (opcional)
                'anomaly',              // presente pero con anomalías graves
            ])->default('present');

            // Datos cuantitativos
            $table->integer('worked_minutes')->unsigned()->default(0);
            $table->integer('late_minutes')->unsigned()->default(0);
            $table->integer('early_leave_minutes')->unsigned()->default(0);

            // Indicadores de contexto
            $table->boolean('has_license')->default(false);
            $table->boolean('has_context_event')->default(false);
            // Cualquier evento que modifique el cálculo normal del presentismo

            // Anomalías detectadas (json)
            // Ej: [{"type":"late_entry","minutes":12},{"type":"missing_checkout"}]
            $table->json('anomalies')->nullable();

            // Notas explicativas generadas por el sistema o RRHH
            // (No para notas manuales antiguas: eso va en otra tabla)
            $table->text('notes')->nullable();

            // Metadata técnica del análisis (útil para auditoría futura)
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Un empleado solo puede tener un resumen por día
            $table->unique(['employee_id', 'date'], 'unique_daily_summary_employee_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suan_daily_summary');
    }
};
