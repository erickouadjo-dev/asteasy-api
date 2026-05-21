<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Utility\Imports\CsvImport;
use App\Models\Departement;

class DepartementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $result = Excel::toArray(new CsvImport, public_path('files/excel/departements.xlsx'));

      foreach ($result[0] as $key => $departement) {
        if($key == 0) {
          continue;
        }

        $db_departement = Departement::find($departement[0]);

        if(!$db_departement) {
          Departement::create([
            'id' => $departement[0],
            'libelle' => $departement[1],
            'created_at' => now(),
            'updated_at' => now(),
          ]);
        }
      }
    }
}
