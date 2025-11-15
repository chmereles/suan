<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suan_manual_notes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('suan_employees')
                ->cascadeOnDelete();

            $table->date('date')->index();

            $table->enum('note_type', [
                'justification',
                'observation',
                'special_shift',
            ]);

            $table->text('content');      // motivo o detalle cargado por el jefe
            $table->foreignId('created_by')->nullable(); // usuario del sistema / jefe

            $table->timestamps();

            $table->index(['employee_id', 'date', 'note_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suan_manual_notes');
    }
};
