<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Utility\Imports\CsvImport;
use App\Models\Programme;
use App\Models\Marche;
use App\Models\ActiviteProgramme;
use App\Models\Sous_traitant;
use App\Models\Prestataire;
use App\Models\MissionControle;
use Carbon;
use Illuminate\Support\Facades\Log;

class MarcheSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $result = Excel::toArray(new CsvImport, public_path('files/excel/donnees_historiques/MarcheTermine.xlsx'));

      foreach ($result[0] as $key => $marche) {
        if($key == 0) {
          continue;
        }

        if ($marche[0]) {
       
        $db_marche = Marche::find($marche[0]);

          if(!$db_marche) {
            $activite_programme = ActiviteProgramme::join('programmes','programmes.id','=','activites_programmes.programme')
                                                  ->select('activites_programmes.id AS id_activite_programme')
                                                  ->where('programmes.nom_programme',$marche[0])
                                                  ->first();
            //var_dump($activite_programme->id_activite_programme);

            $maitre_oeuvre= Prestataire::where('libelle',$marche [18])->first();
            $entreprise= Sous_traitant::where('libelle',$marche [11])->first();

            //$date_cautionnement_def = $marche [23];
           
            // if (is_numeric($date_cautionnement_def)) {
            //   var_dump(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date_cautionnement_def));
            // }else {
            //   var_dump($date_cautionnement_def);
            // }
            
            Marche::create([ 
              'nature_reseau'=> $marche[2],
              'numero_marche' => $marche[8],
              'activite_programme'=> $activite_programme->id_activite_programme,
              'type_entretien' =>$marche [3],
              'lot' =>$marche [4],
              'categorie_travaux' =>$marche [5],
              'objet' =>$marche [6],
              'departements' =>$marche [7],
              'numero_marche_ou_lettre_commande' =>$marche [8],
              'rf_ano' =>$marche [9],
              'montant_inital_marche' =>$marche [10],
              'entreprise' => is_null($entreprise) ? $entreprise : $entreprise->id,
              'entreprise_historique' => $marche [11],
              'date_sinature_attributaire' => is_numeric($marche [12]) ? (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($marche [12])) : $marche [12],
              'date_signature_autorite_contractante' => is_numeric($marche [13]) ? (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($marche [13])) : $marche [13],
              'date_approbation_marche' => is_numeric($marche [14]) ? (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($marche [14])) : $marche [14],
              'delai_exécution_marche' => $marche [15],
              'references_ordre_service_demarrage' => $marche [16],
              'date_demarrage_effectif' => is_numeric($marche [17]) ? (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($marche [17])) : $marche [17],
              'date_etablissement_cautionnement_ad' => is_numeric($marche [20]) ? (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($marche [20])) : $marche [20],
              'date_expiration_cautionnement_ad' => is_numeric($marche [21]) ? (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($marche [21])) : $marche [21],
              'date_etablissement_cautionnement_definitif' => is_numeric($marche [22]) ? (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($marche [22])) : $marche [22],
              'date_expiration_cautionnement_definitif' => is_numeric($marche [23]) ? (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($marche [23])) : $marche [23],
              'date_etablissement_cautionnement_rg' => is_numeric($marche [24]) ? (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($marche [24])) : $marche [24],
              'date_expiration_cautionnement_rg' => is_numeric($marche [25]) ? (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($marche [25])) : $marche [25],
              'maitre_oeuvre' => is_null($maitre_oeuvre) ? $maitre_oeuvre : $maitre_oeuvre->id,
              'maitre_oeuvre_historique' =>$marche [18],
              'mission_suivi_controle_historique' =>$marche [19],
              'type_donnees' => 'HISTORIQUES',
            ]);

        }
      }

      }
    }
}