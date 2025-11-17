<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suan_licenses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('labor_link_id')
                ->constrained('suan_labor_links')
                ->cascadeOnDelete();

            $table->date('date')->index();

            $table->string('type', 100);
            $table->text('description')->nullable();

            $table->timestamps();

            $table->unique(['labor_link_id', 'date'], 'unique_license_labor_link_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suan_licenses');
    }
};
