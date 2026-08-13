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
        Schema::disableForeignKeyConstraints();

        // 1. Add ID to TB_MESURES_CONTROLE if it doesn't exist
        if (!Schema::hasColumn('TB_MESURES_CONTROLE', 'ID')) {
            Schema::table('TB_MESURES_CONTROLE', function (Blueprint $table) {
                $table->bigIncrements('ID')->after('IS_DELETE');
            });
        }

        // 2. Fix TB_RISQUES foreign keys
        Schema::table('TB_RISQUES', function (Blueprint $table) {
            // Drop old foreign keys
            $table->dropForeign('tb_risques_id_mesures_controle_foreign');
            $table->dropForeign('tb_risques_id_risk_subcategory_foreign');

            // Re-add pointing to correct tables
            $table->foreign('ID_MESURES_CONTROLE')->references('ID')->on('TB_MESURES_CONTROLE')->nullable();
            $table->foreign('ID_RISK_SUBCATEGORY')->references('ID')->on('TB_RISK_SUBCATEGORY')->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('TB_RISQUES', function (Blueprint $table) {
            $table->dropForeign(['ID_MESURES_CONTROLE']);
            $table->dropForeign(['ID_RISK_SUBCATEGORY']);

            $table->foreign('ID_MESURES_CONTROLE')->references('ID')->on('MESURES_CONTROLE')->nullable();
            $table->foreign('ID_RISK_SUBCATEGORY')->references('ID')->on('RISK_SUBCATEGORY')->nullable();
        });

        Schema::table('TB_MESURES_CONTROLE', function (Blueprint $table) {
            $table->dropColumn('ID');
        });

        Schema::enableForeignKeyConstraints();
    }
};
