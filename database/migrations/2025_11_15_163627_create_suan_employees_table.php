<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suan_employees', function (Blueprint $table) {
            $table->id();

            $table->string('legajo', 50);           // ID en el sistema de Haberes
            $table->string('cuil', 20)->nullable();
            $table->string('full_name');
            $table->string('area')->nullable();

            $table->string('device_user_id', 50)->nullable(); // ID del reloj CrossChex
            $table->boolean('active')->default(true);

            $table->timestamp('synced_at')->nullable(); // última sincronización con Firebird

            $table->timestamps();

            $table->unique('legajo');
            $table->index('device_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suan_employees');
    }
};
