<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suan_context_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('labor_link_id')
                ->constrained('suan_labor_links')
                ->cascadeOnDelete();

            $table->date('date')->index();

            $table->foreignId('type_id')
                ->constrained('suan_context_event_types')
                ->restrictOnDelete();

            // $table->enum('type', [
            //     'justification',
            //     'observation',
            //     'special_shift',
            //     'supervisor_note',
            //     'commission',
            // ]);
            $table->string('source', 50)->default('manual');
            $table->text('description');
            $table->json('metadata')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete(); // usuario del sistema / jefe

            $table->timestamps();

            $table->index(['labor_link_id', 'date', 'type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suan_context_events');
    }
};
