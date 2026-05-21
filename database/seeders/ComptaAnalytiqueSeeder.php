<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Utility\Imports\CsvImport;
use App\Models\CompteComptaAnalytique;
use App\Models\GroupeComptaAnalytique;
use Illuminate\Support\Facades\Log;

class ComptaAnalytiqueSeeder extends Seeder 
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //importation des groupes analytique
        $result = Excel::toArray(new CsvImport, public_path('files/excel/groupeAnalytique.xlsx'));

        foreach ($result[0] as $key => $groupe_analytique) {
            if($key == 0) {
              continue;
            }
    
            $db_groupe_analytique = GroupeComptaAnalytique::find($groupe_analytique[0]);
    
            if(!$db_groupe_analytique) {
                GroupeComptaAnalytique::create([
                    'departement' => $groupe_analytique[0],
                    'libelle' => $groupe_analytique[1],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        //importation des comptes analytique
        $result0 = Excel::toArray(new CsvImport, public_path('files/excel/compteAnalytique.xlsx'));

        foreach ($result0[0] as $key => $compte_analytique) {
            if($key == 0) {
                continue;
            }
    
            $db_compte_analytique = CompteComptaAnalytique::find($compte_analytique[0]);
            $groupe_analytique = GroupeComptaAnalytique::where('libelle',$compte_analytique[1])->where('departement',$compte_analytique[0])->orderBy('id','desc')->first();

            if(!$db_groupe_analytique) {
                CompteComptaAnalytique::create([
                    'numero' => $compte_analytique[2],
                    'libelle' => $compte_analytique[3],
                    'groupe_compta_analytique' => $groupe_analytique->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
       
    }
}
