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
        Schema::table('TB_SAFETY_ACTION', function (Blueprint $table) {
            $table->dropForeign('tb_safety_action_id_task_safety_foreign');
            $table->foreign('ID_TASK_SAFETY')->references('ID')->on('TB_TASKS_SAFETY');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('TB_SAFETY_ACTION', function (Blueprint $table) {
            $table->dropForeign(['ID_TASK_SAFETY']);
            $table->foreign('ID_TASK_SAFETY')->references('ID')->on('TB_TASK_SAFETY');
        });
    }
};
