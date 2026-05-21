<?php

namespace App\Utility\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;
use App\Models\Decompte;
use App\Models\Marche;
use App\Models\Utilisateur;
use App\Models\Sous_traitant;
use Carbon;
use Log;

class ImporterDecomptes implements ToCollection, WithHeadingRow
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
        $this->Decompte = collect();
        foreach ($rows as $key => $value) 
        {
          if ($value['marches']) {
       
            $db_decompte = Marche::find($value['marches']);
              if($db_decompte) {
               
                $marche = Marche::where('numero_marche',$value['marches'])->first();
                $marche_maitre_oeuvre = Marche::join('prestataires','marches.maitre_oeuvre','=','prestataires.id')
                                                ->where('numero_marche',$value['marches'])
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
                  
             
                $validation4=($value['date_emission_decompte'] == null) ? null : \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_emission_decompte']);
                $validation6=($value['date_reception_dossier_paiement_dt'] == null) ? null : \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_reception_dossier_paiement_dt']);
                $validation9=($value['date_paiement_effectue_sur_decompte'] == null) ? null : \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_paiement_effectue_sur_decompte']);
                $validation7=($value['date_transmission_dossier_paiement_daf'] == null) ? null : \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_transmission_dossier_paiement_daf']);
    
    
                Decompte::create([ 
                    'numero_decompte' => $value['numero_decompte'],
                    'montant_facture_decompte' => $value['montant_facture_decompte'],
                    'marche' => $marche->id,
                    'type_decompte' => $type_decompte,
                    'created_at' => $validation4,
                    'updated_at' => null,
                ]);
    
                // $decompte = Decompte::where('numero_decompte',$value['numero_decompte'])->orderBy('decomptes.id', 'desc')->first();
                // ValidationDecompte::create([ 
                //   'decompte' => $decompte->id,
                //   'validateur' => $DT->id,//A corriger plutard
                //   'statut_validation'=>'VALIDE',
                //   'rang_validation'=>'avant_dernier',
                //   'created_at' => $validation6,
                //   'updated_at' => null,
                // ]);
    
                // ValidationDecompte::create([ 
                //   'decompte' => $decompte->id,
                //   'validateur' => $DAF->id,//A corriger plutard
                //   'statut_validation'=>'VALIDE',
                //   'rang_validation'=>'dernier',
                //   'created_at' => $validation7,
                //   'updated_at' => null,
                // ]);
    
                // Decaissement::create([
                //   'decompte' => $decompte->id,
                //   'created_at' => $validation9,
                //   'updated_at' => null,
                // ]);
            }
          }
        
        /*foreach ($rows as $key => $value) 
        {
          //verification marche
          $libelle_marche = Marche::where('libelle', $value['marche'])->first();
          //Log::info('ddd',['prestataire'=>$libelle_prestataire]);
          if(is_null($libelle_marche)) {
            throw new \Exception("Libelle marché ". $value['marche']." est inexistant", 1); 
          }
  
          //verification département
          $libelle_sous_traitant = Sous_traitant::where('libelle', $value['sous_traitant'])->first();
          //Log::info('ddd',['département'=>$value['sousTraitant']]);
          if(is_null($libelle_sous_traitant)) {
            throw new \Exception("Libelle sous-traitant ". $value['sous_traitant']." est inexistant", 1); 
          }
  
          if ($value['type_decompte']=='decompte_travaux_ageroute') {
            Log::info('ddd',['département'=>'decompte_travaux_ageroute']);
            $decompte = Decompte::create([ 
               //Décompte travaux AGEROUTE
               'marche' => $libelle_marche->id,
               'numero_decompte' =>$value['numero_decompte'],
               'sous_traitant' => $libelle_sous_traitant->id,
               'type_entretien' => $value['type_entretien'],
               'objet_travaux' => $value['objet_travaux'],
               'date_approbation' => Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_approbation'])),
               'montant_ordonnancement' => $value['montant_ordonnancement'],
               'date_ordonnancement' => Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_ordonnancement'])),
               'date_debut_execution' => Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_debut_execution'])),
               'date_fin_execution' => Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_fin_execution'])),
               'date_ordre_service' => Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_ordre_service'])),
               'date_demarrage_effectif' => Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_demarrage_effectif'])),
               'caution_avance_demarrage' => $value['caution_avance_demarrage'],
               'retenue_garantie' => $value['retenue_garantie'],
               'cautionnement_definitif' => $value['cautionnement_definitif'],
               'proces_verbal_reception_travaux' => $value['proces_verbal_reception_travaux'],
               'decompte' => $value['decompte'],
               'attachement' => $value['attachement'],
               'situation_financiere_marche' => $value['situation_financiere_marche'],
               'facture' => $value['facture'],
               'montant_facture_decompte' => $value['montant_facture_decompte'],
               'reference_facture' =>$value['reference_facture'],
               'date_facture' => Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_facture'])),
               'observation' => $value['observation'],
               'type_decompte' => $value['type_decompte'],
            ]);
          }elseif ($value['type_decompte']=='decompte_intellectuel_ageroute') {
            Log::info('ddd',['département'=>'decompte_intellectuel_ageroute']);
            $decompte = Decompte::create([ 
              //décompte intellectuel AGEROUTE
              'marche' => $libelle_marche->id,
              'numero_decompte' =>$value['numero_decompte'],
              'sous_traitant' => $libelle_sous_traitant->id,
              'type_entretien' => $value['type_entretien'],
              'objet_travaux' => $value['objet_travaux'],
              'date_approbation' => $value['date_approbation'],
              'montant_ordonnancement' => $value['montant_ordonnancement'],
              'date_ordonnancement' => Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_ordonnancement'])),
              'date_debut_execution' => Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_debut_execution'])),
              'date_fin_execution' => Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_fin_execution'])),
              'date_ordre_service' => Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_ordre_service'])),
              'date_demarrage_effectif' => Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_demarrage_effectif'])),
              'caution_avance_demarrage' => $value['caution_avance_demarrage'],
              'retenue_garantie' => $value['retenue_garantie'],
              'cautionnement_definitif' => $value['cautionnement_definitif'],
              'proces_verbal_reception_travaux' =>$value['proces_verbal_reception_travaux'],
              'decompte' => $value['decompte'],
              'attachement' => $value['attachement'],
              'situation_financiere_marche' => $value['situation_financiere_marche'],
              'facture' => $value['facture'],
              'montant_facture_decompte' => $value['montant_facture_decompte'],
              'reference_facture' => $value['reference_facture'],
              'date_facture' => Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_facture'])),
              'observation' => $value['observation'],
              'rapport' => $value['rapport'],
              'reference_attestation_rc' => $value['reference_attestation_rc'],
              'reference_accord_financement' => $value['reference_accord_financement'],
              'type_decompte' => $value['type_decompte'],
            ]);
          }elseif ($value['type_decompte']=='decompte_travaux_dgir') {
              //Décompte travaux DGIR
              Log::info('ddd',['département'=>'decompte_travaux_dgir']);
            $decompte = Decompte::create([ 
              'marche' => $libelle_marche->id,
              'numero_decompte' =>$value['numero_decompte'],
              'sous_traitant' => $libelle_sous_traitant->id,
              'type_entretien' => $value['type_entretien'],
              'reference_lettre_commande' =>  $value['reference_lettre_commande'],
              'date_lettre_commande' =>  Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_lettre_commande'])),
              'date_ordre_service' =>  Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_ordre_service'])),
              'date_demarrage_effectif' =>  Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_demarrage_effectif'])),
              'courrier_indication_ligne_lcd' => $value['courrier_indication_ligne_lcd'],
              'demande_proposition_prix' => $value['demande_proposition_prix'],
              'courrier_invitation_sn' => $value['courrier_invitation_sn'],
              'courrier_confirmation_prix' => $value['courrier_confirmation_prix'],
              'bon_livraison' => $value['bon_livraison'],
              'certificat_service_fait' => $value['certificat_service_fait'],
              'situation_financiere_marche' => $value['situation_financiere_marche'],
              'facture' => $value['facture'],
              'montant_facture_decompte' => $value['montant_facture_decompte'],
              'reference_facture' => $value['reference_facture'],
              'date_facture' => Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_facture'])),
              'observation' => $value['observation'],
              'type_decompte' => $value['type_decompte'],
            ]);
          }elseif ($value['type_decompte']=='decompte_intellectuel_dgir') {
              //décompte intellectuel DGIR
              Log::info('ddd',['département'=>'decompte_intellectuel_dgir']);
            $decompte = Decompte::create([ 
              'marche' => $libelle_marche->id,
              'numero_decompte' =>$value['numero_decompte'],
              'sous_traitant' => $libelle_sous_traitant->id,
              'type_entretien' => $value['type_entretien'],
              'objet_travaux' => $value['objet_travaux'],
              'date_approbation' => Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_approbation'])),
              'montant_ordonnancement' => $value['montant_ordonnancement'],
              'date_ordonnancement' => Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_ordonnancement'])),
              'date_debut_execution' => Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_debut_execution'])),
              'date_fin_execution' => Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_fin_execution'])),
              'date_ordre_service' => Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_ordre_service'])),
              'date_demarrage_effectif' => Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_demarrage_effectif'])),
              'caution_avance_demarrage' => $value['caution_avance_demarrage'],
              'retenue_garantie' => $value['retenue_garantie'],
              'cautionnement_definitif' => $value['cautionnement_definitif'],
              'proces_verbal_reception_travaux' =>$value['proces_verbal_reception_travaux'],
              'decompte' => $value['decompte'],
              'attachement' => $value['attachement'],
              'situation_financiere_marche' => $value['situation_financiere_marche'],
              'facture' => $value['facture'],
              'montant_facture_decompte' => $value['montant_facture_decompte'],
              'reference_facture' => $value['reference_facture'],
              'date_facture' => Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_facture'])),
              'observation' => $value['observation'],
              'rapport' => $value['rapport'],
              'reference_attestation_rc' => $value['reference_attestation_rc'],
              'reference_accord_financement' => $value['reference_accord_financement'],
              'type_decompte' => $value['type_decompte'],
            ]);
          }
*/
            //$programmes = Programme::orderBy('id','desc')->first();
    
          //$this->Decompte->push($decompte);
        }
        return $this->Decompte;
      }

      // public function rules(): array
      // {
      //   return [
      //     'numero_decompte' => 'required|string',
      //     'date_ordre_service' =>'required',
      //     'date_demarrage_effectif' =>'required',
      //     'montant_facture_decompte' =>'required',
      //     'reference_facture' =>'required|string',
      //     'sous_traitant' =>'required|string',
      //     'type_decompte' =>'required|string',
      //   ];
      // }
}