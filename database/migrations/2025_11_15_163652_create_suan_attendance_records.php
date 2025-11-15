<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suan_attendance_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('suan_employees')
                ->cascadeOnDelete();

            $table->date('date')->index();

            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();

            $table->integer('worked_minutes')->unsigned()->default(0);
            $table->integer('late_minutes')->unsigned()->default(0);
            $table->integer('early_leave_minutes')->unsigned()->default(0);

            $table->string('source', 50)->default('crosschex');
            $table->json('metadata')->nullable();

            $table->timestamps();

            // $table->unique(['employee_id', 'date'], 'unique_employee_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suan_attendance_records');
    }
};
