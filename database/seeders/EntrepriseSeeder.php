<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EntrepriseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $entreprises = [
            [
                'NON_SOCIETE' => 'ASTEASY DEMO',
                'SITE_WEB' => 'https://demo.asteasy.local',
                'TELEPHONE' => '+2250700000001',
                'FICHIER_LOGO' => null,
                'IS_DELETE' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'NON_SOCIETE' => 'ENTREPRISE TEST API',
                'SITE_WEB' => 'https://api-test.asteasy.local',
                'TELEPHONE' => '+2250700000002',
                'FICHIER_LOGO' => null,
                'IS_DELETE' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($entreprises as $entreprise) {
            $exists = DB::table('TB_ENTREPRISE')
                ->where('NON_SOCIETE', $entreprise['NON_SOCIETE'])
                ->whereNull('deleted_at')
                ->exists();

            if (!$exists) {
                DB::table('TB_ENTREPRISE')->insert($entreprise);
            }
        }
    }
}
