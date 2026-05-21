<?php
namespace App\Utility;

use App\Models\BpHypothese;
use App\Models\BpInvestissement;
use App\Models\BpProjectionTarif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell;
use Carbon\Carbon;
use DB;

class BusinessPlan{
   
    //Dépenses d'investissement
    const REMPLACEMENT_DES_VEHICULES_AMORTIS = 'Remplacement des véhicules amortis';
    const RENOUVELLEMENT_DES_ORDINATEURS_AMORTIS = 'Renouvellement des ordinateurs amortis';
    const RENOUVELLEMENT_DES_TELEPHONES_FIXES_AMORTIS = 'Renouvellement des téléphones fixes amortis';
    const RENOUVELLEMENT_DU_MOBILIER_AMORTI = 'Renouvellement du mobilier amorti';
    const AURES_AMORTI = 'Autres amorti';

    public static $Pick_Up  = array();
    public static $Berline = array();
    public static $Bureau = array();
    public static $Fauteuil_agent = array();
    public static $Fauteuil_cabine = array();
   
    const PICKUP = 'Pick-Up';
    const BERLINE = 'Berline';
    const FAUTEUIL_CABINE = 'Fauteuil cabine';
    const FAUTEUIL_AGENT = 'Fauteuil agent';
    const BUREAU = 'Bureau';
    const TELEPHONE_FIXE = 'Téléphone fixe';
    const TELEPHONE = 'Ordinateur';


    public static $Remplacement_des_vehicules_amortis = array();
    public static $Renouvellement_des_ordinateurs_amortis = array();
    public static $Renouvellement_des_telephones_fixes_amortis = array();
    public static $Renouvellement_du_mobilier_amortis = array();
    public static $Autres_amortis = array();
    
    //Charges d'exploitation
    const ACHAT = 'Achats (carburant, fournitures, etc.)'; 
    const TRANSPORT = 'Transports (transport du personnel et des plis, etc.)'; 
    const SEA = 'SEA'; 
    const SEB = 'SEB'; 
    const AUTRES_CHARGES = 'Autres charges'; 
    const CHARGES_DE_PERSONNELS = 'Charges de personnel'; 

    public static $Achats = array();
    public static $Transports = array();
    public static $SEAs = array();
    public static $SEBs = array();
    public static $Charges_de_personnels = array();
    public static $Autres_charges = array();

    //Provisions et dépenses en entretien routier
    const ENTRETIEN_COURANT = 'Entretien courant';
    const PROVISIONS_POUR_ENTRETIEN_PERIODIQUE_NECESSAIRE = 'Provisions pour entretien périodique nécessaire';
    const PROVISIONS_POUR_ENTRETIEN_PERIODIQUE_PART_DU_FER = 'Provisions pour entretien périodique (part du FER)';

    public static $Entretien_courants = array();
    public static $Provisions_pour_entretien_periodique_necessaires = array();
    public static $Provisions_pour_entretien_periodique_part_du_FERs = array();
    
    //Ressoucres additionnelles
    const RESSOURCES_ADDITIONNELLES = 'Ressources additionnelles';
    public static $Ressources_additionnelles = array();

    //Recettes gares payages ou projections des tarifis annuels
    public static $Recettes_des_gare_de_peages = array();

    //Levee de fond
    public static $Annee_tranches = array();
    public static $Montant_tranches = array();

    //Entete
    public static $Service_de_la_dette = 0;
    public static $Total_investment = 0;

    public static function generer(Request $request){
        try{
            $result = [
                'code_http' => 200,
                'code_message' => 200,
                'business_plan' => [],
            ];
           
            $inputs = json_decode($request->getContent(), true);

            if(!is_array($inputs)){
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_VALIDATION';
                $result['erreurs'] = 'Corps de la requête vide.';
                return $result;
            }

            $rules = [
                'hypotheses' => 'required|numeric',
                'investissements' => 'required|numeric',
            ];

            $validator = Validator::make($inputs, $rules);
            if(!$validator->passes()){
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_VALIDATION';
                $result['erreurs'] = $validator->errors()->all();
                return $result;
            }

            $hypotheses = BpHypothese::where('id', $inputs['hypotheses'])
                ->where('initiateur', $request->user()->id)
                ->first();
            if(is_null($hypotheses)){
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_HYPOTHESES';
                return $result;
            }

            $investissements = BpInvestissement::where('id', $inputs['investissements'])
                ->where('initiateur', $request->user()->id)
                ->first();
            if(is_null($investissements)){
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_INVESTISSEMENTS';
                return $result;
            }  
            
            $curseur_ligne = 1;
            $curseur_colonne = 1;

            $nombre_annees = intval($hypotheses->b15);
            $annee_initiale = intval(substr($hypotheses->b2, 0, 4));
            $annees = [$annee_initiale-1, $annee_initiale];
            for($i=$annee_initiale; $i<$annee_initiale+$nombre_annees; $i++){
                $annees[] = $i;
            }

            $investissements_type3 =  DB::table('bp_investissements_type3')
                ->where('investissement', $investissements->id)
                ->orderBy('id', 'asc')
                ->get();

            $investissements_type4 =  DB::table('bp_investissements_type4')
                ->where('investissement', $investissements->id)
                ->orderBy('id', 'asc')
                ->get();

            $investissements_type5 =  DB::table('bp_investissements_type5')
                ->where('investissement', $investissements->id)
                ->orderBy('id', 'asc')
                ->get();

            $investissements_type6 =  DB::table('bp_investissements_type6')
                ->where('investissement', $investissements->id)
                ->orderBy('id', 'asc')
                ->get();

            $maDate = $hypotheses->b2;
            $date = Carbon::createFromFormat('Y-m-d', $maDate)->format('Y');
                
            $annee_actuelle = intval($date);
            $annee_debut = $annee_actuelle - 5;
            $annee_fin = $annee_actuelle + 5;

            $projection_tarif_debuts = DB::table('bp_projection_tarifs')
                ->where('annee', $annee_debut)
                ->orderBy('id', 'asc')
                ->get();

            $projection_tarif_fins = DB::table('bp_projection_tarifs')
                ->where('annee', $annee_fin)
                ->orderBy('id', 'asc')
                ->get();

            $spreadsheet = new Spreadsheet();
            $spreadsheet->removeSheetByIndex(0);

            $resultatW = new Worksheet($spreadsheet, 'Resultat');
            $hypotheseW = new Worksheet($spreadsheet, 'Hypothese');
            $InvestissementW = new Worksheet($spreadsheet, 'Investissement');
            $Projection_tarifW = new Worksheet($spreadsheet, 'Projection des Tarifs');
            $Table_empruntW = new Worksheet($spreadsheet, 'Table des emprunts');

            $spreadsheet->addSheet($resultatW, 0);
            $spreadsheet->addSheet($hypotheseW, 1);
            $spreadsheet->addSheet($InvestissementW, 2);
            $spreadsheet->addSheet($Projection_tarifW, 3);
            $spreadsheet->addSheet($Table_empruntW, 4);

            $filename = sprintf('downloads/Business-Plan-%s.xlsx', time());
            $writer = new Xlsx($spreadsheet);
            $writer->save(base_path($filename));

            $result['url'] = $filename;

            $mes_resultats_interet = array();
            $annee = Carbon::createFromFormat('Y-m-d', $hypotheses->b2)->format('Y');//Date de debut de l'hypothèse (projection des tarifs)

        /** TABLE DES EMPRUNTS **/
            if ($hypotheses->h10 <> 0 ) {
                if ($hypotheses->h14 == 'ANNUITE') {
                    $result['business_plan']['table_emprunts'][] = self::table_emprunt($hypotheses->h10, $hypotheses->h4 , 1, 1, $hypotheses->h6 , $hypotheses->h3 ,$hypotheses->i10,$hypotheses->h9,'ANNUITE','Tranche_A');
                    //Log::info('hypothèse',['test periode tri' => $hypotheses->h14]);
                }elseif ($hypotheses->h14 == 'SEMESTRIALITE') {
                    $result['business_plan']['table_emprunts'][] = self::table_emprunt($hypotheses->h10, $hypotheses->h4 , 2, 2, $hypotheses->h6 , $hypotheses->h3 ,$hypotheses->i10,$hypotheses->h9,'SEMESTRIALITE','Tranche_A');
                    //Log::info('hypothèse',['test periode tri' => $hypotheses->h14]);
                }elseif ($hypotheses->h14 == 'TRIMESTRIALITE') {
                    $result['business_plan']['table_emprunts'][] = self::table_emprunt($hypotheses->h10, $hypotheses->h4 , 4, 4, $hypotheses->h6 , $hypotheses->h3 ,$hypotheses->i10,$hypotheses->h9,'TRIMESTRIALITE','Tranche_A');
                    //Log::info('hypothèse',['test periode tri' => $hypotheses->h14]);
                }elseif ($hypotheses->h14 == 'MENSUALITE') {
                    $result['business_plan']['table_emprunts'][] = self::table_emprunt($hypotheses->h10, $hypotheses->h4 , 12, 12, $hypotheses->h6 , $hypotheses->h3 ,$hypotheses->i10,$hypotheses->h9,'MENSUALITE','Tranche_A');
                    //Log::info('hypothèse',['test periode tri' => $hypotheses->h14]);
                }
            }
            if ($hypotheses->h11 <> 0 ) {
                if ($hypotheses->h14 == 'ANNUITE') {
                    $result['business_plan']['table_emprunts'][] = self::table_emprunt($hypotheses->h11, $hypotheses->h4 , 1, 1, $hypotheses->h6 , $hypotheses->h3 ,$hypotheses->i11,$hypotheses->h9,'ANNUITE','Tranche_B');
                    //Log::info('hypothèse',['test periode tri' => $hypotheses->h14]);
                }elseif ($hypotheses->h14 == 'SEMESTRIALITE') {
                    $result['business_plan']['table_emprunts'][] = self::table_emprunt($hypotheses->h11, $hypotheses->h4 , 2, 2, $hypotheses->h6 , $hypotheses->h3 ,$hypotheses->i11,$hypotheses->h9,'SEMESTRIALITE','Tranche_B');
                    //Log::info('hypothèse',['test periode tri' => $hypotheses->h14]);
                }elseif ($hypotheses->h14 == 'TRIMESTRIALITE') {
                    $result['business_plan']['table_emprunts'][] = self::table_emprunt($hypotheses->h11, $hypotheses->h4 , 4, 4, $hypotheses->h6 , $hypotheses->h3 ,$hypotheses->i11,$hypotheses->h9,'TRIMESTRIALITE','Tranche_B');
                    //Log::info('hypothèse',['test periode tri' => $hypotheses->h14]);
                }elseif ($hypotheses->h14 == 'MENSUALITE') {
                    $result['business_plan']['table_emprunts'][] = self::table_emprunt($hypotheses->h11, $hypotheses->h4 , 12, 12, $hypotheses->h6 , $hypotheses->h3 ,$hypotheses->i11,$hypotheses->h9,'MENSUALITE','Tranche_B');
                    //Log::info('hypothèse',['test periode tri' => $hypotheses->h14]);
                }            
            }
            if ($hypotheses->h12 <> 0 ) {
                    if ($hypotheses->h14 == 'ANNUITE') {
                        $result['business_plan']['table_emprunts'][] = self::table_emprunt($hypotheses->h12, $hypotheses->h4 , 1, 1, $hypotheses->h6 , $hypotheses->h3 ,$hypotheses->i12,$hypotheses->h9,'ANNUITE','Tranche_C');
                        //Log::info('hypothèse',['test periode tri' => $hypotheses->h14]);
                    }elseif ($hypotheses->h14 == 'SEMESTRIALITE') {
                        $result['business_plan']['table_emprunts'][] = self::table_emprunt($hypotheses->h12, $hypotheses->h4 , 2, 2, $hypotheses->h6 , $hypotheses->h3 ,$hypotheses->i12,$hypotheses->h9,'SEMESTRIALITE','Tranche_C');
                        //Log::info('hypothèse',['test periode tri' => $hypotheses->h14]);
                    }elseif ($hypotheses->h14 == 'TRIMESTRIALITE') {
                        $result['business_plan']['table_emprunts'][] = self::table_emprunt($hypotheses->h12, $hypotheses->h4 , 4, 4, $hypotheses->h6 , $hypotheses->h3 ,$hypotheses->i12,$hypotheses->h9,'TRIMESTRIALITE','Tranche_C');
                        //Log::info('hypothèse',['test periode tri' => $hypotheses->h14]);
                    }elseif ($hypotheses->h14 == 'MENSUALITE') {
                        $result['business_plan']['table_emprunts'][] = self::table_emprunt($hypotheses->h12, $hypotheses->h4 , 12, 12, $hypotheses->h6 , $hypotheses->h3 ,$hypotheses->i12,$hypotheses->h9,'MENSUALITE','Tranche_C');
                        //Log::info('hypothèse',['test periode tri' => $hypotheses->h14]);
                    }            
                }
            
            if ($hypotheses->h13 <> 0 ) {
                    if ($hypotheses->h14 == 'ANNUITE') {
                        $result['business_plan']['table_emprunts'][] = self::table_emprunt($hypotheses->h13, $hypotheses->h4 , 1, 1, $hypotheses->h6 , $hypotheses->h3 ,$hypotheses->i13,$hypotheses->h9,'ANNUITE','Tranche_D');
                        //Log::info('hypothèse',['test periode tri' => $hypotheses->h14]);
                    }elseif ($hypotheses->h14 == 'SEMESTRIALITE') {
                        $result['business_plan']['table_emprunts'][] = self::table_emprunt($hypotheses->h13, $hypotheses->h4 , 2, 2, $hypotheses->h6 , $hypotheses->h3 ,$hypotheses->i13,$hypotheses->h9,'SEMESTRIALITE','Tranche_D');
                        //Log::info('hypothèse',['test periode tri' => $hypotheses->h14]);
                    }elseif ($hypotheses->h14 == 'TRIMESTRIALITE') {
                        $result['business_plan']['table_emprunts'][] = self::table_emprunt($hypotheses->h13, $hypotheses->h4 , 4, 4, $hypotheses->h6 , $hypotheses->h3 ,$hypotheses->i13,$hypotheses->h9,'TRIMESTRIALITE','Tranche_D');
                        //Log::info('hypothèse',['test periode tri' => $hypotheses->h14]);
                    }elseif ($hypotheses->h14 == 'MENSUALITE') {
                        $result['business_plan']['table_emprunts'][] = self::table_emprunt($hypotheses->h13, $hypotheses->h4 , 12, 12, $hypotheses->h6 , $hypotheses->h3 ,$hypotheses->i13,$hypotheses->h9,'MENSUALITE','Tranche_D');
                        //Log::info('hypothèse',['test periode tri' => $hypotheses->h14]);
                    }            
                }
        /** PROJECTION FINANCIERE **/   
            $result['business_plan']['projection_tarif'][] = self::projection_tarifs(intval($annee)-5, intval($annee)+5, $hypotheses->b13, $hypotheses->e2,$hypotheses->e3,$hypotheses->e4,$hypotheses->e5,$hypotheses->b15,$hypotheses->b2, $hypotheses->b3, $hypotheses->b4, $hypotheses->b5, $hypotheses->b6);
        
        /** INVESTISSEMENT **/
            $result['business_plan']['investissements'][] = self::investissement($investissements_type3, $hypotheses->b2, $hypotheses->b15, $hypotheses->b9, $hypotheses->b10, $hypotheses->b7, $investissements_type4, $investissements_type5, $hypotheses->b14, $investissements_type6);
     
        /** RESULTAT **/
            //Log::info("display",['valeur' => $result['business_plan'][0]['table_emprunts']['Tranche A'][0]['taux_interet']]);         
           
            //Depense totales
            $depenses_total = array();
            $var_depense = 0;
             for ($i=0; $i < $hypotheses->b15; $i++) { 
                if (!empty(self::$Pick_Up[$i])) {
                    $var_depense += intval(self::$Pick_Up[$i]);
                }
                if (!empty(self::$Berline[$i])) {
                    $var_depense += intval(self::$Berline[$i]);
                }
                if (!empty(self::$Bureau[$i])) {
                    $var_depense += intval(self::$Bureau[$i]);
                }
                if (!empty(self::$Fauteuil_cabine[$i])) {
                    $var_depense += intval(self::$Fauteuil_cabine[$i]);
                }
                if (!empty(self::$Fauteuil_agent[$i])) {
                    $var_depense += intval(self::$Fauteuil_agent[$i]);
                }
                if (!empty(self::$Renouvellement_des_ordinateurs_amortis)) {
                    $var_depense += intval(self::$Renouvellement_des_ordinateurs_amortis[$i]);
                }
                if (!empty(self::$Renouvellement_des_telephones_fixes_amortis)) {
                    $var_depense += intval(self::$Renouvellement_des_telephones_fixes_amortis[$i]);
                }
                if (!empty(self::$Autres_amortis)) {
                    $var_depense += intval(self::$Autres_amortis[$i]);
                } 

                if (!empty(self::$Achats)) {
                    $var_depense += intval(self::$Achats[$i]); 
                } 
                if (!empty(self::$Transports)) {
                    $var_depense += intval(self::$Transports[$i]);
                } 
                if (!empty(self::$SEAs)) {
                    $var_depense += intval(self::$SEAs[$i]);
                }
                if (!empty(self::$SEBs)) {
                    $var_depense += intval(self::$SEBs[$i]);
                }
                if (!empty(self::$Charges_de_personnels)) {
                    $var_depense += intval(self::$Charges_de_personnels[$i]);
                }
                if (!empty(self::$Autres_charges)) {
                    $var_depense += intval(self::$Autres_charges[$i]);
                }
                if (!empty(self::$Entretien_courants)) {
                    $var_depense += intval(self::$Entretien_courants[$i]);
                }
                if (!empty(self::$Provisions_pour_entretien_periodique_necessaires)) {
                    $var_depense += intval(self::$Provisions_pour_entretien_periodique_necessaires[$i]);
                }
                if (!empty(self::$Provisions_pour_entretien_periodique_part_du_FERs)) {
                    $var_depense += intval(self::$Provisions_pour_entretien_periodique_part_du_FERs[$i]);
                }

                $depenses_total[] = $var_depense; 
             }

           /* Faire la correspondance des montants des tranches au dates qui conviennent */
            $Valeur_tranches = array();
            $Valeur_tranches1 = array();

            $cpteur = 0;
            for ($i=$annee; $i < (intval($annee) + intval($hypotheses->b15)) ; $i++) { 
                $Valeur_tranches[] = $i;
                $Valeur_tranches1[] = 0;
            }
            foreach (self::$Annee_tranches as $Annee_tranche ) {
                for ($i=0; $i < intval($hypotheses->b15) ; $i++) { 
                    if ($Annee_tranche == intval($Valeur_tranches[$i])) {
                        $Valeur_tranches1[$i] = $Valeur_tranches1[$i] + self::$Montant_tranches[$cpteur];
                    }
                }
                $cpteur++;
            }
            /* fin correspondance */
             
            /*Dépenses d'investissement*/
            $depenses_investissements = array();
            $depenses_investissements [0] = 0;

            for ($i=0; $i < intval($hypotheses->b15); $i++) 
            {
                $result['business_plan']['resultat']['Remplacement_des_vehicules_amortis'][]=[
                    'date' => intval($Valeur_tranches[$i]),
                    'montant' => empty(self::$Remplacement_des_vehicules_amortis) ? 0 : self::$Remplacement_des_vehicules_amortis[$i]
                ];
                $depenses_investissements [$i] = empty(self::$Remplacement_des_vehicules_amortis) ? 0 : self::$Remplacement_des_vehicules_amortis[$i];
            }
            for ($i=0; $i < intval($hypotheses->b15); $i++) 
            {
                $result['business_plan']['resultat']['Renouvellement_des_ordinateurs_amortis'][]=[
                    'date' => intval($Valeur_tranches[$i]),
                    'montant' => empty(self::$Renouvellement_des_ordinateurs_amortis) ? 0 : self::$Renouvellement_des_ordinateurs_amortis[$i]
                ];
                $depenses_investissements [$i] += empty(self::$Renouvellement_des_ordinateurs_amortis) ? 0 : self::$Renouvellement_des_ordinateurs_amortis[$i];
            }
            for ($i=0; $i < intval($hypotheses->b15); $i++) 
            {
                $result['business_plan']['resultat']['Renouvellement_des_telephones_fixes_amortis'][]=[
                    'date' => intval($Valeur_tranches[$i]),
                    'montant' => empty(self::$Renouvellement_des_telephones_fixes_amortis) ? 0 : self::$Renouvellement_des_telephones_fixes_amortis[$i]
                ];
                $depenses_investissements [$i] += empty(self::$Renouvellement_des_telephones_fixes_amortis) ? 0 : self::$Renouvellement_des_telephones_fixes_amortis[$i];
            }
            for ($i=0; $i < intval($hypotheses->b15); $i++) 
            {
                $result['business_plan']['resultat']['Renouvellement_du_mobilier_amortis'][]=[
                    'date' => intval($Valeur_tranches[$i]),
                    'montant' => empty(self::$Renouvellement_du_mobilier_amortis) ? 0 : self::$Renouvellement_du_mobilier_amortis[$i]
                ];
                $depenses_investissements [$i] += empty(self::$Renouvellement_du_mobilier_amortis) ? 0 : self::$Renouvellement_du_mobilier_amortis[$i];
            }
            for ($i=0; $i < intval($hypotheses->b15); $i++)
            {
                $result['business_plan']['resultat']['Autres_amortis'][]=[
                    'date' => intval($Valeur_tranches[$i]),
                    'montant' => empty(self::$Autres_amortis) ? 0 : self::$Autres_amortis[$i]
                ];
                $depenses_investissements [$i] += empty(self::$Autres_amortis) ? 0 : self::$Autres_amortis[$i];
            }
            for ($i=0; $i < intval($hypotheses->b15); $i++)
            {
                $result['business_plan']['resultat']['Depenses_investissements'][]=[
                    'date' => intval($Valeur_tranches[$i]),
                    'montant' => $depenses_investissements[$i]
                ];
            }
            /*Charges d'exploitation*/ 
            $Charges_exploitation = array();
            $Charges_exploitation[0] = 0;

            for ($i=0; $i < intval($hypotheses->b15); $i++) 
            { 
                $result['business_plan']['resultat']['Achats'][]=[
                        'date' => intval($Valeur_tranches[$i]),
                        'montant' => empty(self::$Achats) ? 0 : self::$Achats[$i]
                ];
                $Charges_exploitation[$i] = empty(self::$Achats) ? 0 : self::$Achats[$i];
            }
            for ($i=0; $i < intval($hypotheses->b15); $i++) 
            {
                $result['business_plan']['resultat']['Transports'][]=[
                    'date' => intval($Valeur_tranches[$i]),
                    'montant' => empty(self::$Transports) ? 0 : self::$Transports[$i]
                ];
                $Charges_exploitation [$i] += empty(self::$Transports) ? 0 : self::$Transports[$i];
            }
            for ($i=0; $i < intval($hypotheses->b15); $i++) 
            {
                $result['business_plan']['resultat']['SEA'][]=[
                    'date' => intval($Valeur_tranches[$i]),
                    'montant' => self::$SEAs[$i]
                ];
                $Charges_exploitation [$i] += empty(self::$SEAs) ? 0 : self::$SEAs[$i];
            }
            for ($i=0; $i < intval($hypotheses->b15); $i++) 
            {
                $result['business_plan']['resultat']['SEB'][]=[
                    'date' => intval($Valeur_tranches[$i]),
                    'montant' => empty(self::$SEBs) ? 0 : self::$SEBs[$i]
                ];
                $Charges_exploitation [$i] += empty(self::$SEBs) ? 0 : self::$SEBs[$i];
            }
            for ($i=0; $i < intval($hypotheses->b15); $i++) 
            {
                $result['business_plan']['resultat']['Charges_de_personnels'][]=[
                    'date' => intval($Valeur_tranches[$i]),
                    'montant' => empty(self::$Charges_de_personnels) ? 0 : self::$Charges_de_personnels[$i]
                ];
                $Charges_exploitation [$i] += empty(self::$Charges_de_personnels) ? 0 : self::$Charges_de_personnels[$i];
            }
            for ($i=0; $i < intval($hypotheses->b15); $i++) 
            {
                $result['business_plan']['resultat']['Autres_charges'][]=[
                    'date' => intval($Valeur_tranches[$i]),
                    'montant' => empty(self::$Autres_charges) ? 0 : self::$Autres_charges[$i]
                ];
                $Charges_exploitation [$i] += empty(self::$Autres_charges) ? 0 : self::$Autres_charges[$i];
            }
            for ($i=0; $i < intval($hypotheses->b15); $i++) 
            {
                $result['business_plan']['resultat']['Charges_exploitation'][]=[
                    'date' => intval($Valeur_tranches[$i]),
                    'montant' => $Charges_exploitation [$i] 
                ];
                
            }

            /*Provisions et dépenses en entretien routier*/
            $Provision_depenses_entretien_routier = array();
            $Provision_depenses_entretien_routier [0]= 0;

            for ($i=0; $i < intval($hypotheses->b15); $i++) 
            {
                $result['business_plan']['resultat']['Entretien_courants'][]=[
                    'date' => intval($Valeur_tranches[$i]),
                    'montant' => empty(self::$Entretien_courants) ? 0 : self::$Entretien_courants[$i]
                ];
                $Provision_depenses_entretien_routier[$i] = empty(self::$Entretien_courants) ? 0 : self::$Entretien_courants[$i];
            }
            for ($i=0; $i < intval($hypotheses->b15); $i++) 
            {
                $result['business_plan']['resultat']['Provisions_pour_entretien_periodique_necessaires'][]=[
                    'date' => intval($Valeur_tranches[$i]),
                    'montant' => empty(self::$Provisions_pour_entretien_periodique_necessaires) ? 0 : self::$Provisions_pour_entretien_periodique_necessaires[$i]
                ];
                $Provision_depenses_entretien_routier[$i] += empty(self::$Provisions_pour_entretien_periodique_necessaires) ? 0 : self::$Provisions_pour_entretien_periodique_necessaires[$i];
            }
            for ($i=0; $i < intval($hypotheses->b15); $i++) 
            {
                $result['business_plan']['resultat']['Provisions_pour_entretien_periodique_part_du_FER'][]=[
                    'date' => intval($Valeur_tranches[$i]),
                    'montant' => empty(self::$Provisions_pour_entretien_periodique_part_du_FERs) ? 0 : self::$Provisions_pour_entretien_periodique_part_du_FERs[$i]
                ];
                $Provision_depenses_entretien_routier[$i] += empty(self::$Provisions_pour_entretien_periodique_part_du_FERs) ? 0 : self::$Provisions_pour_entretien_periodique_part_du_FERs[$i];
            }
            for ($i=0; $i < intval($hypotheses->b15); $i++) 
            {
                $result['business_plan']['resultat']['Provision_depenses_entretien_routier'][]=[
                    'date' => intval($Valeur_tranches[$i]),
                    'montant' => $Provision_depenses_entretien_routier[$i]
                ];
            }

            //Fonds levé

            for ($i=0; $i < intval($hypotheses->b15); $i++) {
                $result['business_plan']['resultat']['Fonds_leve'][]=[
                    'date' => intval($Valeur_tranches[$i]),
                    'montant' => intval($Valeur_tranches1[$i])
                   ];
            }

            //Cash flow = somme (Fond propore + ressources additionnelles + Fonds levé + recette de gar de péage)
            $cash_flows = array();
            for ($i=0; $i <  intval($hypotheses->b15); $i++) { 
                $cash_flows[$i] = $depenses_total[$i] + self::$Recettes_des_gare_de_peages[$i] + self::$Ressources_additionnelles[$i] + $Valeur_tranches1[$i];
            }
            $cpteur = 0;
            foreach ($cash_flows as $cash_flow) {
                $result['business_plan']['resultat']['cash_flow'][]=[
                    'date' => intval($Valeur_tranches[$cpteur]),
                    'montant' => $cash_flow
                ];
                $cpteur++;
            }

            //Cach flow actualisé = cash_flow(n) / (1 + Taux d'actualisation des flux de trésorerie )^(nbre d'année)
            $cash_flow_actualises = array();
            $cpt_annee = 1;
            for ($i=0; $i <  intval($hypotheses->b15); $i++) { 
                $cash_flow_actualises[$i] = ($cash_flows[$i] / (1 + pow($hypotheses->b11, $cpt_annee)));
                //Log::info('cashFlow',['cash flow'=> $cash_flow_actualises[$i].' '.$cpt_date]);
                $cpt_annee++;
            }
            $cpteur = 0;
            foreach ($cash_flow_actualises as $cash_flow_actualise) {
                $result['business_plan']['resultat']['cash_flow_actualise'][]=[
                    'date' => intval($Valeur_tranches[$cpteur]),
                    'montant' => $cash_flow_actualise
                ];
                $cpteur++;
            }
            
            //Calcule de la VAN
            $VAN = 0;
            for ($i=0; $i < intval($hypotheses->b15); $i++) { 
                $VAN += $cash_flow_actualises[$i];
            }
            $result['business_plan']['resultat']['VAN']=[
                'VAN' => $VAN
            ];

            //Calcule de la TRI
        
            //$TRI = self::tri($hypotheses->b16, $cash_flow_actualises);
            $IRR = self::IRR($hypotheses->b16, $cash_flow_actualises);
            $result['business_plan']['resultat']['TRI']=[
                'TRI' =>  $IRR

            ];

            //Date debut projection
            $result['business_plan']['resultat']['entete']=[
                'Date_debut_projection' =>  $hypotheses->h8,
                'Nombre_annee_de_projection' => $hypotheses->b15,
                'Total_investment'=>'',
                'Fonds_propres' => intval($hypotheses->b16),
                'Dettes' => $hypotheses->h2,
                'Service_de_la_dette'=> self::$Service_de_la_dette,
                'Commission_sur_LF' => $hypotheses->h9,
                'Taux_actualisation_des_flux_tresorerie' => $hypotheses->b11
            ];

            return $result;
        }catch(\Exception $e){
            Log::error('BusinessPlan::generer a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }

    //save file
    public static function enregistrer(Request $request){
        try{
            $result = [
                'code_http' => 201,
                'code_message' => 201
            ];

            $inputs = $request->all();

            if(!is_array($inputs)){
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_VALIDATION';
                $result['erreurs'] = 'Corps de la requête vide.';
                return $result;
            }

            //validate inputs
            $rules = [
                'fichier' => 'required|mimes:doc,csv,xlsx,xls,docx,jpeg,png,pdf,txt'
            ];

            $validator = Validator::make($inputs, $rules);
            if(!$validator->passes()){
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_VALIDATION';
                $result['erreurs'] = $validator->errors()->all();
                return $result;
            }

            $chemin_destination = base_path() . "/uploads";
            $fichier = 'f_' . $request->user()->id . '_' . date('YmdHis') . '.' . $inputs['fichier']->getClientOriginalExtension();
            if($inputs['fichier']->move($chemin_destination, $fichier)){
                $result['url'] = '/uploads/' . $fichier;
            }else{
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_UPLOAD';
                $result['erreurs'] = 'Impossible d\'enregistrer le fichier.';
            }

            
            return $result;
        }catch(\Exception $e){
            Log::error('Upload::enregistrer a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }

     /**
     * Table des emprunts
     * @param $investment
     * @param int $rate ou pourcentage
     * @param int $n ou nombre de période par année
     * @param int $t ou nombre de calcul par année
     * @return mixed
     */

    private static function TE_calculInteretCompose($investment, $rate, $n, $t)
    {
        //A = P(1 + r/n) ^ nt 
        // TE_calculInteretCompose($investment, $year, $rate, $n)
        // Log::info('Valeur_Interet',['Interet_1', self::TE_calculInteretCompose(8000000000, 1 , 6.5 , 1)]);
       
        $accumulated = $investment * pow(1 + $rate/(100 * $n),($n * $t));
        return $accumulated;
    }
    /**
     * TE table des emprunts
     * @param int $mont_init_emp (montant de l'emprunt)
     * @param int $taux (pourcentage du taux d'intérêt)
     * @param int $nb_periode 
     * @param int $n ou nombre de calcul par année
     * @param int $tob
     * @param string $date debut
     * @param string $tranche 
     * @param string $periode_capitalisation 
     * @return mixed
     */
    private static function table_emprunt($mont_init_emp, $taux,$n, $t, $nb_periode, $tob, $date_debut, $commission,$periode_capitalisation,$tranche)
    {

        $mois = Carbon::createFromFormat('Y-m-d', $date_debut)->format('m');
        $annee = Carbon::createFromFormat('Y-m-d', $date_debut)->format('Y');
        $valeurs = array();
        $rest_mois = 0;
        self::$Annee_tranches[] = $annee;
        self::$Montant_tranches[] = $mont_init_emp;
        
        //Au cas ou l'année de debut n'est pas entière
        if ($n == 1 && $t == 1) {
            if ($mois > 01) {
                $rest_mois = 12 - $mois;
                //Log::info('Annee non entiere',['mois',$rest_mois]);
                for ($i=0; $i < $nb_periode; $i++) {
                    if ($i == 0) {
                        $valeurs[] = self::TE_calculInteretCompose($mont_init_emp , $taux , $n, ($rest_mois+1)/12);//taux plus capital dans un tableau
                        //Log::info('testValeur',['interet' =>(($rest_mois+1)/12), 'valeur'=>self::TE_calculInteretCompose($mont_init_emp , $taux , $n, ($rest_mois+1)/12)]);
                    } else {
                        $valeurs[] = self::TE_calculInteretCompose($mont_init_emp , $taux , $n, (($i)/$t)+ (($rest_mois+1)/12));//taux plus capital dans un tableau
                        //Log::info('testValeur',['interet' =>(($i)/$t)+ (($rest_mois+1)/12)]);
                    }            
                } 
            }
            //Au cas ou l'année de debut est entière
            elseif ($mois == 01) {
            
                for ($i=0; $i < $nb_periode; $i++) {
                    if ($i == 0) {
                        $valeurs[] = self::TE_calculInteretCompose($mont_init_emp , $taux , $n, 1/$t);//taux plus capital dans un tableau
                        //Log::info('testValeur',['interetkk' => (1/$t) ]);
                    } else {
                        $valeurs[] = self::TE_calculInteretCompose($mont_init_emp , $taux , $n, ($i+1)/$t);//taux plus capital dans un tableau
                        //Log::info('testValeur',['interet' =>($i/$t)]);
                    }            
                }
            }
        }
        //En ca de semestrialité, trimestrialité et mensualité
        else {
            for ($i=0; $i < $nb_periode; $i++) {
                if ($i == 0) {
                    $valeurs[] = self::TE_calculInteretCompose($mont_init_emp , $taux , $n, 1/$t);//taux plus capital dans un tableau
                    //Log::info('testValeur',['interetkk' => (1/$t) ]);
                } else {
                    $valeurs[] = self::TE_calculInteretCompose($mont_init_emp , $taux , $n, ($i+1)/$t);//taux plus capital dans un tableau
                    //Log::info('testValeur',['interet' =>($i/$t)]);
                }            
            }
        }
        $som_taux = 0;//variable pour faire la somme des taux des périodes précédentes
        $valeur_som_taux = array();//tableau pour recencer la somme des taux des périodes précédentes
        $mensualite = 0;
        $som_mensualite = 0;
        foreach ($valeurs as $valeur) {
            $som_mensualite = $som_mensualite + ($mont_init_emp / $nb_periode);
            $som_taux = $som_taux + end($valeur_som_taux);
            $valeur_som_taux []= $valeur - $mont_init_emp - $som_taux;
            $result[$tranche][] = [
                'nro' => $mensualite += 1 ,
                'taux_interet' => end($valeur_som_taux),
                'TOB' => (end($valeur_som_taux) * $tob),
                 $periode_capitalisation => round(($mont_init_emp / $nb_periode)),
                'service' => ($mont_init_emp / $nb_periode) + (end($valeur_som_taux) * $tob),
                'reste_a_payer' => round($mont_init_emp - round($som_mensualite))
            ];
        }
        // Faire la somme des montants par année
        $som_taux_par_an = array();
        if ($n == 1 && $t == 1) {
            $annee = intval($annee);
            $som_taux = 0;//variable pour faire la somme des taux des périodes précédentes
            $valeur_som_taux = array();//tableau pour recencer la somme des taux des périodes précédentes
            $compteur_annee = 0;
            $periode = 0;
            foreach ($valeurs as $valeur) {
                $som_taux = $som_taux + end($valeur_som_taux);
                $valeur_som_taux []= $valeur - $mont_init_emp - $som_taux;
                $result['resume_annuel'][] = [
                    'nro' => $periode += 1 ,
                    'annee' => $annee + $compteur_annee,
                    'taux_interet' => end($valeur_som_taux),
                    'services' => ($mont_init_emp / $nb_periode) + (end($valeur_som_taux) * $tob),
                    'commission' => $commission
                ];
                self::$Service_de_la_dette += ($mont_init_emp / $nb_periode) + (end($valeur_som_taux) * $tob);//somme des service de la dette
                $compteur_annee += 1;
            }

        }elseif ($n == 2 && $t == 2) {
            Log::info('type intérêt',['intérêt','semestrialité']);

            $annee = intval($annee);
            $som_taux = 0;//variable pour faire la somme des taux des périodes précédentes
            $valeur_som_taux = array();//tableau pour recencer la somme des taux des périodes précédentes
            $compteur_annee = 0;
            $periode = 0;
            $x = 1;
            $y = $nb_periode;
            $sem_mont = 0;
            $init = 13 - $mois;
            $div_init = ($init/6);
            $rest = $nb_periode-$div_init;
            //Log::info('type intérêt',['nb_annee'=>ceil($nb_periode/2), 'init'=> $init,'arrondir'=> floor(4.6),'rest'=>$rest]);
            foreach ($valeurs as $valeur) {     
                if (($init % 6) != 0) {
                    $div_init = floor($init/6);
                }
                if ($div_init >= 0) {
                    $som_taux = $som_taux + end($valeur_som_taux);
                    $valeur_som_taux []= $valeur - $mont_init_emp - $som_taux;
                    $sem_mont = end($valeur_som_taux) + $sem_mont;
                    $div_init = round($div_init - 1);
                    //Log::info('type intérêt',['intérêt'=>$div_init]);
                }
                if ($div_init == 0) {
                    $result['resume_annuel'][] = [
                        'nro' => $periode += 1 ,
                        'annee' => $annee + $compteur_annee,
                        'taux_interet' => $sem_mont,
                        'services' => ($mont_init_emp / $nb_periode) + (end($valeur_som_taux) * $tob),
                        'commission' => $commission
                    ];
                    self::$Service_de_la_dette += ($mont_init_emp / $nb_periode) + (end($valeur_som_taux) * $tob);//somme des service de la dette
                    $compteur_annee += 1;
                    $sem_mont = 0;
                    if ($rest > 2) {
                        $div_init = 2;
                        $rest = $rest - 2;
                    }elseif ($rest <= 2) {
                        $div_init = $rest;
                    } 
                } 
            }
             

        }elseif ($n == 4 && $t == 4) {
            $annee = intval($annee);
            $som_taux = 0;//variable pour faire la somme des taux des périodes précédentes
            $valeur_som_taux = array();//tableau pour recencer la somme des taux des périodes précédentes
            $compteur_annee = 0;
            $periode = 0;
            $x = 1;
            $y = $nb_periode;
            $sem_mont = 0;
            $init = 13 - $mois;
            $div_init = ($init/3);
            $rest = $nb_periode-$div_init;
            //Log::info('type intérêt',['nb_annee'=>ceil($nb_periode/4), 'init'=> $init,'div_init'=> ($init/3),'rest'=>$rest]);
            foreach ($valeurs as $valeur) {
                if ($div_init >= 0) {
                    $som_taux = $som_taux + end($valeur_som_taux);
                    $valeur_som_taux []= $valeur - $mont_init_emp - $som_taux;
                    $sem_mont = end($valeur_som_taux) + $sem_mont;
                    $div_init = $div_init - 1;
                   //Log::info('type intérêt',['intérêt'=>$div_init]);
                }
                if ($div_init == 0) {
                    $result['resume_annuel'][] = [
                        'nro' => $periode += 1 ,
                        'annee' => $annee + $compteur_annee,
                        'taux_interet' => $sem_mont,
                        'services' => ($mont_init_emp / $nb_periode) + (end($valeur_som_taux) * $tob),
                        'commission' => $commission
                    ];
                    self::$Service_de_la_dette += ($mont_init_emp / $nb_periode) + (end($valeur_som_taux) * $tob);//somme des service de la dette
                    $compteur_annee += 1;
                    $sem_mont = 0;
                    if ($rest > 4) {
                        $div_init = 4;
                        $rest = $rest - 4;
                    }elseif ($rest <= 4) {
                        $div_init = $rest;
                    }
                } 
            }
        }elseif ($n == 12 && $t == 12) {
            $annee = intval($annee);
            $som_taux = 0;//variable pour faire la somme des taux des périodes précédentes
            $valeur_som_taux = array();//tableau pour recencer la somme des taux des périodes précédentes
            $compteur_annee = 0;
            $periode = 0;
            $x = 1;
            $y = $nb_periode;
            $sem_mont = 0;
            $init = 13 - $mois;
            $rest = $nb_periode-$init;
            //Log::info('type intérêt',['nb_annee'=>ceil($nb_periode/12), 'init'=> $init,'rest'=>$rest]);

            foreach ($valeurs as $valeur) {
                if ($init > $nb_periode) {
                    $init = $nb_periode;
                }
                if ($init >= 0) {
                    $som_taux = $som_taux + end($valeur_som_taux);
                    $valeur_som_taux []= $valeur - $mont_init_emp - $som_taux;
                    $sem_mont = end($valeur_som_taux) + $sem_mont;
                    $init = $init - 1;
                    //Log::info('type intérêt',['intérêt'=>$init]);
                } 
                if ($init == 0) {
                    $result['resume_annuel'][] = [
                        'nro' => $periode += 1 ,
                        'annee' => $annee + $compteur_annee,
                        'taux_interet' => $sem_mont,
                        'services' => ($mont_init_emp / $nb_periode) + (end($valeur_som_taux) * $tob),
                        'commission' => $commission
                    ];
                    self::$Service_de_la_dette += ($mont_init_emp / $nb_periode) + (end($valeur_som_taux) * $tob);//somme des service de la dette
                    $compteur_annee += 1;
                    $sem_mont = 0;
                    if ($rest > 12) {
                        $init = 12;
                        $rest = $rest - 12;
                    }elseif ($rest <= 12) {
                        $init = $rest;
                    }
                } 
            }
        }
        
        return $result;
    }
    private static function projection_tarifs($annee_debut, $annee_fin, $captation_peage, $classe_1, $classe_2, $classe_3, $classe_4, $nombre_annees, $date_debut, $croissance_annuel_classe_1, $croissance_annuel_classe_2,$croissance_annuel_classe_3,$croissance_annuel_classe_4)
    {       
            $var_projection_debut_class_1 = array();
            $var_projection_debut_class_2 = array();
            $var_projection_debut_class_3 = array();
            $var_projection_debut_class_4 = array();

            $var_projection_fin_class_1 = array();
            $var_projection_fin_class_2 = array();
            $var_projection_fin_class_3 = array();
            $var_projection_fin_class_4 = array();

            $var_projection_class_1 = array();
            $var_projection_class_2 = array();
            $var_projection_class_3 = array();
            $var_projection_class_4 = array();

            $var_tarif_moyen = array();
            
            $projection_tarif_debuts = DB::table('bp_projection_tarifs')
                ->where('annee', $annee_debut)
                ->orderBy('id', 'asc')
                ->get();

            $projection_tarif_fins = DB::table('bp_projection_tarifs')
                ->where('annee', $annee_fin)
                ->orderBy('id', 'asc')
                ->get();

            $cpt = 0;
            foreach ($projection_tarif_fins as $projection_tarif_fin) {
                $cpt++;
                $var_projection_fin_class_1[] = $projection_tarif_fin->classe_1;
                $var_projection_fin_class_2[] = $projection_tarif_fin->classe_2;
                $var_projection_fin_class_3[] = $projection_tarif_fin->classe_3;
                $var_projection_fin_class_4[] = $projection_tarif_fin->classe_4;
                //Log::info('projection_fin',['verif'=> $projection_tarif_fin->classe_1]);
            }

            foreach ($projection_tarif_debuts as $projection_tarif_debut) {
                $var_projection_debut_class_1[] = $projection_tarif_debut->classe_1;
                $var_projection_debut_class_2[] = $projection_tarif_debut->classe_2;
                $var_projection_debut_class_3[] = $projection_tarif_debut->classe_3;
                $var_projection_debut_class_4[] = $projection_tarif_debut->classe_4;
                //Log::info('projection_debut',['verif'=> $projection_tarif_debut->classe_1]);
            }

            $tarif_moyen = 0;
            for ($i=0; $i < $cpt; $i++) { 
                
                $var_tarif_moyen[$i] = $tarif_moyen += 100;
                $var_projection_class_1[$i] = (($var_projection_fin_class_1[$i] + $var_projection_debut_class_1[$i])/2) * (1 + $captation_peage);
                $var_projection_class_2[$i] = (($var_projection_fin_class_2[$i] + $var_projection_debut_class_2[$i])/2) * (1 + $captation_peage);
                $var_projection_class_3[$i] = (($var_projection_fin_class_3[$i] + $var_projection_debut_class_3[$i])/2) * (1 + $captation_peage);
                $var_projection_class_4[$i] = (($var_projection_fin_class_4[$i] + $var_projection_debut_class_4[$i])/2) * (1 + $captation_peage);
            }

            
            for ($i=0; $i < $cpt; $i++) { 
                $result['trafic_moyen'][] = [
                    'tarif_classe_1' => $var_tarif_moyen[$i],
                    'classe_1' => $var_projection_class_1[$i],
                    'classe_2' => $var_projection_class_2[$i],
                    'classe_3' => $var_projection_class_3[$i],
                    'classe_4' => $var_projection_class_4[$i]
                ];
            }
            
            $annee = (Carbon::createFromFormat('Y-m-d', $date_debut)->format('Y') - 1);
            //Log::info('verif',['classe1' => $classe_1,'classe2' => $classe_2,'classe3' => $classe_3,'classe4' => $classe_4,'nb_anne' => $nombre_annees,'nb_annee' => $date_debut,'annee' => $annee]);
            $result['Revenu_annel'][] = [
                'classe_1' => $classe_1,
                'classe_2' => $classe_2,
                'classe_3' => $classe_3,
                'classe_4' => $classe_4,
            ];

            $val_class_1 = array_search($classe_1, $var_tarif_moyen);
            $val_class_2 = array_search($classe_2, $var_tarif_moyen);
            $val_class_3 = array_search($classe_3, $var_tarif_moyen);
            $val_class_4 = array_search($classe_4, $var_tarif_moyen);
            
            $dateDebut = Carbon::parse($date_debut);
            $dateFin = Carbon::parse(1+$annee.'-12-31');

            $nombre_jours = $dateDebut->diffInDays($dateFin);

            //Remplir le tableau projection des tarifs

            $variable_classe1 = $classe_1 * (1 + $croissance_annuel_classe_1) * $var_projection_class_1[$val_class_1] * (1 + $nombre_jours);
            $variable_classe2 = $classe_2 * (1 + $croissance_annuel_classe_2) * $var_projection_class_2[$val_class_2] * (1 + $nombre_jours);
            $variable_classe3 = $classe_3 * (1 + $croissance_annuel_classe_3) * $var_projection_class_3[$val_class_3] * (1 + $nombre_jours);
            $variable_classe4 = $classe_4 * (1 + $croissance_annuel_classe_4) * $var_projection_class_4[$val_class_4] * (1 + $nombre_jours);

            $result['Revenu_annel'][] = [
                'annee' =>  $annee +=1,
                'classe_1' => $variable_classe1,
                'classe_2' => $variable_classe2,
                'classe_3' => $variable_classe3,
                'classe_4' => $variable_classe4
            ];

            self::$Recettes_des_gare_de_peages[] = ($variable_classe1 + $variable_classe2 + $variable_classe3 + $variable_classe4);

            for ($i=0; $i < ($nombre_annees - 1); $i++) { 
                $val_annee = $annee +=1;
                $variable1_classe1 = $classe_1 * pow((1 + $croissance_annuel_classe_1),(($val_annee) - ($annee + 1 ))) * $var_projection_class_1[$val_class_1] * (1 + $nombre_jours);
                $variable1_classe2 = $classe_2 * pow((1 + $croissance_annuel_classe_2),(($val_annee) - ($annee + 1 ))) * $var_projection_class_2[$val_class_2] * (1 + $nombre_jours);           
                $variable1_classe3 = $classe_3 * pow((1 + $croissance_annuel_classe_3),(($val_annee) - ($annee + 1 ))) * $var_projection_class_3[$val_class_3] * (1 + $nombre_jours);           
                $variable1_classe4 = $classe_4 * pow((1 + $croissance_annuel_classe_4),(($val_annee) - ($annee + 1 ))) * $var_projection_class_4[$val_class_4] * (1 + $nombre_jours);
            
                $result['Revenu_annel'][] = [
                    'annee'    => $val_annee ,
                    'classe_1' => $variable1_classe1,
                    'classe_2' => $variable1_classe2,
                    'classe_3' => $variable1_classe3,
                    'classe_4' => $variable1_classe4
                    ];

                self::$Recettes_des_gare_de_peages[] = ($variable1_classe1 + $variable1_classe2 + $variable1_classe3 + $variable1_classe4);

                }
            
        return $result;
    }
    private static function investissement($investissements_type_3, $date_debut, $nombre_annees, $part_charge, $nb_mois_salaire, $croissance_annuel, $investissements_type_4, $investissements_type_5, $taux_inflation_annuel,$investissements_type_6)
    {
        $mois = Carbon::createFromFormat('Y-m-d', $date_debut)->format('m');
        $annee = Carbon::createFromFormat('Y-m-d', $date_debut)->format('Y');

        //Log::info('testarrondissemen',['arrondi' =>  ceil( 21648643/ 1000000) * 1000000]);

        $libs_date = array();

        for ($i=0; $i < intval($nombre_annees) ; $i++) { 
            $libs_date[] = intval($annee + $i);
        }

        //Investissement 3
        $donnees_types_3 = array();
        $vale=array();
        $cpt = 0;//compteur
        //self::$Charges_de_personnels[0] = 0;
        foreach ($investissements_type_3 as  $investissement_type3) {
            $donnees_types_3 ['parametres'][] = [
                'id' => $cpt,
                'categorie_3' => $investissement_type3->a,
                'categorie_3_2' => $investissement_type3->b,
                'fonction' => $investissement_type3->c,
                'net_mensuel' => $investissement_type3->d,
                'carburant' => $investissement_type3->e,
                'telephone' => $investissement_type3->f,
                'nb_agent' => $investissement_type3->g,
            ];
            $cptt = 0;//compteur
            foreach ($libs_date as $lib_date) {
                if (($investissement_type3->d <> 0) && ($investissement_type3->f <> 0 ) && ($investissement_type3->g <> 0 )) {
                    $donnees_types_3 ['parametres'][$cpt]['periodes'][] = [
                        "date"   => $lib_date,
                        "montant" => ($cptt == 0) ? $investissement_type3->d * (1 + $part_charge) * $nb_mois_salaire * $investissement_type3->g * pow((1 + $croissance_annuel), (($libs_date[$cptt]) - ($libs_date[0]))) * (((12-$mois)+1)/12) + $investissement_type3->f * 2 * $investissement_type3->g * pow((1 + $croissance_annuel), (($libs_date[$cptt]) - ($libs_date[0]))) : ($investissement_type3->d * (1 + $part_charge) * $nb_mois_salaire + ($investissement_type3->f * 12)) * $investissement_type3->g * pow((1 + $croissance_annuel), (($libs_date[$cptt]) - ($libs_date[0])))
                    ];
                    //self::$Charges_de_personnels[$cptt] += ($cptt == 0) ? $investissement_type3->d * (1 + $part_charge) * $nb_mois_salaire * $investissement_type3->g * pow((1 + $croissance_annuel), (($libs_date[$cptt]) - ($libs_date[0]))) * (((12-$mois)+1)/12) + $investissement_type3->f * 2 * $investissement_type3->g * pow((1 + $croissance_annuel), (($libs_date[$cptt]) - ($libs_date[0]))) : ($investissement_type3->d * (1 + $part_charge) * $nb_mois_salaire + ($investissement_type3->f * 12)) * $investissement_type3->g * pow((1 + $croissance_annuel), (($libs_date[$cptt]) - ($libs_date[0])));
                    $vale[] = ($cptt == 0) ? $investissement_type3->d * (1 + $part_charge) * $nb_mois_salaire * $investissement_type3->g * pow((1 + $croissance_annuel), (($libs_date[$cptt]) - ($libs_date[0]))) * (((12-$mois)+1)/12) + $investissement_type3->f * 2 * $investissement_type3->g * pow((1 + $croissance_annuel), (($libs_date[$cptt]) - ($libs_date[0]))) : ($investissement_type3->d * (1 + $part_charge) * $nb_mois_salaire + ($investissement_type3->f * 12)) * $investissement_type3->g * pow((1 + $croissance_annuel), (($libs_date[$cptt]) - ($libs_date[0])));
                }elseif (($investissement_type3->d == 0)  && ($investissement_type3->f == 0 ) && ($investissement_type3->g == 0 )) {
                    $donnees_types_3 ['parametres'][$cpt]['periodes'][] = [
                        "date"   => $lib_date,
                        "montant" => 0
                    ];
                }
                $cptt++ ;
            }
            self::$Charges_de_personnels += $vale;
            $cpt++ ;
        }
        $result['investissements_type_3'] = $donnees_types_3['parametres'];
       
        //Investissement 4
        $donnees_types_4 = array();

        $cpt1 = 0;
       
        foreach ($investissements_type_4 as  $investissement_type4) {
            $donnees_types_4 ['parametres'][] = [
                'id' => $cpt1,
                'categorie_4' => $investissement_type4->a,
                'remplacement_de' => $investissement_type4->b,
                'description_4' => $investissement_type4->c,
                'cout_unitaire' => $investissement_type4->d,
                'quantite' => $investissement_type4->f,
                'duree' => $investissement_type4->g,
                'cout_total_duree_amortissement' => $investissement_type4->d * $investissement_type4->f
            ];

            //ceil( 21648643/ 1000000) * 1000000 formule pour arrondir au 100000 près
            $cpt11 = 0;
            $v = 0;
            $vall =array();
            foreach ($libs_date as $lib_date) {
                //if ($investissement_type4->g <> 0) {
                    $donnees_types_4 ['parametres'][$cpt1]['periodes'][] = [
                        "date"   => $lib_date,
                        "montant" => $investissement_type4->g == 0 ? 0 : (($cpt11 % intval($investissement_type4->g) == 0 && $cpt11 != 0) ? ceil( $investissement_type4->d * $investissement_type4->f * pow((1+ $taux_inflation_annuel),(($libs_date[$cpt11]) - ($libs_date[0]))) / 1000000) * 1000000 : 0)
                    ];     
                    $vall[] = $investissement_type4->g == 0 ? 0 : (($cpt11 % intval($investissement_type4->g) == 0 && $cpt11 != 0) ? ceil( $investissement_type4->d * $investissement_type4->f * pow((1+ $taux_inflation_annuel),(($libs_date[$cpt11]) - ($libs_date[0]))) / 1000000) * 1000000 : 0);
                $cpt11++ ;
            } 
            //calcul Dépenses d'investissement
            //non tout le monde
        
            if ( $investissement_type4->b === self::REMPLACEMENT_DES_VEHICULES_AMORTIS ) {

                if (empty( self::$Remplacement_des_vehicules_amortis)) {
                     //Log::info('info',['remplacement'=> 'empty']);
                     foreach ($vall as $value) {
                        self::$Remplacement_des_vehicules_amortis[] = $value;
                        Log::info('info',['REMPLACEMENT_DES_VEHICULES_AMORTIS'=> 'empty', 'REMPLACEMENT_DES_VEHICULES_AMORTIS'=> $value]);
                     }
                }else {
                    //Log::info('info',['remplacement'=> 'pas empty']);
                    $x=0;
                    foreach ($vall as $value) {
                         self::$Remplacement_des_vehicules_amortis[$x] += $value ;
                         Log::info('info',['REMPLACEMENT_DES_VEHICULES_AMORTIS'=> 'non-empty', 'REMPLACEMENT_DES_VEHICULES_AMORTIS'=> $value]);
                         $x++;
                    }

                }
               
            }

            if ( $investissement_type4->b === self::RENOUVELLEMENT_DES_ORDINATEURS_AMORTIS ) {
             
                if (empty( self::$Renouvellement_des_ordinateurs_amortis)) {
                    //Log::info('info',['remplacement'=> 'empty']);
                    foreach ($vall as $value) {
                       self::$Renouvellement_des_ordinateurs_amortis[] = $value;
                    }
               }else {
                   //Log::info('info',['remplacement'=> 'pas empty']);
                   $x=0;
                   foreach ($vall as $value) {
                        self::$Renouvellement_des_ordinateurs_amortis[$x] += $value ;
                        $x++;
                   }

               }            
            }
            
            if ( $investissement_type4->b === self::RENOUVELLEMENT_DES_TELEPHONES_FIXES_AMORTIS) {
            
                if (empty( self::$Renouvellement_des_telephones_fixes_amortis)) {
                    //Log::info('info',['remplacement'=> 'empty']);
                    foreach ($vall as $value) {
                       self::$Renouvellement_des_telephones_fixes_amortis[] = $value;
                    }
               }else {
                   //Log::info('info',['remplacement'=> 'pas empty']);
                   $x=0;
                   foreach ($vall as $value) {
                        self::$Renouvellement_des_telephones_fixes_amortis[$x] += $value ;
                        $x++;
                   }

               }
            }

            if ( $investissement_type4->b === self::RENOUVELLEMENT_DU_MOBILIER_AMORTI ) {
                
                if (empty( self::$Renouvellement_du_mobilier_amortis)) {
                    //Log::info('info',['remplacement'=> 'empty']);
                    foreach ($vall as $value) {
                       self::$Renouvellement_du_mobilier_amortis[] = $value;
                    }
               }else {
                   //Log::info('info',['remplacement'=> 'pas empty']);
                   $x=0;
                   foreach ($vall as $value) {
                        self::$Renouvellement_du_mobilier_amortis[$x] += $value ;
                        $x++;
                   }

               }
            }
           
            if ( $investissement_type4->b === self::AURES_AMORTI) {
               
                if (empty( self::$Autres_amortis)) {
                    //Log::info('info',['remplacement'=> 'empty']);
                    foreach ($vall as $value) {
                       self::$Autres_amortis[] = $value;
                    }
               }else {
                   //Log::info('info',['remplacement'=> 'pas empty']);
                   $x=0;
                   foreach ($vall as $value) {
                        self::$Autres_amortis[$x] += $value ;
                        $x++;
                   }

               }                
            }
            $cpt1++ ;
        }
        $result['investissements_type_4'] = $donnees_types_4['parametres'];

        //Investissement 5
        $donnees_types_5 = array();
        $cpt2 = 0;//compteur
        foreach ($investissements_type_5 as  $investissement_type5) {
            $donnees_types_5 ['parametres'][] = [
                'id' => $cpt2,
                'categorie_5' => $investissement_type5->a,
                'categorie_5_2' => $investissement_type5->b,
                'charges_pour_une_année_pleine' => $investissement_type5->c,
                'montant' => $investissement_type5->d,
            ];

            $cpt22 = 0;
            $val=array();
            foreach ($libs_date as $lib_date) {
                // if ($investissement_type5->d <> 0) {
                    $donnees_types_5 ['parametres'][$cpt2]['periodes'][] = [
                        "date"   => $lib_date,
                        "montant" => $investissement_type5->d == 0 ? 0 : (($cpt22 == 0) ? $investissement_type5->d * (13-$mois)/12 : $investissement_type5->d * pow((1 + $taux_inflation_annuel),(($libs_date[$cpt22]) - ($libs_date[0])))) 
                    ];
                    $val[]= $investissement_type5->d == 0 ? 0 : (($cpt22 == 0) ? $investissement_type5->d * (13-$mois)/12 : $investissement_type5->d * pow((1 + $taux_inflation_annuel),(($libs_date[$cpt22]) - ($libs_date[0])))) ;
           
                $cpt22 ++;
            }
            if ($investissement_type5->c === self::ACHAT) {
                foreach ($val as $value) {
                    self::$Achats[] = $value;
                }
            }
            if ($investissement_type5->c === self::SEA) {
                foreach ($val as $value) {
                    self::$SEAs[] = $value;
                }
            }
            if ($investissement_type5->c === self::SEB) {
                foreach ($val as $value) {
                    self::$SEBs[] = $value;
                }
            }
            if ($investissement_type5->c === self::TRANSPORT) {
                foreach ($val as $value) {
                    self::$Transports[] = $value;
                }
            }
            if ($investissement_type5->c === self::AUTRES_CHARGES) {
                foreach ($val as $value) {
                    self::$Autres_charges[] = $value;
                }
            }
            
            if ($investissement_type5->b === self::ENTRETIEN_COURANT) {
                foreach ($val as $value) {
                    self::$Entretien_courants[] = $value;
                }
            }
            if ($investissement_type5->b === self::PROVISIONS_POUR_ENTRETIEN_PERIODIQUE_NECESSAIRE) {
                foreach ($val as $value) {
                    self::$Provisions_pour_entretien_periodique_necessaires[] = $value;
                }
            }
            if ($investissement_type5->b === self::PROVISIONS_POUR_ENTRETIEN_PERIODIQUE_PART_DU_FER) {
                foreach ($val as $value) {
                    self::$Provisions_pour_entretien_periodique_part_du_FERs[] = $value;
                }
            }

            $cpt2 ++; 
        }
        $result['investissements_type_5'] = $donnees_types_5['parametres'];
              
        //Investissement 6
        $donnees_types_6 = array();
        $cpt3 = 0;
        foreach ($investissements_type_6 as  $investissement_type6) {
            $donnees_types_6 ['parametres'][] = [
                'categorie_6' => $investissement_type6->a,
                'description' => $investissement_type6->c,
                'montant' => $investissement_type6->d,
                'annee' => $investissement_type6->e,
            ];
            $cpt33 = 0;
            foreach ($libs_date as $lib_date) {
                    if ($investissement_type6->e <> 0) {
                        $donnees_types_6 ['parametres'][$cpt3]['periodes'][] = [
                            "date"   => $lib_date,
                            "montant" => ($lib_date == $investissement_type6->e) ? $investissement_type6->d : 0
                        ];
                        if ( $investissement_type6->a === self::RESSOURCES_ADDITIONNELLES ) {
                            self::$Ressources_additionnelles[] = ($lib_date == $investissement_type6->e) ? intval($investissement_type6->d) : 0;
                        }else {
                            self::$Ressources_additionnelles[] = 0;
                        }
                    }elseif ($investissement_type6->e == 0) {
                        $donnees_types_6 ['parametres'][$cpt3]['periodes'][] = [
                            "date"   => $lib_date,
                            "montant" => 0
                        ];
                        self::$Ressources_additionnelles[] = 0;
                    }
                
                $cpt33 ++;
            }
            $cpt3 ++;
        }
        $result['investissements_type_6'] = $donnees_types_6['parametres'];
        return $result;
    }
    private static function tri ($fond_propre, $cash_flow) {

        for ($n = 0; $n < 100; $n += 0.0001) {
    
            $pv = 0;
            for ($i = 0; $i < count($cash_flow); $i++) {
                $pv = $pv + $cash_flow[$i] / pow(1 + $n, $i + 1);
            }
    
            if ($pv <= $fond_propre) {
                return round($n * 10000) / 100;
            }
    
        }
        return $pv;
    }
    private static function IRR($investment, $flow, $precision = 0.000001) {

        if (array_sum($flow) < $investment):
            return 0;
        endif;
        $maxIterations = 20;
        $i =0;
        if (is_array($flow)):
            $min = 0;
            $max = 1;
            $net_present_value = 1;
            while ((abs($net_present_value - $investment) > $precision)&& ($i < $maxIterations)) {
                $net_present_value = 0;
                $guess = ($min + $max) / 2;
                foreach ($flow as $period => $cashflow) {
                    $net_present_value += $cashflow / (1 + $guess) ** ($period + 1);
                }
                if ($net_present_value - $investment > 0) {
                    $min = $guess;
                } else {
                    $max = $guess;
                }
                $i++;
            }
            return $guess * 100;
        else:
            return 0;
        endif;
    }
}
