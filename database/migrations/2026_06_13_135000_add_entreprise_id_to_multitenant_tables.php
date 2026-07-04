<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $tables = [
        'utilisateurs',
        'TB_PROFIL_EMPLOYE',
        'TB_NOMINATION_EMPLOYE',
        'TB_RISK_ASSESSMENT',
        'TB_PERSONAL_RISK_EVAL',
        'TB_FORMATION',
        'TB_EMPLOYE_FORMATION',
        'TB_HISTORIQUE_PROFIL_EMPLOYE',
        'TB_DOCUMENTATION',
        'TB_DOCUMENTS',
        'TB_VEHICULES',
        'TB_AERONEFS',
        'TB_EQUIPEMENTS',
        'TB_BASE_MATERIEL',
        'trace_activites',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedBigInteger('ENTREPRISE_ID')->nullable();
            });
        }

        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreign('ENTREPRISE_ID')->references('ID')->on('TB_ENTREPRISE')->onDelete('cascade');
            });
        }

        // Migrate existing data based on relationships
        try {
            // 1. Users from their employees
            DB::table('utilisateurs')
                ->join('TB_EMPLOYE', 'TB_EMPLOYE.USER_ID', '=', 'utilisateurs.id')
                ->whereNotNull('TB_EMPLOYE.ENTREPRISE_ID')
                ->update(['utilisateurs.ENTREPRISE_ID' => DB::raw('`TB_EMPLOYE`.`ENTREPRISE_ID`')]);

            // 2. TB_NOMINATION_EMPLOYE from TB_EMPLOYE
            DB::table('TB_NOMINATION_EMPLOYE')
                ->join('TB_EMPLOYE', 'TB_EMPLOYE.ID', '=', 'TB_NOMINATION_EMPLOYE.EMPLOYE_ID')
                ->whereNotNull('TB_EMPLOYE.ENTREPRISE_ID')
                ->update(['TB_NOMINATION_EMPLOYE.ENTREPRISE_ID' => DB::raw('`TB_EMPLOYE`.`ENTREPRISE_ID`')]);

            // 3. TB_EMPLOYE_FORMATION from TB_EMPLOYE
            DB::table('TB_EMPLOYE_FORMATION')
                ->join('TB_EMPLOYE', 'TB_EMPLOYE.ID', '=', 'TB_EMPLOYE_FORMATION.EMPLOYE_ID')
                ->whereNotNull('TB_EMPLOYE.ENTREPRISE_ID')
                ->update(['TB_EMPLOYE_FORMATION.ENTREPRISE_ID' => DB::raw('`TB_EMPLOYE`.`ENTREPRISE_ID`')]);

            // 4. TB_HISTORIQUE_PROFIL_EMPLOYE from TB_EMPLOYE
            DB::table('TB_HISTORIQUE_PROFIL_EMPLOYE')
                ->join('TB_EMPLOYE', 'TB_EMPLOYE.ID', '=', 'TB_HISTORIQUE_PROFIL_EMPLOYE.EMPLOYE_ID')
                ->whereNotNull('TB_EMPLOYE.ENTREPRISE_ID')
                ->update(['TB_HISTORIQUE_PROFIL_EMPLOYE.ENTREPRISE_ID' => DB::raw('`TB_EMPLOYE`.`ENTREPRISE_ID`')]);

            // 5. TB_PERSONAL_RISK_EVAL from TB_EMPLOYE
            DB::table('TB_PERSONAL_RISK_EVAL')
                ->join('TB_EMPLOYE', 'TB_EMPLOYE.ID', '=', 'TB_PERSONAL_RISK_EVAL.EMPLOYE_ID')
                ->whereNotNull('TB_EMPLOYE.ENTREPRISE_ID')
                ->update(['TB_PERSONAL_RISK_EVAL.ENTREPRISE_ID' => DB::raw('`TB_EMPLOYE`.`ENTREPRISE_ID`')]);

            // 6. TB_DOCUMENTATION from TB_BASE
            DB::table('TB_DOCUMENTATION')
                ->join('TB_BASE', 'TB_BASE.ID', '=', 'TB_DOCUMENTATION.BASE_ID')
                ->whereNotNull('TB_BASE.ENTREPRISE_ID')
                ->update(['TB_DOCUMENTATION.ENTREPRISE_ID' => DB::raw('`TB_BASE`.`ENTREPRISE_ID`')]);

            // 7. TB_BASE_MATERIEL from TB_BASE
            DB::table('TB_BASE_MATERIEL')
                ->join('TB_BASE', 'TB_BASE.ID', '=', 'TB_BASE_MATERIEL.BASE_ID')
                ->whereNotNull('TB_BASE.ENTREPRISE_ID')
                ->update(['TB_BASE_MATERIEL.ENTREPRISE_ID' => DB::raw('`TB_BASE`.`ENTREPRISE_ID`')]);

            // 8. trace_activites from utilisateurs
            DB::table('trace_activites')
                ->join('utilisateurs', 'utilisateurs.id', '=', 'trace_activites.utilisateur')
                ->whereNotNull('utilisateurs.ENTREPRISE_ID')
                ->update(['trace_activites.ENTREPRISE_ID' => DB::raw('`utilisateurs`.`ENTREPRISE_ID`')]);
        } catch (\Exception $e) {
            // Keep going if some tables have no existing data or fail during data migration
            report($e);
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['ENTREPRISE_ID']);
                $table->dropColumn('ENTREPRISE_ID');
            });
        }

        Schema::enableForeignKeyConstraints();
    }
};
