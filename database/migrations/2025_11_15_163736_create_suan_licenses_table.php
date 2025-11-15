<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('suan_licenses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                  ->constrained('suan_employees')
                  ->cascadeOnDelete();

            $table->date('date')->index();

            $table->string('type', 100);
            $table->text('description')->nullable();

            $table->timestamps();

            $table->unique(['employee_id', 'date'], 'unique_license_employee_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suan_licenses');
    }
};
