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
        Schema::create('crosschex_logs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('employee_workno')->nullable()->index();
            $table->unsignedTinyInteger('checktype')->nullable();
            $table->timestamp('checktime')->nullable()->index();
            $table->string('device_serial')->nullable()->index();
            $table->json('raw')->nullable();
            $table->timestamps();

            $table->index(['employee_workno', 'checktime']);
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crosschex_logs');
    }
};
