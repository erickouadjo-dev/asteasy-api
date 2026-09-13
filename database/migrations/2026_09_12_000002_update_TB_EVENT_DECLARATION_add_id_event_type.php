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
        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('TB_EVENT_DECLARATION')) {
            Schema::table('TB_EVENT_DECLARATION', function (Blueprint $table) {
                if (!Schema::hasColumn('TB_EVENT_DECLARATION', 'ID_EVENT_TYPE')) {
                    $table->unsignedBigInteger('ID_EVENT_TYPE')->nullable()->after('REF_EVENT');
                }
                if (Schema::hasColumn('TB_EVENT_DECLARATION', 'TYPE_EVENT')) {
                    $table->string('TYPE_EVENT', 255)->nullable()->change();
                }
            });

            // Migration des données textuelles existantes vers ID_EVENT_TYPE
            try {
                DB::statement("
                    UPDATE TB_EVENT_DECLARATION
                    SET ID_EVENT_TYPE = CASE
                        WHEN UPPER(TYPE_EVENT) LIKE '%ACCIDENT%' THEN 1
                        WHEN UPPER(TYPE_EVENT) LIKE '%INCIDENT MAJEUR%' THEN 2
                        WHEN UPPER(TYPE_EVENT) LIKE '%INCIDENT MINEUR%' THEN 3
                        WHEN UPPER(TYPE_EVENT) LIKE '%DANGER%' THEN 4
                        WHEN UPPER(TYPE_EVENT) LIKE '%TECHNIQUE%' THEN 5
                        ELSE 3
                    END
                    WHERE ID_EVENT_TYPE IS NULL
                ");
            } catch (\Exception $e) {
                // ignore
            }

            // Ajout de la contrainte de clé étrangère
            Schema::table('TB_EVENT_DECLARATION', function (Blueprint $table) {
                $table->foreign('ID_EVENT_TYPE')->references('ID')->on('TB_EVENT_TYPE')->onDelete('set null');
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

        if (Schema::hasTable('TB_EVENT_DECLARATION')) {
            Schema::table('TB_EVENT_DECLARATION', function (Blueprint $table) {
                if (Schema::hasColumn('TB_EVENT_DECLARATION', 'ID_EVENT_TYPE')) {
                    $table->dropForeign(['ID_EVENT_TYPE']);
                    $table->dropColumn('ID_EVENT_TYPE');
                }
            });
        }

        Schema::enableForeignKeyConstraints();
    }
};
