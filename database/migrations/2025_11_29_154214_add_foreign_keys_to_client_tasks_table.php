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
        Schema::table('client_tasks', function (Blueprint $table) {
            $table->foreign(['client_id'])->references(['id'])->on('clients')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['assigned_to'], 'fk_client_tasks_assigned_to')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['user_id'], 'fk_client_tasks_created_by')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_tasks', function (Blueprint $table) {
            $table->dropForeign('client_tasks_client_id_foreign');
            $table->dropForeign('fk_client_tasks_assigned_to');
            $table->dropForeign('fk_client_tasks_created_by');
        });
    }
};
