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
            if (!Schema::hasColumn('TB_RISQUES', 'TOP_RISQUE')) {
                $table->enum('TOP_RISQUE', ['OUI', 'NON'])->default('NON')->after('STATUT_RISK');
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
            if (Schema::hasColumn('TB_RISQUES', 'TOP_RISQUE')) {
                $table->dropColumn('TOP_RISQUE');
            }
        });

        Schema::enableForeignKeyConstraints();
    }
};
