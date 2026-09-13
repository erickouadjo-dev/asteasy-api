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

        if (!Schema::hasTable('TB_EVENT_TYPE')) {
            Schema::create('TB_EVENT_TYPE', function (Blueprint $table) {
                $table->bigIncrements('ID');
                $table->string('CODE', 50)->nullable()->comment('Code court du type événement (ex: ACC, INC-MAJ, DAN)');
                $table->string('LIBELLE', 255)->comment('Libellé du type d événement (ex: ACCIDENT, INCIDENT MAJEUR)');
                $table->text('DESCRIPTION')->nullable()->comment('Description détaillée du type d événement');
                $table->unsignedBigInteger('ENTREPRISE_ID')->nullable();
                $table->boolean('IS_DELETE')->default(false);
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('ENTREPRISE_ID')->references('ID')->on('TB_ENTREPRISE')->onDelete('cascade');
            });
        }

        // Seeding des types d'événements standards
        $defaultTypes = [
            [
                'ID' => 1,
                'CODE' => 'EVE',
                'LIBELLE' => 'ÉVÉNEMENT',
                'DESCRIPTION' => 'Événement de sécurité des vols, incident opérationnel ou fait technique.',
                'IS_DELETE' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ID' => 2,
                'CODE' => 'HZD',
                'LIBELLE' => 'HAZARD (DANGER)',
                'DESCRIPTION' => 'Notification d un danger, condition ou risque potentiel (signalement proactif ou volontaire).',
                'IS_DELETE' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($defaultTypes as $type) {
            $exists = DB::table('TB_EVENT_TYPE')->where('ID', $type['ID'])->exists();
            if (!$exists) {
                DB::table('TB_EVENT_TYPE')->insert($type);
            } else {
                DB::table('TB_EVENT_TYPE')->where('ID', $type['ID'])->update([
                    'CODE' => $type['CODE'],
                    'LIBELLE' => $type['LIBELLE'],
                    'DESCRIPTION' => $type['DESCRIPTION'],
                ]);
            }
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('TB_EVENT_TYPE');
        Schema::enableForeignKeyConstraints();
    }
};
