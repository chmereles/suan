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
        Schema::create('suan_labor_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('suan_people')->cascadeOnDelete();
            $table->enum('source', ['haberes', 'planes']);
            $table->string('external_id', 50)->nullable();
            $table->boolean('active')->default(true);
            $table->string('area')->nullable();
            $table->string('position')->nullable();
            $table->json('schedule')->nullable();
            $table->timestamps();

            $table->index(['source', 'external_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suan_labor_links');
    }
};
