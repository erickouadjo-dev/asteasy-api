<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Utility\Imports\CsvImport;
use App\Models\MissionControle;

class MissionSuiviControleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $result=Excel::toArray(new CsvImport, public_path('files/excel/donnees_historiques/mission_suivi_controles.xlsx'));
        foreach ($result[0] as $key => $mission_suivi_controle) {
            if ($key == 0) {
                continue;
            }
            $db_mission_suivi_controle = MissionControle::find($mission_suivi_controle[0]);
            if (!$db_mission_suivi_controle) {
                MissionControle::create([
                    'id' => $mission_suivi_controle[0],
                    'libelle' => $mission_suivi_controle[1],
                ]);
            }
        }
    }
}
