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
        Schema::create('suan_people', function (Blueprint $table) {
            $table->id();
            $table->string('external_id', 50)->nullable();
            $table->string('document', 20)->unique();
            $table->string('full_name');

            $table->string('device_user_id', 50)->nullable(); // ID del reloj CrossChex
            $table->timestamps();

            $table->index('external_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suan_people');
    }
};
