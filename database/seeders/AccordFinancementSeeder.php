<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Utility\Imports\CsvImport;
use App\Models\Marche;
use App\Models\AccordFinancement;
use Carbon;
use Illuminate\Support\Facades\Log;

class AccordFinancementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $result = Excel::toArray(new CsvImport, public_path('files/excel/donnees_historiques/accordDeFinancement.xlsx'));
      foreach ($result[0] as $key => $accord_financement) {
        if($key == 0) {
          continue;
        }

        $db_accord_financement = AccordFinancement::find($accord_financement[1]);

        if(!$db_accord_financement) {
          $marche = Marche::where('numero_marche',$accord_financement[1])->first();
          // var_dump($accord_financement[1]);
          // var_dump($marche->id);

          AccordFinancement::create([ 
            'ref_accord_financement'=> $accord_financement[2],
            'date_accord_financement' => is_numeric($accord_financement [3]) ? (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($accord_financement [3])) : $accord_financement [3],    
            'marche'=> $marche->id,       
          ]);

      }

      }
    }
}