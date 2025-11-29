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
        Schema::create('client_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id')->index('client_files_client_id_foreign');
            $table->unsignedBigInteger('uploaded_by')->nullable()->index('fk_client_files_uploaded_by');
            $table->string('original_name');
            $table->string('custom_name')->nullable();
            $table->text('description')->nullable();
            $table->json('tags')->nullable();
            $table->string('filepath');
            $table->unsignedInteger('size')->default(0);
            $table->string('mime_type', 100)->nullable();
            $table->string('type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_files');
    }
};
