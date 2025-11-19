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
    Schema::create('suan_monthly_summary', function (Blueprint $table) {

        $table->id();

        $table->foreignId('labor_link_id')
            ->constrained('suan_labor_links')
            ->cascadeOnDelete();

        $table->string('period', 7); // YYYY-MM

        $table->unsignedSmallInteger('present_days')->default(0);
        $table->unsignedSmallInteger('absent_unjustified_days')->default(0);
        $table->unsignedSmallInteger('absent_justified_days')->default(0);

        $table->unsignedInteger('late_minutes')->default(0);
        $table->unsignedInteger('early_leave_minutes')->default(0);

        $table->unsignedInteger('worked_minutes_total')->default(0);

        $table->json('metadata')->nullable();

        $table->timestamps();

        $table->unique(['labor_link_id', 'period']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suan_monthly_summary');
    }
};
