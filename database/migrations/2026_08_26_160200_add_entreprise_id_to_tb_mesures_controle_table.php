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

        if (Schema::hasTable('TB_MESURES_CONTROLE') && !Schema::hasColumn('TB_MESURES_CONTROLE', 'ENTREPRISE_ID')) {
            Schema::table('TB_MESURES_CONTROLE', function (Blueprint $table) {
                $table->unsignedBigInteger('ENTREPRISE_ID')->nullable();
                $table->foreign('ENTREPRISE_ID')->references('ID')->on('TB_ENTREPRISE')->onDelete('cascade');
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('TB_MESURES_CONTROLE') && Schema::hasColumn('TB_MESURES_CONTROLE', 'ENTREPRISE_ID')) {
            Schema::table('TB_MESURES_CONTROLE', function (Blueprint $table) {
                $table->dropForeign(['ENTREPRISE_ID']);
                $table->dropColumn('ENTREPRISE_ID');
            });
        }

        Schema::enableForeignKeyConstraints();
    }
};
