<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
      $this->call([
        /* ComptabiliteSeeder::class,*/
        
        /* PlanComptableSeeder::class,*/
        /* SaisieOperationSeeder::class,*/

          // BanqueSeeder::class,
          // CompteBancaireSeeder::class,
          // DepartementSeeder::class,
          // SousTraitantSeeder::class,
          // Levee_fondsSeeder::class,
          // ProgrammeSeeder::class,
          // MissionSuiviControleSeeder::class,
          // PrestataireSeeder::class,
          // MarcheSeeder::class,
          // AvenantSeeder::class,
          // AccordFinancementSeeder::class,
          // DecompteSeeder::class,
          // EntrepriseSeeder::class,
          //ComptaAnalytiqueSeeder::class,
          RolesPermissionsSeeder::class,
          EntrepriseSeeder::class,
      ]);
    }
}
