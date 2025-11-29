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
        Schema::table('clients', function (Blueprint $table) {
            $table->foreign(['created_by'], 'fk_clients_created_by')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['responsible_id'], 'fk_clients_responsible')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign('fk_clients_created_by');
            $table->dropForeign('fk_clients_responsible');
        });
    }
};
