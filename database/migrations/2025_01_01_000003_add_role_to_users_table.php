<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'user'])->default('user')->after('email');
            $table->string('phone')->nullable()->after('role');
            $table->date('date_of_birth')->nullable()->after('phone');
            $table->enum('gender', ['male', 'female'])->nullable()->after('date_of_birth');
            $table->text('avatar')->nullable()->after('gender');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'date_of_birth', 'gender', 'avatar']);
        });
    }
};
