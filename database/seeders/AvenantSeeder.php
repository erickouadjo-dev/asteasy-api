<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Utility\Imports\CsvImport;
use App\Models\Programme;
use App\Models\Avenant;
use App\Models\Marche;

use Carbon;
use Illuminate\Support\Facades\Log;

class AvenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $result = Excel::toArray(new CsvImport, public_path('files/excel/donnees_historiques/avenantsFinal.xlsx'));

      foreach ($result[0] as $key => $avenant) {
        if($key == 0) {
          continue;
        }

        $db_avenant = Avenant::find($avenant[0]);

        if(!$db_avenant) {
          $marche = Marche::where('numero_marche',$avenant[0])->first();
          //var_dump($avenant[0]);
          //var_dump($marche->id);
          Avenant::create([ 
            'ref_avenant' =>$avenant [1],
            'nature_avenant' =>$avenant [2],
            'montant_avenant' =>$avenant [3],
            'date_signature_attributaire' =>is_numeric($avenant [4]) ? (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($avenant [4])) : $avenant [4],    
            'date_signature_autorite_contractante' =>$avenant [5],
            'date_approbation' => $avenant [6],
            'marche'=> $marche->id,
          ]);

      }

      }
    }
}