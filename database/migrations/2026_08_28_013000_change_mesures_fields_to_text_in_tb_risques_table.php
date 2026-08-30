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

        Schema::table('TB_RISQUES', function (Blueprint $table) {
            // Add MESURES_CONTROLE text column if it doesn't exist
            if (!Schema::hasColumn('TB_RISQUES', 'MESURES_CONTROLE')) {
                $table->text('MESURES_CONTROLE')->nullable()->after('CONSEQUENSE_ULTIME');
            }

            // Add MESURES_ADDITIONNELLES text column if it doesn't exist
            if (!Schema::hasColumn('TB_RISQUES', 'MESURES_ADDITIONNELLES')) {
                $table->text('MESURES_ADDITIONNELLES')->nullable()->after('ID_MATRICE_RISQUE');
            }
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
            if (Schema::hasColumn('TB_RISQUES', 'MESURES_CONTROLE')) {
                $table->dropColumn('MESURES_CONTROLE');
            }
            if (Schema::hasColumn('TB_RISQUES', 'MESURES_ADDITIONNELLES')) {
                $table->dropColumn('MESURES_ADDITIONNELLES');
            }
        });

        Schema::enableForeignKeyConstraints();
    }
};
