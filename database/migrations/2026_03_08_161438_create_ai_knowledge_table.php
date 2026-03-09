<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_knowledge', function (Blueprint $table) {
            $table->id();
            $table->string('title');              // Judul topik, contoh: "Hipertensi"
            $table->string('category')->default('umum'); // Kategori: umum, tensi, gula, jantung, bmi, dll
            $table->text('keywords');             // Kata kunci CSV, contoh: "hipertensi,tensi,tekanan darah"
            $table->longText('content');          // Isi pengetahuan (bisa panjang)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_knowledge');
    }
};
