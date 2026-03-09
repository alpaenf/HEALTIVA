<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('systolic')->nullable()->comment('Tekanan darah sistolik (mmHg)');
            $table->integer('diastolic')->nullable()->comment('Tekanan darah diastolik (mmHg)');
            $table->integer('heart_rate')->nullable()->comment('Detak jantung (bpm)');
            $table->decimal('blood_sugar', 6, 2)->nullable()->comment('Gula darah (mg/dL)');
            $table->decimal('weight', 5, 2)->nullable()->comment('Berat badan (kg)');
            $table->decimal('height', 5, 2)->nullable()->comment('Tinggi badan (cm)');
            $table->decimal('temperature', 4, 2)->nullable()->comment('Suhu tubuh (°C)');
            $table->integer('oxygen_saturation')->nullable()->comment('Saturasi oksigen SpO2 (%)');
            $table->text('notes')->nullable();
            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_records');
    }
};
