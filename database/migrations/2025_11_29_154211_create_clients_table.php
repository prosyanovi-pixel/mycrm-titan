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
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('type', ['individual', 'entrepreneur', 'legal'])->default('individual');
            $table->string('last_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('ogrnip')->nullable();
            $table->string('legal_form')->nullable();
            $table->string('company_name')->nullable();
            $table->string('legal_type')->nullable();
            $table->string('ogrn')->nullable();
            $table->string('kpp')->nullable();
            $table->string('inn')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->unsignedBigInteger('responsible_id')->nullable()->index('fk_clients_responsible');
            $table->unsignedBigInteger('created_by')->nullable()->index('fk_clients_created_by');
            $table->string('status')->default('lead');
            $table->json('extra')->nullable();
            $table->json('tags')->nullable();
            $table->string('source')->nullable();
            $table->decimal('total_revenue', 12)->default(0);
            $table->timestamp('last_activity_at')->nullable();
            $table->integer('activity_score')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
