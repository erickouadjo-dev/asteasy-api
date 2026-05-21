<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Utility\Imports\CsvImport;
use App\Models\PlanComptable;
use Illuminate\Support\Facades\Log;

class PlanComptableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $result=Excel::toArray(new CsvImport, public_path('files/excel/plan_comptable.xlsx'));
        foreach ($result[0] as $key => $plan_comptable) {
            if ($key == 0) {
                continue;
            }
            $db_plan_comptable = PlanComptable::find($plan_comptable[0]);
            if (!$db_plan_comptable) {
                PlanComptable::create([
                    'numero_compte' => $plan_comptable[0],
                    'libelle_concession_peage' => $plan_comptable[1],
                    'libelle_pesage' => $plan_comptable[1],
                    'libelle_siege' => $plan_comptable[1],
                    'annee' => $plan_comptable[2],
                ]);
            }
        }
    }
}
