<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Utility\Imports\CsvImport;
use App\Models\Programme;
use App\Models\Utilisateur;
use App\Models\Marche;
use App\Models\ActiviteProgramme;
use App\Models\Decompte;
use App\Models\Decaissement;
use App\Models\ValidationDecompte;
use App\Models\Prestataire;
use App\Models\MissionControle;
use Carbon;
use Illuminate\Support\Facades\Log;

class DecompteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $result = Excel::toArray(new CsvImport, public_path('files/excel/donnees_historiques/decomptesFinal.xlsx'));

      foreach ($result[0] as $key => $decompte) {
        if($key == 0) {
          continue;
        }

        if ($decompte[0]) {
       
        $db_decompte = Marche::find($decompte[0]);

          if(!$db_decompte) {
            $marche = Marche::where('numero_marche',$decompte[1])->first();
            $marche_maitre_oeuvre = Marche::join('prestataires','marches.maitre_oeuvre','=','prestataires.id')
                                            ->where('numero_marche',$decompte[1])
                                            ->select('prestataires.libelle AS libelle_prestataire')
                                            ->first();
           
            if ($marche_maitre_oeuvre) {
              $type_decompte = $x = ($marche_maitre_oeuvre->libelle_prestataire == 'AGEROUTE' ? 'decompte_travaux_ageroute' : ($marche_maitre_oeuvre->libelle_prestataire == 'DGIR' ? 'decompte_travaux_dgir' : 'autre_decompte'));
            }
            //var_dump($type_decompte);
                                         
            $DT = Utilisateur::select('id')->where('type_utilisateur','DT')->first();
            $DAF = Utilisateur::select('id')->where('type_utilisateur','DAF')->first();
              // var_dump($decompte[1]);
              //Log::info('profilID',['id'=>$DT->id]);
              
         
            $validation4=($decompte[4] == null) ? null : \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($decompte[4]);
            $validation6=($decompte[6] == null) ? null : \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($decompte[6]);
            $validation9=($decompte[9] == null) ? null : \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($decompte[9]);
            $validation7=($decompte[7] == null) ? null : \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($decompte[7]);


            Decompte::create([ 
                'numero_decompte' => $decompte[2],
                'montant_facture_decompte' => $decompte[3],
                'marche' => $marche->id,
                'type_decompte' => $type_decompte,
                'created_at' => $validation4,
                'updated_at' => null,
            ]);

            $decompte = Decompte::where('numero_decompte',$decompte[2])->orderBy('decomptes.id', 'desc')->first();
            ValidationDecompte::create([ 
              'decompte' => $decompte->id,
              'validateur' => $DT->id,//A corriger plutard
              'statut_validation'=>'VALIDE',
              'rang_validation'=>'avant_dernier',
              'created_at' => $validation6,
              'updated_at' => null,
            ]);

            ValidationDecompte::create([ 
              'decompte' => $decompte->id,
              'validateur' => $DAF->id,//A corriger plutard
              'statut_validation'=>'VALIDE',
              'rang_validation'=>'dernier',
              'created_at' => $validation7,
              'updated_at' => null,
            ]);

            Decaissement::create([
              'decompte' => $decompte->id,
              'created_at' => $validation9,
              'updated_at' => null,
            ]);
        }
      }

      }
    }
}