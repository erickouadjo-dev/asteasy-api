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
        Schema::table('TB_EVENT_ANALYSE', function (Blueprint $table) {
            // Fix ID_STATUT_EVENEMENT foreign key
            $table->dropForeign('tb_event_analyse_id_statut_evenement_foreign');
            $table->foreign('ID_STATUT_EVENEMENT')->references('ID')->on('TB_STATUT');

            // Fix ID_RISQUE foreign key
            $table->dropForeign('tb_event_analyse_id_risque_foreign');
            $table->foreign('ID_RISQUE')->references('ID')->on('TB_RISQUES');

            // Fix ID_TAG_ETIQUETTE foreign key
            $table->dropForeign('tb_event_analyse_id_tag_etiquette_foreign');
            $table->foreign('ID_TAG_ETIQUETTE')->references('ID')->on('TB_TARG_ETIQUETTE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('TB_EVENT_ANALYSE', function (Blueprint $table) {
            $table->dropForeign(['ID_STATUT_EVENEMENT']);
            $table->foreign('ID_STATUT_EVENEMENT')->references('ID')->on('TB_STATUT_EVENEMENT');

            $table->dropForeign(['ID_RISQUE']);
            $table->foreign('ID_RISQUE')->references('id')->on('RISQUES');

            $table->dropForeign(['ID_TAG_ETIQUETTE']);
            $table->foreign('ID_TAG_ETIQUETTE')->references('ID')->on('TB_TAG_ETIQUETTE');
        });
    }
};
