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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('last_name')->comment('Фамилия');
            $table->string('first_name')->comment('Имя');
            $table->string('middle_name')->nullable()->comment('Отчество');
            $table->string('email')->unique('email');
            $table->string('nickname', 100)->nullable()->unique('nickname')->comment('Псевдоним для входа');
            $table->string('position')->nullable()->comment('Должность');
            $table->unsignedBigInteger('role_id')->default(3)->index('role_id');
            $table->string('phone', 50)->nullable();
            $table->boolean('is_active')->nullable()->default(true);
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
