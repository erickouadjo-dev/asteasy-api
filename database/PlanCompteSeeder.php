<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Utility\Imports\CsvImport;
use App\Models\PlanCompte;
use Illuminate\Support\Facades\Log;

class PlanCompteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $result=Excel::toArray(new CsvImport, public_path('files/excel/plan_compte.csv'));
      foreach ($result[0] as $key => $plan_compte) {
          if ($key == 0) {
              continue;
          }
          $db_plan_compte = PlanCompte::find($plan_compte[0]);
          if (!$db_plan_compte) {
              PlanCompte::create([
                  'compte' => $plan_compte[0],
                  'type_flux' => $plan_compte[1],
                  'description' => $plan_compte[2],
                  'flux' => $plan_compte[3],
                  'base' => $plan_compte[4],
              ]);
          }
      }
    }
}
