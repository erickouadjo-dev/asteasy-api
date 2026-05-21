<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Utility\Imports\CsvImport;
use App\Models\SaisieOperation;
use Illuminate\Support\Facades\Log;

class SaisieOperationSeeder extends Seeder 
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //importation des produits

        $result=Excel::toArray(new CsvImport, public_path('files/excel/donnees_historiques/produits_BA.xlsx'));
        foreach ($result[0] as $key => $saisie_opreation) {
            if ($key == 0) {
                continue;
            }
    
            //Log::info('test',['valeur3'=> ((strlen(strval($saisie_opreation[0])) == 4) ? intval(strval($saisie_opreation[0]).'0000') : ((strlen(strval($saisie_opreation[0])) == 6) ? (intval(strval($saisie_opreation[0]).'00')) : ((strlen(strval($saisie_opreation[0])) == 2) ? (intval(strval($saisie_opreation[0]).'000000')) : 0))) ]);
            $db_saisie_opreation = SaisieOperation::find($saisie_opreation[0]);
            if (!$db_saisie_opreation) {
                SaisieOperation::create([
                    'numero_compte' => ((strlen(strval($saisie_opreation[0])) == 4) ? intval(strval($saisie_opreation[0]).'0000') : ((strlen(strval($saisie_opreation[0])) == 6) ? (intval(strval($saisie_opreation[0]).'00')) : ((strlen(strval($saisie_opreation[0])) == 2) ? (intval(strval($saisie_opreation[0]).'000000')) : 0))), 
                    'libelle' => $saisie_opreation[1],
                    'mouvement' => $saisie_opreation[2],
                    'montant' => $saisie_opreation[3],
                    'date' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($saisie_opreation[4]),
                    'type_saisie' => $saisie_opreation[5],
                    'type_comptabilite' => $saisie_opreation[6],
                ]);
            }
        }
        //importation des charges

        $result=Excel::toArray(new CsvImport, public_path('files/excel/donnees_historiques/charges_BA.xlsx'));
        foreach ($result[0] as $key => $saisie_opreation) {
            if ($key == 0) {
                continue;
            }
    
            //Log::info('test',['valeur3'=> ((strlen(strval($saisie_opreation[0])) == 4) ? intval(strval($saisie_opreation[0]).'0000') : ((strlen(strval($saisie_opreation[0])) == 6) ? (intval(strval($saisie_opreation[0]).'00')) : ((strlen(strval($saisie_opreation[0])) == 2) ? (intval(strval($saisie_opreation[0]).'000000')) : 0))) ]);
            $db_saisie_opreation = SaisieOperation::find($saisie_opreation[0]);
            if (!$db_saisie_opreation) {
                SaisieOperation::create([
                    'numero_compte' => ((strlen(strval($saisie_opreation[0])) == 4) ? intval(strval($saisie_opreation[0]).'0000') : ((strlen(strval($saisie_opreation[0])) == 6) ? (intval(strval($saisie_opreation[0]).'00')) : ((strlen(strval($saisie_opreation[0])) == 2) ? (intval(strval($saisie_opreation[0]).'000000')) : 0))), 
                    'libelle' => $saisie_opreation[1],
                    'mouvement' => $saisie_opreation[2],
                    'montant' => $saisie_opreation[3],
                    'date' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($saisie_opreation[4]),
                    'type_saisie' => $saisie_opreation[5],
                    'type_comptabilite' => $saisie_opreation[6],
                ]);
            }
        }
    }
}
