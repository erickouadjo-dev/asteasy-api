<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Utility\Imports\CsvImport;
use App\Models\Prestataire;

class PrestataireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $result=Excel::toArray(new CsvImport, public_path('files/excel/donnees_historiques/prestataires_final.xlsx'));
       
        foreach ($result[0] as $key => $prestataire) {
            if ($key == 0) {
                continue;
            }
            $db_prestataire = Prestataire::where('libelle',$prestataire[0])->first();
            //var_dump($db_prestataire->libelle);
            if (!$db_prestataire) {
                Prestataire::create([
                    'libelle' => $prestataire[0],
                ]);
            }
        }
    }
}
