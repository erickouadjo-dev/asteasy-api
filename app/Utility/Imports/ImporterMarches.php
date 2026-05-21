<?php

namespace App\Utility\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;
use App\Models\Marche;
use App\Models\Activite;
use App\Models\Programme;
use App\Models\Prestataire;
use App\Models\Departement;
use App\Models\ActiviteProgramme;
use App\Models\Sous_traitant;
use App\Models\MissionControle;
use Carbon;
use Log;

class importerMarches implements ToCollection, WithHeadingRow
{
    use Importable, SkipsFailures;
    public function __construct($utilisateur)
      {
        $this->utilisateur = $utilisateur;
      }
  
      /**
      * @param Collection $collection
      */
    
    public function collection(Collection $rows)
    {
      $this->Marche = collect();
        
      foreach ($rows as $key => $value) 
      {
        
        $activite_programme = ActiviteProgramme::join('programmes','programmes.id','=','activites_programmes.programme')
                                                ->select('activites_programmes.id AS id_activite_programme')
                                                ->where('programmes.nom_programme',$value['nom_programme'])
                                                ->first();

        $maitre_oeuvre= Prestataire::where('libelle',$value ['maitre_oeuvre'])->first();
        $mission_controle= MissionControle::where('libelle',$value ['mission_suivi_controle'])->first();
        $entreprise= Sous_traitant::where('libelle',$value ['entreprise'])->first();

        //var_dump($value['nom_programme']);     
        Log::info('Test',['Montant'=>$value['montant_initial_marche']]);
         
        $marche = Marche::create([ 
            'libelle'=> $value['libelle'],
            'nature_reseau'=> $value['nature_reseau'],
            'numero_marche' => $value['numero_marche_ou_lettre_commande'],
            'activite_programme'=> $activite_programme->id_activite_programme,
            'type_entretien' =>$value ['type_entretien'],
            'lot' =>$value ['lot'],
            // 'categorie_travaux' =>$value ['categorie_travaux'],
            'objet' =>$value ['objet'],
            'departements' =>$value ['departements'],
            'numero_marche_ou_lettre_commande' =>$value ['numero_marche_ou_lettre_commande'],
            'rf_ano' =>$value ['rf_ano'],
            'montant_initial_marche' =>$value['montant_initial_marche'],
            'entreprise' => is_null($entreprise) ? $entreprise : $entreprise->id,
            'entreprise_historique' =>$value ['entreprise'],
            'date_sinature_attributaire' =>is_numeric($value ['date_sinature_attributaire']) ? (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value ['date_sinature_attributaire'])) : $value['date_sinature_attributaire'],
            'date_signature_autorite_contractante' =>is_numeric($value ['date_signature_autorite_contractante']) ?  (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value ['date_signature_autorite_contractante'])) : $value['date_signature_autorite_contractante'],
            'date_approbation_marche' =>is_numeric($value ['date_approbation_marche']) ?  (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value ['date_approbation_marche'])) : $value['date_approbation_marche'],
            'delai_execution_marche' =>$value ['delai_execution_marche'],
            'references_ordre_service_demarrage' =>$value ['references_ordre_service_demarrage'],
            'date_demarrage_effectif' =>is_numeric($value ['date_demarrage_effectif']) ?  (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value ['date_demarrage_effectif'])) : $value['date_demarrage_effectif'],
            'date_etablissement_cautionnement_ad' =>is_numeric($value ['date_etablissement_cautionnement_ad']) ?  (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value ['date_etablissement_cautionnement_ad'])) : $value['date_etablissement_cautionnement_ad'],
            'date_expiration_cautionnement_ad' =>is_numeric($value ['date_expiration_cautionnement_ad']) ?  (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value ['date_expiration_cautionnement_ad'])) : $value['date_expiration_cautionnement_ad'],
            'date_etablissement_cautionnement_definitif' =>is_numeric($value ['date_etablissement_cautionnement_definitif']) ?  (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value ['date_etablissement_cautionnement_definitif'])) : $value['date_etablissement_cautionnement_definitif'],
            'date_expiration_cautionnement_definitif' =>is_numeric($value ['date_expiration_cautionnement_definitif']) ?  (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value ['date_expiration_cautionnement_definitif'])) : $value['date_expiration_cautionnement_definitif'],
            'date_etablissement_cautionnement_rg' =>is_numeric($value ['date_etablissement_cautionnement_rg']) ?  (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value ['date_etablissement_cautionnement_rg'])) : $value['date_etablissement_cautionnement_rg'],
            'date_expiration_cautionnement_rg' =>is_numeric($value ['date_expiration_cautionnement_rg']) ?  (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value ['date_expiration_cautionnement_rg'])) : $value['date_expiration_cautionnement_rg'],
            'mission_suivi_controle' =>is_null($mission_controle) ? $mission_controle : $mission_controle->id,
            'maitre_oeuvre' => is_null($maitre_oeuvre) ? $maitre_oeuvre : $maitre_oeuvre->id,
            'maitre_oeuvre_historique' =>$value ['maitre_oeuvre'],
            'mission_suivi_controle_historique' =>$value ['mission_suivi_controle'],
            'type_donnees' => 'HISTORIQUES',

            // nature_reseau
            // numero_marche
            // activite_programme
            // type_entretien
            // lot
            // categorie_travaux
            // objet
            // departements
            // numero_marche_ou_lettre_commande
            // rf_ano
            // montant_inital_marche
            // entreprise
            // entreprise_historique
            // date_sinature_attributaire
            // date_signature_autorite_contractante
            // date_approbation_marche
            // delai_exécution_marche
            // references_ordre_service_demarrage
            // date_demarrage_effectif
            // date_etablissement_cautionnement_ad
            // date_expiration_cautionnement_ad
            // date_etablissement_cautionnement_definitif
            // date_expiration_cautionnement_definitif
            // date_etablissement_cautionnement_rg
            // date_expiration_cautionnement_rg
            // maitre_oeuvre
            // maitre_oeuvre_historique
            // mission_suivi_controle_historique
            // type_donnees
          ]);
  
        $this->Marche->push($marche);
      }
      return $this->Marche;
    }
    // public function rules(): array
    // {
    //   return [
    //     'sigle_banque' => 'string',
    //   ];
    // }

    
}

