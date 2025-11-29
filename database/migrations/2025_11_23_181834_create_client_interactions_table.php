<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('client_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // call, meeting, email, etc.
            $table->string('title');
            $table->text('description');
            $table->string('outcome')->nullable();
            $table->timestamp('interaction_date')->useCurrent();
            $table->timestamps();
            
            // Индексы для оптимизации запросов
            $table->index('client_id');
            $table->index('user_id');
            $table->index('type');
            $table->index('interaction_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('client_interactions');
    }
};
