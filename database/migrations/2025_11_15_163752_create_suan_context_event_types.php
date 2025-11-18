<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suan_context_event_types', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100);
            $table->string('code', 50)->unique();
            // ej: 'JUSTIFICATION', 'COMMISSION', 'SPECIAL_SHIFT'

            $table->string('description')->nullable();

            $table->boolean('requires_description')->default(true);
            $table->boolean('is_active')->default(true);

            $table->string('color', 20)->default('gray'); // opcional: para UI
            $table->string('icon', 50)->default('Info'); // opcional: para UI

            $table->timestamps();
        });

        // Inserción básica inicial
        DB::table('suan_context_event_types')->insert([
            [
                'name' => 'Justificación',
                'code' => 'JUSTIFICATION',
                'color' => 'amber',
                'icon' => 'AlertCircle',
            ],
            [
                'name' => 'Observación',
                'code' => 'OBSERVATION',
                'color' => 'sky',
                'icon' => 'MessageSquare',
            ],
            [
                'name' => 'Turno Especial',
                'code' => 'SPECIAL_SHIFT',
                'color' => 'violet',
                'icon' => 'Clock',
            ],
            [
                'name' => 'Nota Supervisor',
                'code' => 'SUPERVISOR_NOTE',
                'color' => 'indigo',
                'icon' => 'UserCheck',
            ],
            [
                'name' => 'Comisión',
                'code' => 'COMMISSION',
                'color' => 'emerald',
                'icon' => 'Briefcase',
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('suan_context_event_types');
    }
};
