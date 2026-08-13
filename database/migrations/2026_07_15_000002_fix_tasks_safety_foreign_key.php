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
        Schema::table('TB_TASKS_SAFETY', function (Blueprint $table) {
            $table->dropForeign('tb_tasks_safety_id_tag_etiquette_foreign');
            $table->foreign('ID_TAG_ETIQUETTE')->references('ID')->on('TB_TARG_ETIQUETTE');
        });

        Schema::table('TB_SAFETY_ACTION', function (Blueprint $table) {
            $table->dropForeign('tb_safety_action_id_tag_foreign');
            $table->foreign('ID_TAG')->references('ID')->on('TB_TARG_ETIQUETTE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('TB_TASKS_SAFETY', function (Blueprint $table) {
            $table->dropForeign(['ID_TAG_ETIQUETTE']);
            $table->foreign('ID_TAG_ETIQUETTE')->references('ID')->on('TB_TAG_ETIQUETTE');
        });

        Schema::table('TB_SAFETY_ACTION', function (Blueprint $table) {
            $table->dropForeign(['ID_TAG']);
            $table->foreign('ID_TAG')->references('ID')->on('TB_TAG_ETIQUETTE');
        });
    }
};
