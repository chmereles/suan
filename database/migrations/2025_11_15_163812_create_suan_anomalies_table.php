<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('suan_anomalies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                  ->constrained('suan_employees')
                  ->cascadeOnDelete();

            $table->date('date')->index();

            $table->string('anomaly_type', 100)->nullable();
            $table->text('description')->nullable();

            $table->boolean('resolved')->default(false);
            $table->foreignId('resolved_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suan_anomalies');
    }
};
