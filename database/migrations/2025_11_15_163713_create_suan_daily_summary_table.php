<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('suan_daily_summary', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                  ->constrained('suan_employees')
                  ->cascadeOnDelete();

            $table->date('date')->index();

            $table->enum('status', [
                'present',
                'absent_unjustified',
                'absent_justified',
                'license',
                'holiday',
                'anomaly'
            ])->default('present');

            $table->integer('total_worked_minutes')->unsigned()->default(0);

            $table->boolean('justified')->default(false); // si jefe cargó una justificación
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['employee_id', 'date'], 'unique_summary_employee_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suan_daily_summary');
    }
};
