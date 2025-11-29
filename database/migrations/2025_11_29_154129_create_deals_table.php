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
        Schema::create('deals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id')->index('deals_client_id_foreign');
            $table->unsignedBigInteger('created_by')->nullable()->index('fk_deals_created_by');
            $table->string('title');
            $table->decimal('amount', 12)->default(0);
            $table->string('status')->default('new');
            $table->timestamp('expected_close_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
