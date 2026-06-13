<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Créer la nouvelle table pivot TB_UTILISATEUR_ROLE
        Schema::create('TB_UTILISATEUR_ROLE', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);
            $table->bigIncrements('ID');

            $table->unsignedBigInteger('UTILISATEUR_ID')->nullable();
            $table->foreign('UTILISATEUR_ID')->references('id')->on('utilisateurs')->onDelete('cascade');

            $table->unsignedBigInteger('ROLE_ID')->nullable();
            $table->foreign('ROLE_ID')->references('ID')->on('TB_ROLE')->onDelete('cascade');
        });

        // 2. Copier les données existantes utilisateur-rôle de TB_ROLE_PERMISSION vers TB_UTILISATEUR_ROLE
        $rows = DB::table('TB_ROLE_PERMISSION')
            ->whereNotNull('UTILISATEUR_ID')
            ->get();

        foreach ($rows as $row) {
            DB::table('TB_UTILISATEUR_ROLE')->insert([
                'UTILISATEUR_ID' => $row->UTILISATEUR_ID,
                'ROLE_ID' => $row->ROLE_ID,
                'IS_DELETE' => $row->IS_DELETE ?? false,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
                'deleted_at' => $row->deleted_at ?? null,
            ]);
        }

        // 3. Supprimer les enregistrements utilisateur-rôle de l'ancienne table TB_ROLE_PERMISSION
        DB::table('TB_ROLE_PERMISSION')
            ->whereNotNull('UTILISATEUR_ID')
            ->delete();

        // 4. Supprimer la colonne UTILISATEUR_ID de TB_ROLE_PERMISSION
        Schema::table('TB_ROLE_PERMISSION', function (Blueprint $table) {
            $table->dropForeign(['UTILISATEUR_ID']);
            $table->dropColumn('UTILISATEUR_ID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Recréer la colonne UTILISATEUR_ID sur TB_ROLE_PERMISSION
        Schema::table('TB_ROLE_PERMISSION', function (Blueprint $table) {
            $table->unsignedBigInteger('UTILISATEUR_ID')->nullable();
            $table->foreign('UTILISATEUR_ID')->references('id')->on('utilisateurs')->onDelete('cascade');
        });

        // 2. Transférer les données de TB_UTILISATEUR_ROLE vers TB_ROLE_PERMISSION
        $rows = DB::table('TB_UTILISATEUR_ROLE')->get();

        foreach ($rows as $row) {
            DB::table('TB_ROLE_PERMISSION')->insert([
                'UTILISATEUR_ID' => $row->UTILISATEUR_ID,
                'ROLE_ID' => $row->ROLE_ID,
                'IS_DELETE' => $row->IS_DELETE ?? false,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
                'deleted_at' => $row->deleted_at ?? null,
            ]);
        }

        // 3. Supprimer la table TB_UTILISATEUR_ROLE
        Schema::dropIfExists('TB_UTILISATEUR_ROLE');
    }
};
