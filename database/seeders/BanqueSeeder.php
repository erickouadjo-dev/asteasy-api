<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Utility\Imports\CsvImport;
use App\Models\Banque;
use App\Models\Marche;
use Illuminate\Support\Facades\Log;

class BanqueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $result = Excel::toArray(new CsvImport, public_path('files/excel/banques.xlsx'));

      $marche = Marche::get();
      Log::info('marche',['moi'=>$marche]);
      foreach ($result[0] as $key => $banque) {
        if($key == 0) {
          continue;
        }

        $db_banque = Banque::find($banque[0]);

        if(!$db_banque) {
          Banque::create([
            'id' => $banque[0],
            'sigle' => $banque[1],
            'denomination' => $banque[2],
          ]);
        }
      }
    }
}
