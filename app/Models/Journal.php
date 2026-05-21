<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Utility\Imports\ImporterJournaux;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use App\Models\ComptabiliteGlobale;
use App\Models\GroupeComptaAnalytique;
use App\Models\CompteComptaAnalytique;
use App\Models\PlanCompte;
use Carbon\Carbon;

class Journal extends Model
{
    use HasFactory;
    protected $table = 'journals';
    protected $primaryKey = 'id';
    protected $guarded = ['updated_at'];
    public $timestamps = true;
    public $incrementing = true;
    protected $casts = [
        'compte' => 'string',
    ];

    
    //compte resultat
    public static $Produits_exploitations = Array();
    public static $Revenus = Array('701','702','703','704','705','706','707','708','709');
    public static $Autres_produits_exploitations = Array('731','732','733','734','735','738','739','781');//74,79
    public static $Production_immobilisees_2 = Array( '72');
    public static $Total_produits_exploitations = Array();
    public static $Charges_exploitations = Array();
    public static $Variation_des_stocks_2 = Array('71');
    public static $Achat_marchandises = Array( '60');
    public static $Charges_de_personnels = Array('64');
    public static $Dotations_aux_amortissements = Array('681');
    public static $Autres_charges_exploitations = Array('61','62','63','66');


    public static $Total_charges_exploitations = Array();
    public static $Resultat_exploitations = Array();
    public static $Charges_financieres_nettes = Array('67');//,'676'
    public static $Produits_des_placements_2 = Array('77');//Sauf 776
    public static $Produits_des_placements_4 = Array('7866','6866');
    public static $Autres_gains_ordinaires = Array('776');
    public static $Autres_pertes_ordinaires = Array('676');
    public static $Resultat_des_activites_ordinaires_avant_impots = Array();
    public static $Impot_sur_les_benefices = Array( '64');
    public static $Resultat_des_activites_ordinaires_apres_impots = Array();
    public static $Elements_extraordinaires = Array('81','83','85','87','89');
    public static $Resultat_nets = Array('13');
    public static $Effet_des_modifications = Array('128');
    public static $Resultat_apres_modifications = Array();
 
    //Bilan Actif
    public static $immobilisations_incorporelles_3 = array('211','212','213','214','215','216','217','218');//'2191','2198'
    public static $immobilisations_corporelles_3  = array('221','222','223','224','225','226','227','228','229','231','232','233','234','235','237','238','239');//'24'
    public static $immobilisations_financieres_3  = array('25','26','27','485','486','488');//-269
    public static $stocks_2 = array('31','32','33','34','35','36','37','38');
    public static $creances_3 = array('411','413','416','417','418','409','421','426','431','446','452','455','471'); 
    public static $creances_4 = array('4287','4341','4342','4366','4387','4587');
    public static $creances_3_DR =  array('441','422','423','433','442','448','457');//(46 DR)
    public static $creances_4_DR =  array('4349','4368');//
    public static $disponibilites_2 = array('51','52','55','54 ');//(53 DR)  

    //Bilan Passif
    public static $capitaux_propres_3 = array('101','111','112','117','118','141','142','143','144','121','128');//(-109) (145 nets) 
    public static $dettes_financieres_3 = array('161','162','163','164','165','166','167','168');
    public static $dettes_exploitations_2 = array('18'); 
    public static $dettes_immobilisations = array();
    public static $autres_dettes = array(); 



     //lister plan comptable
     public static function bilan(Request $request)
    {
     
         try{
             if (!($request instanceof Request)) {
                 throw new \Exception('Instance de Illuminate\Http\Request attendue en paramètre, ' . (is_object($request) ? get_class($request) : gettype($request)) . ' trouvé.');
             }
 
             $result = [
                 'code_http' => 200,
                 'code_message' => 200,
                 'donnees' => []
             ];

            //$annee = $request->query('annee', null);
            $annee_debut = $request->query('annee_debut', null);
            $annee_fin = $request->query('annee_fin', null);
            $base = $request->query('base', null);
            if(is_null($base)){
                Log::info('Erreur de génération d\'un bilan avec le paramètre base vide.');
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_BASE_VIDE';
                return $result;
            }

            if ($base == 'CONSOLIDEE') {
                //$selection = Journal::orderBy('journals.id','desc');
                $selection = ComptabiliteGlobale::leftJoin('plan_comptes','plan_comptes.id','=','comptabilite_globales.compte')
                                              ->select('comptabilite_globales.debit','comptabilite_globales.credit','comptabilite_globales.date','comptabilite_globales.base','plan_comptes.compte')
                                              ->where('departement','COMPTABILITE');
            }else {
                //$selection = Journal::where('journals.base',$base);
                $selection = ComptabiliteGlobale::leftJoin('plan_comptes','plan_comptes.id','=','comptabilite_globales.compte')
                                              ->select('comptabilite_globales.debit','comptabilite_globales.credit','comptabilite_globales.date','comptabilite_globales.base','plan_comptes.compte')
                                              ->where('comptabilite_globales.base',$base)
                                              ->where('departement','COMPTABILITE');
            }
            // if(!is_null($annee))
            // {
            //     $selection->whereYear('comptabilite_globales.date', $annee)->orderBy('comptabilite_globales.id','desc');
            // }
            if(!is_null($annee_debut) && !is_null($annee_fin)){
                $selection->whereBetween('date', [$annee_debut , $annee_fin]);
            }
            
             $comptabilites_globales = $selection->get();
            
             if(count($comptabilites_globales)){

                $val_immobilisation_incorporelle = 0;
                $val_immobilisation_corporelle = 0;
                $val_immobilisation_financiere = 0;
                $val_stock = 0;
                $val_creance = 0;
                $val_disponibilite = 0;
                $val_capitaux_propre = 0;
                $val_dette_financiere = 0;
                $val_dette_exploitation = 0;

                $verif_immobilisation_incorporelle = array();
                $verif_immobilisation_corporelle = array();
                $verif_immobilisation_financiere = array();
                $verif_stock = array();
                $verif_creance = array();
                $verif_disponibilite = array();
                $verif_capitaux_propre = array();
                $verif_dette_financiere = array();
                $verif_dette_exploitation = array();

                
                foreach($comptabilites_globales as $comptabilite_globale){
                    
                //immobilisation_incorporelle
                foreach (self::$immobilisations_incorporelles_3 as $immobilisation_incorporelle) {
                    //var_dump(substr($journal->compte, 0 , 3) );
                    if( substr($comptabilite_globale->compte, 0 , 3) == substr($immobilisation_incorporelle , 0 , 3)){
                        //var_dump($immobilisation_incorporelle);
                        if (!array_key_exists($immobilisation_incorporelle,$verif_immobilisation_incorporelle)) {
                            $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , $immobilisation_incorporelle) : self::n_calcul_solde($base, $annee_debut, $annee_fin , $immobilisation_incorporelle);
                            $val_immobilisation_incorporelle += (intVal($soldes->credit) - intVal($soldes->debit));
                            $verif_immobilisation_incorporelle[$immobilisation_incorporelle]= 0;
                            //var_dump($soldes->credit.' '.$soldes->debit);
                        }
                    }   
                } 
                //immobilisations_corporelles
                foreach (self::$immobilisations_corporelles_3 as $immobilisations_corporelle) {
                    if( substr($comptabilite_globale->compte, 0 , 3) == substr($immobilisations_corporelle , 0 , 3)){
                        //var_dump($comptabilite_globale->compte);
                        if (!array_key_exists($immobilisations_corporelle, $verif_immobilisation_corporelle)) {
                          $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , $immobilisations_corporelle) :  self::n_calcul_solde($base, $annee_debut, $annee_fin , $immobilisations_corporelle);
                          $val_immobilisation_corporelle += (intVal($soldes->credit) - intVal($soldes->debit));
                          $verif_immobilisation_corporelle[$immobilisations_corporelle] = 0;
                        }
                    } 
                    if( substr($comptabilite_globale->compte, 0 , 2) == '24'){
                        if (!array_key_exists("24", $verif_immobilisation_corporelle)) {
                          $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , '24') :  self::n_calcul_solde($base, $annee_debut, $annee_fin , '24');
                          $val_immobilisation_corporelle += (intVal($soldes->credit) - intVal($soldes->debit));
                          $verif_immobilisation_corporelle["24"] = 0;
                        }
                    }
                } 
                //immobilisations_financieres
                foreach (self::$immobilisations_financieres_3 as $immobilisation_financiere) {
                    if( substr($comptabilite_globale->compte, 0 , 3) == substr($immobilisation_financiere , 0 , 3)){
                        //var_dump($comptabilite_globale->compte);
                        if (!array_key_exists($immobilisation_financiere, $verif_immobilisation_financiere)) {
                          $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin ,  $immobilisation_financiere) :  self::n_calcul_solde($base, $annee_debut, $annee_fin , $immobilisation_financiere);
                          $val_immobilisation_financiere += (intVal($soldes->credit) - intVal($soldes->debit));
                          $verif_immobilisation_financiere[$immobilisation_financiere] = 0;
                        }
                    }   
                    if( substr($comptabilite_globale->compte, 0 , 3) == '269'){
                      if (!array_key_exists('269', $verif_immobilisation_financiere)) {
                        $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , '269') :  self::n_calcul_solde($base, $annee_debut, $annee_fin , $immobilisation_financiere);
                        $val_immobilisation_financiere += (intVal($soldes->credit) - intVal($soldes->debit));
                        $verif_immobilisation_financiere['269'] = 0;
                      }
                        
                    }
                } 
                //stocks
                foreach (self::$stocks_2 as $stock) {
                    //var_dump($comptabilite_globale->compte);
                    if( substr($comptabilite_globale->compte, 0 , 2) == substr($stock , 0 , 2)){
                        //var_dump($comptabilite_globale->compte);
                        if(!array_key_exists($stock, $verif_stock)){
                          $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , $stock) :  self::n_calcul_solde($base, $annee_debut, $annee_fin , $stock);
                          $val_stock += (intVal($soldes->credit) - intVal($soldes->debit));
                          $verif_stock[$stock] = 0;
                        }
                    }   
                } 
                //creances
                foreach (self::$creances_3 as $creance) {
                    //var_dump($comptabilite_globale->compte);
                    if( substr($comptabilite_globale->compte, 0 , 3) == substr($creance , 0 , 3)){
            
                        if(!array_key_exists($creance, $verif_creance)){
                          $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , $creance) :  self::n_calcul_solde($base, $annee_debut, $annee_fin , $creance);
                          $val_creance += (intVal($soldes->credit) - intVal($soldes->debit));
                          $verif_creance[$creance] = 0;
                        }
                    }   
                } 
                foreach (self::$creances_4 as $creance4) {
                    //var_dump($comptabilite_globale->compte);
                    if( substr($comptabilite_globale->compte, 0 , 4) == substr($creance4 , 0 , 4)){
                        if(!array_key_exists($creance4, $verif_creance)){
                          $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , $creance4) :  self::n_calcul_solde($base, $annee_debut, $annee_fin , $creance4);
                          $val_creance += (intVal($soldes->credit) - intVal($soldes->debit));
                          $verif_creance[$creance4] = 0;
                        }
                    }   
                } 
                foreach (self::$creances_3_DR as $creance_3_DR) {
                    //var_dump($comptabilite_globale->compte);
                    if( substr($comptabilite_globale->compte, 0 , 3) == substr($creance_3_DR , 0 , 3)){
                       
                        if(!array_key_exists($creance_3_DR ,$verif_creance)){
                          $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , $creance_3_DR) :  self::n_calcul_solde($base, $annee_debut, $annee_fin , $creance_3_DR);
                          $val_creance += (intVal($soldes->credit) - intVal($soldes->debit));
                          $verif_creance[$creance_3_DR] = 0;
                        }
                    } 
                    if( substr($comptabilite_globale->compte, 0 , 2) == '46'){
                        if(!array_key_exists('46',$verif_creance)){
                          $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , '46') :  self::n_calcul_solde($base, $annee_debut, $annee_fin , '46');
                          $val_creance += (intVal($soldes->credit) - intVal($soldes->debit));
                          $verif_creance['46'] = 0;
                        }
                    }  
                } 
                foreach (self::$creances_4_DR as $creance_4_DR) {
                    //var_dump($comptabilite_globale->compte);
                    if( substr($comptabilite_globale->compte, 0 , 4) == substr($creance_4_DR , 0 , 4)){
                        
                        if(!array_key_exists($creance_4_DR ,$verif_creance)){
                          $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , $creance_4_DR) :  self::n_calcul_solde($base, $annee_debut, $annee_fin , $creance_4_DR);
                          $val_creance += (intVal($soldes->credit) - intVal($soldes->debit));
                          $verif_creance[$creance_4_DR] = 0;
                        }
                    }   
                } 
                    //disponibilite
                    foreach (self::$disponibilites_2 as $disponibilite) {
                        //var_dump($comptabilite_globale->compte);
                        if( substr($comptabilite_globale->compte, 0 , 2) == substr($disponibilite , 0 , 2)){
                            if (!array_key_exists(substr($disponibilite , 0 , 2),$verif_disponibilite)) { 
                                $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , $disponibilite) :  self::n_calcul_solde($base, $annee_debut, $annee_fin , $disponibilite);
                                $val_disponibilite += (intVal($soldes->credit) - intVal($soldes->debit));
                                $verif_disponibilite[$disponibilite] = 0;
                            }
                        }  
                        if( substr($comptabilite_globale->compte, 0 , 2) == '53'){
                            if (!array_key_exists(substr($disponibilite , 0 , 2),$verif_disponibilite)) { 
                                $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , '53') :  self::n_calcul_solde($base, $annee_debut, $annee_fin , '53');
                                $val_disponibilite += (intVal($soldes->credit) - intVal($soldes->debit));
                                $verif_disponibilite['53'] = 0;
                            }
                        }   
                    } 
                    //capitaux propres
                    foreach (self::$capitaux_propres_3 as $capitaux_propre) {
                        //var_dump($comptabilite_globale->compte);
                        if( substr($comptabilite_globale->compte, 0 , 3) == substr($capitaux_propre , 0 , 3)){
                            if (!array_key_exists(substr($capitaux_propre , 0 , 3),$verif_capitaux_propre)) { 
                                $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , $capitaux_propre) :  self::n_calcul_solde($base, $annee_debut, $annee_fin , $capitaux_propre);
                                $val_capitaux_propre += (intVal($soldes->debit) - intVal($soldes->credit));
                                $verif_capitaux_propre[$capitaux_propre] = 0;
                            }
                        }  
                        if( substr($comptabilite_globale->compte, 0 , 3) == '109'){
                            if (!array_key_exists(substr($capitaux_propre , 0 , 3), '109')) { 
                                $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , '109') :  self::n_calcul_solde($base, $annee_debut, $annee_fin , '109');
                                $val_capitaux_propre += (intVal($soldes->debit) - intVal($soldes->credit));
                                $verif_capitaux_propre['109'] = 0;
                            }
                        }  
                        if( substr($comptabilite_globale->compte, 0 , 3) == '145'){
                            if (!array_key_exists(substr($capitaux_propre , 0 , 3), '145')) { 
                                $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , '145') :  self::n_calcul_solde($base, $annee_debut, $annee_fin , '145');
                                $val_capitaux_propre += (intVal($soldes->debit) - intVal($soldes->credit));
                                $verif_capitaux_propre['145'] = 0;
                            }
                        }  
                    } 
                    //dettes financieres
                    foreach (self::$dettes_financieres_3 as $dette_financiere) {
                        //var_dump($comptabilite_globale->compte);
                        if( substr($comptabilite_globale->compte, 0 , 3) == substr($dette_financiere , 0 , 3)){
                            if (!array_key_exists(substr($dette_financiere , 0 , 2),$verif_dette_financiere)) { 
                                $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , $dette_financiere) :  self::n_calcul_solde($base, $annee_debut, $annee_fin , $dette_financiere);
                                $val_dette_financiere += (intVal($soldes->debit) - intVal($soldes->credit));
                                $verif_dette_financiere[$dette_financiere] = 0;
                            }
                        }  
                    } 
                    //dettes exploitations
                    foreach (self::$dettes_exploitations_2 as $dette_exploitation) {
                        //var_dump($comptabilite_globale->compte);
                        if( substr($comptabilite_globale->compte, 0 , 2) == substr($dette_exploitation , 0 , 2)){    
                            if (!array_key_exists(substr($dette_exploitation , 0 , 2),$verif_dette_exploitation)) {                        
                                $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , $dette_exploitation) :  self::n_calcul_solde($base, $annee_debut, $annee_fin , $dette_exploitation);
                                $val_dette_exploitation += (intVal($soldes->debit) - intVal($soldes->credit));
                                $verif_dette_exploitation[$dette_exploitation] = 0; 
                            }
                        }  
                    } 

                    }

                    $result['donnees'][] = [
                            'immobilisation_incorporelle' => abs($val_immobilisation_incorporelle),
                            'immobilisation_corporelle' => abs($val_immobilisation_corporelle),
                            'immobilisation_financiere' => abs($val_immobilisation_financiere),
                            'stock' => abs($val_stock),
                            'creance' => abs($val_creance),
                            'disponibilite' => abs($val_disponibilite),
                            'capitaux_propre' => abs($val_capitaux_propre),
                            'dette_financiere' => abs($val_dette_financiere),
                            'dette_exploitation' => abs($val_dette_exploitation),
                     ];

                }

             TraceActivite::insertOrIgnore([
                [
                    'created_at' => now(),
                    'operation' => TraceActivite::OPERATION_LECTURE,
                    'description' => 'Lecture du bilan',
                    'donnees' => json_encode([
                        'entrees' => [],
                        'sorties' => ['donnees'=>$result['donnees']]
                    ]),
                    'table_cible' => 'comptabilite_globales',
                    'utilisateur' => $request->user()->id
                ]
            ]);

            return $result;
        }catch(\Exception $e){
            Log::error('Journaux::compte_resultat a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }
    //lister compte resultat
    public static function compte_resultat(Request $request)
    {
        try{
            if (!($request instanceof Request)) {
                throw new \Exception('Instance de Illuminate\Http\Request attendue en paramètre, ' . (is_object($request) ? get_class($request) : gettype($request)) . ' trouvé.');
            }

            $result = [
                'code_http' => 200,
                'code_message' => 200,
                'donnees' => []
            ];

            //$annee = $request->query('annee', null);
            $annee_debut = $request->query('annee_debut', null);
            $annee_fin = $request->query('annee_fin', null);
            $base  = $request->query('base', null);
            if(is_null($base)){
                Log::info('Erreur de génération d\'un compte de résultat avec le paramètre base vide.');
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_BASE_VIDE';
                return $result;
            }
            
            if ($base == 'CONSOLIDEE') {
              
                $selection = ComptabiliteGlobale::leftJoin('plan_comptes','plan_comptes.id','=','comptabilite_globales.compte')
                                                    ->select('comptabilite_globales.debit','comptabilite_globales.credit','comptabilite_globales.date','comptabilite_globales.base','plan_comptes.compte')
                                                    ->where('departement','COMPTABILITE');

                $selection_n = ComptabiliteGlobale::leftJoin('plan_comptes','plan_comptes.id','=','comptabilite_globales.compte')
                                                    ->select('comptabilite_globales.debit','comptabilite_globales.credit','comptabilite_globales.date','comptabilite_globales.base','plan_comptes.compte')
                                                    ->where('departement','COMPTABILITE');
            }else {
                
                $selection = ComptabiliteGlobale::leftJoin('plan_comptes','plan_comptes.id','=','comptabilite_globales.compte')
                                                ->select('comptabilite_globales.debit','comptabilite_globales.credit','comptabilite_globales.date','comptabilite_globales.base','plan_comptes.compte')
                                                ->where('comptabilite_globales.base',$base)
                                                ->where('departement','COMPTABILITE');
                
                $selection_n = ComptabiliteGlobale::leftJoin('plan_comptes','plan_comptes.id','=','comptabilite_globales.compte')
                                                    ->select('comptabilite_globales.debit','comptabilite_globales.credit','comptabilite_globales.date','comptabilite_globales.base','plan_comptes.compte')
                                                    ->where('comptabilite_globales.base',$base)
                                                    ->where('departement','COMPTABILITE');
            }
            //$annee = 2019;
            // if(!is_null($annee))
            // {
            //     $selection->whereYear('comptabilite_globales.date', $annee)->orderBy('comptabilite_globales.id','desc');
            //     $selection_n->whereYear('comptabilite_globales.date', ($annee - 1))->orderBy('comptabilite_globales.id','desc');
            //     //var_dump(($annee - 1) );
            // }
            //Log::info('test1',['annee'=> intval(substr($annee_debut, 0, 4))]);
            if(!is_null($annee_debut) && !is_null($annee_fin)){
                $selection->whereBetween('comptabilite_globales.date', [$annee_debut, $annee_fin])->orderBy('comptabilite_globales.id','desc');
                $selection_n->whereYear('comptabilite_globales.date', ( intval(substr($annee_debut, 0, 4) ) - 1))->orderBy('comptabilite_globales.id','desc');
            }
            // $soldes = $base == 'CONSOLIDEE' ? self::calcul_solde_consolide($annee , '79') :  self::calcul_solde($base, $annee , '40');
            
            $val_Produits_exploitations_n = 0;
            $val_Revenus_n = 0;
            $val_Autres_produits_exploitations_n = 0;
            $val_Production_immobilisees_n = 0;
            $val_Total_produits_exploitations_n = 0;
            $val_Charges_exploitations_n = 0;
            $val_Variation_des_stocks_n = 0;
            $val_Achat_marchandises_n = 0;
            $val_Charges_de_personnels_n = 0;
            $val_Dotations_aux_amortissements_n = 0;
            $val_Autres_charges_exploitations_n = 0;
            $val_Total_charges_exploitations_n = 0;
            $val_Resultat_exploitations_n = 0;
            $val_Charges_financieres_nettes_n = 0;
            $val_Produits_des_placements_n = 0;
            $val_Autres_gains_ordinaires_n = 0;
            $val_Autres_pertes_ordinaires_n = 0;
            $val_Resultat_des_activites_ordinaires_avant_impots_n = 0;
            $val_Impot_sur_les_benefices_n = 0;
            $val_Resultat_des_activites_ordinaires_apres_impots_n = 0;
            $val_Elements_extraordinaires_n = 0;
            $val_Resultat_nets_n = 0;
            $val_Effet_des_modifications_n = 0;
            $val_Resultat_apres_modifications_n = 0;

            $verif_Produits_exploitations_n = array();
            $verif_Revenus_n = array();
            $verif_Autres_produits_exploitations_n = array();
            $verif_Production_immobilisees_n = array();
            $verif_Total_produits_exploitations_n = array();
            $verif_Charges_exploitations_n = array();
            $verif_Variation_des_stocks_n = array();
            $verif_Achat_marchandises_n = array();
            $verif_Charges_de_personnels_n = array();
            $verif_Dotations_aux_amortissements_n = array();
            $verif_Autres_charges_exploitations_n = array();
            $verif_Total_charges_exploitations_n = array();
            $verif_Resultat_exploitations_n = array();
            $verif_Charges_financieres_nettes_n = array();
            $verif_Produits_des_placements_n = array();
            $verif_Autres_gains_ordinaires_n = array();
            $verif_Autres_pertes_ordinaires_n = array();
            $verif_Resultat_des_activites_ordinaires_avant_impots_n = array();
            $verif_Impot_sur_les_benefices_n = array();
            $verif_Resultat_des_activites_ordinaires_apres_impots_n = array();
            $verif_Elements_extraordinaires_n = array();
            $verif_Resultat_nets_n = array();
            $verif_Effet_des_modifications_n = array();
            $verif_Resultat_apres_modifications_n = array();

            $val_Produits_exploitations = 0;
            $val_Revenus = 0;
            $val_Autres_produits_exploitations = 0;
            $val_Production_immobilisees = 0;
            $val_Total_produits_exploitations = 0;
            $val_Charges_exploitations = 0;
            $val_Variation_des_stocks = 0;
            $val_Achat_marchandises = 0;
            $val_Charges_de_personnels = 0;
            $val_Dotations_aux_amortissements = 0;
            $val_Autres_charges_exploitations = 0;
            $val_Total_charges_exploitations = 0;
            $val_Resultat_exploitations = 0;
            $val_Charges_financieres_nettes = 0;
            $val_Produits_des_placements = 0;
            $val_Autres_gains_ordinaires = 0;
            $val_Autres_pertes_ordinaires = 0;
            $val_Resultat_des_activites_ordinaires_avant_impots = 0;
            $val_Impot_sur_les_benefices = 0;
            $val_Resultat_des_activites_ordinaires_apres_impots = 0;
            $val_Elements_extraordinaires = 0;
            $val_Resultat_nets = 0;
            $val_Effet_des_modifications = 0;
            $val_Resultat_apres_modifications = 0;

            $verif_Produits_exploitations = array();
            $verif_Revenus = array();
            $verif_Autres_produits_exploitations = array();
            $verif_Production_immobilisees = array();
            $verif_Total_produits_exploitations = array();
            $verif_Charges_exploitations = array();
            $verif_Variation_des_stocks = array();
            $verif_Achat_marchandises = array();
            $verif_Charges_de_personnels = array();
            $verif_Dotations_aux_amortissements = array();
            $verif_Autres_charges_exploitations = array();
            $verif_Total_charges_exploitations = array();
            $verif_Resultat_exploitations = array();
            $verif_Charges_financieres_nettes = array();
            $verif_Produits_des_placements = array();
            $verif_Autres_gains_ordinaires = array();
            $verif_Autres_pertes_ordinaires = array();
            $verif_Resultat_des_activites_ordinaires_avant_impots = array();
            $verif_Impot_sur_les_benefices = array();
            $verif_Resultat_des_activites_ordinaires_apres_impots = array();
            $verif_Elements_extraordinaires = array();
            $verif_Resultat_nets = array();
            $verif_Effet_des_modifications = array();
            $verif_Resultat_apres_modifications = array();
            
            $comptabilites_globales = $selection->get();
            if(count($comptabilites_globales)){

                foreach($comptabilites_globales as $comptabilite_globale){
                    //Revenus 
                    foreach (self::$Revenus as $Revenu) {
                        //var_dump(substr($journal->compte, 0 , 3) );
                        if( substr($comptabilite_globale->compte, 0 , 3) == substr($Revenu , 0 , 3)){
                            // si le compte n'a pas déjà été croisé
                            if (!array_key_exists($Revenu, $verif_Revenus)) {
                            
                                $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , $Revenu) : self::n_calcul_solde($base, $annee_debut, $annee_fin , $Revenu);
                                $val_Revenus += (intVal($soldes->credit) - intVal($soldes->debit));
                                $verif_Revenus[$Revenu] = 0;
                                // var_dump(intVal($soldes->debit).' '.intVal($soldes->credit) );
                            }
                        }   
                    } 
                    //Autres_produits_exploitations
                    foreach (self::$Autres_produits_exploitations as $Autre_produit_exploitation) {
                        //var_dump(substr($comptabilite_globale->compte, 0 , 3) );
                        if( substr($comptabilite_globale->compte, 0 , 3) == substr($Autre_produit_exploitation , 0 , 3)){
                            // si le compte n'a pas déjà été croisé
                            if (!array_key_exists($Autre_produit_exploitation, $verif_Autres_produits_exploitations)) {
                                
                              $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , $Autre_produit_exploitation) : self::n_calcul_solde($base, $annee_debut, $annee_fin , $Autre_produit_exploitation);
                              $val_Autres_produits_exploitations += (intVal($soldes->credit) - intVal($soldes->debit));
                              $verif_Autres_produits_exploitations[$Autre_produit_exploitation]=0;
                            }
                        }   
                        if( substr($comptabilite_globale->compte, 0 , 2) == '74'){
                            // si le compte n'a pas déjà été croisé
                            if (!array_key_exists('74', $verif_Autres_produits_exploitations)) {
                              $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , '74') : self::n_calcul_solde($base, $annee_debut, $annee_fin , '74');
                              $val_Autres_produits_exploitations += (intVal($soldes->credit) - intVal($soldes->debit));
                              $verif_Autres_produits_exploitations['74']=0;
                            }
                        } 
                        if( substr($comptabilite_globale->compte, 0 , 2) == '79'){
                            // si le compte n'a pas déjà été croisé
                            if (!array_key_exists("79", $verif_Autres_produits_exploitations)) {

                              $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , '79') : self::n_calcul_solde($base, $annee_debut, $annee_fin , '79');
                              $val_Autres_produits_exploitations += (intVal($soldes->credit) - intVal($soldes->debit));
                              $verif_Autres_produits_exploitations["79"]=0;
                            }

                        }  
                    } 
                    //Production_immobilisees_2 
                    foreach (self::$Production_immobilisees_2 as $Production_immobilisee) {
                        //var_dump(substr($comptabilite_globale->compte, 0 , 3) );
                        if( substr($comptabilite_globale->compte, 0 , 2) == substr($Production_immobilisee , 0 , 2)){
                            // si le compte n'a pas déjà été croisé
                            if (!array_key_exists(substr($Production_immobilisee , 0 , 2),$verif_Production_immobilisees)) {
                              
                                $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , $Production_immobilisee) : self::n_calcul_solde($base, $annee_debut, $annee_fin , $Production_immobilisee);
                                $val_Production_immobilisees += (intVal($soldes->credit) - intVal($soldes->debit));
                                $verif_Production_immobilisees[substr($Production_immobilisee , 0 , 2)]= 0;
                            }
                        }   
                    } 
                    //Variation_des_stocks_2 
                    foreach (self::$Variation_des_stocks_2 as $Variation_de_stock) {
                        //var_dump(substr($comptabilite_globale->compte, 0 , 3) );
                        if( substr($comptabilite_globale->compte, 0 , 2) == substr($Variation_de_stock , 0 , 2)){
                            if (!array_key_exists(substr($Variation_de_stock , 0 , 2),$verif_Variation_des_stocks)) {
                              
                              $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , $Variation_de_stock) : self::n_calcul_solde($base, $annee_debut, $annee_fin , $Variation_de_stock);
                              $val_Variation_des_stocks += (intVal($soldes->debit) - intVal($soldes->credit));
                              $verif_Variation_des_stocks[substr($Variation_de_stock , 0 , 2)] = 0;
                            }
                        }   
                    } 
                    //Achat_marchandise 
                    foreach (self::$Achat_marchandises as $Achat_marchandise) {
                        //var_dump(substr($comptabilite_globale->compte, 0 , 3) );
                        if( substr($comptabilite_globale->compte, 0 , 2) == substr($Achat_marchandise , 0 , 2)){
                            if (!array_key_exists(substr($Achat_marchandise , 0 , 2),$verif_Achat_marchandises)) {

                              $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , $Achat_marchandise) : self::n_calcul_solde($base, $annee_debut, $annee_fin , $Achat_marchandise);
                              $val_Achat_marchandises += (intVal($soldes->debit) - intVal($soldes->credit));
                              $verif_Achat_marchandises[substr($Achat_marchandise , 0 , 2)]=0;
                            }
                        }   
                    } 
                    //Charges_de_personnels 
                    foreach (self::$Charges_de_personnels as $Charges_de_personnel) {
                        //var_dump(substr($comptabilite_globale->compte, 0 , 3) );
                        if( substr($comptabilite_globale->compte, 0 , 2) == substr($Charges_de_personnel , 0 , 2)){
                            if (!array_key_exists(substr($Charges_de_personnel , 0 , 2),$verif_Charges_de_personnels)) {

                              $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , $Charges_de_personnel) : self::n_calcul_solde($base, $annee_debut, $annee_fin , $Charges_de_personnel);
                              $val_Charges_de_personnels += (intVal($soldes->debit) - intVal($soldes->credit));
                              $verif_Charges_de_personnels[substr($Charges_de_personnel , 0 , 2)]= 0;
                            }
                        }   
                    } 
                    //Dotations_aux_amortissement 
                    foreach (self::$Dotations_aux_amortissements as $Dotations_aux_amortissement) {
                        //var_dump(substr($comptabilite_globale->compte, 0 , 3) );
                        if( substr($comptabilite_globale->compte, 0 , 3) == substr($Dotations_aux_amortissement , 0 , 3)){
                            if (!array_key_exists($Dotations_aux_amortissement,$verif_Dotations_aux_amortissements)) {
                             
                                $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , $Dotations_aux_amortissement) : self::n_calcul_solde($base, $annee_debut, $annee_fin , $Dotations_aux_amortissement);
                                $val_Dotations_aux_amortissements += (intVal($soldes->debit) - intVal($soldes->credit));
                                $verif_Dotations_aux_amortissements[$Dotations_aux_amortissement] = 0;
                            }
                        }   
                    } 
                    //Autres_charges_exploitations 
                    foreach (self::$Autres_charges_exploitations as $Autres_charges_exploitation) {
                        //var_dump(substr($comptabilite_globale->compte, 0 , 3) );
                        if( substr($comptabilite_globale->compte, 0 , 2) == substr($Autres_charges_exploitation , 0 , 2)){

                            if (!array_key_exists(substr($Autres_charges_exploitation , 0 , 2),$verif_Autres_charges_exploitations)) {
                              
                              $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , $Autres_charges_exploitation) : self::n_calcul_solde($base, $annee_debut, $annee_fin , $Autres_charges_exploitation);
                              $val_Autres_charges_exploitations += (intVal($soldes->debit) - intVal($soldes->credit));
                              $verif_Autres_charges_exploitations[substr($Autres_charges_exploitation , 0 , 2)] = 0;
                            }
                        }   
                    } 
                    //Charges_financieres_nettes 
                    foreach (self::$Charges_financieres_nettes as $Charges_financieres_nette) {
                        //var_dump(substr($comptabilite_globale->compte, 0 , 3) );
                        if( substr($comptabilite_globale->compte, 0 , 2) == substr($Charges_financieres_nette , 0 , 2)){                            
                            if (!array_key_exists(substr($Charges_financieres_nette , 0 , 2),$verif_Charges_financieres_nettes)) {   
                              
                              $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , $Charges_financieres_nette) : self::n_calcul_solde($base, $annee_debut, $annee_fin , $Charges_financieres_nette);
                              $val_Charges_financieres_nettes += (intVal($soldes->debit) - intVal($soldes->credit));
                              $verif_Charges_financieres_nettes[substr($Charges_financieres_nette , 0 , 2)]= 0;
                            }
                        }   
                        if( substr($comptabilite_globale->compte, 0 , 4) == '6865'){
                            if (!array_key_exists('6865', $verif_Charges_financieres_nettes)) {
                              $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , '6865') :  self::n_calcul_solde($base, $annee_debut, $annee_fin , '6865');
                              $val_Charges_financieres_nettes += (intVal($soldes->debit) - intVal($soldes->credit));
                              $verif_Charges_financieres_nettes['6865'] = 0;
                            }
                        } 
                    } 
                    //Produits_des_placements 
                    foreach (self::$Produits_des_placements_2 as $Produits_des_placement) {
                        //var_dump(substr($comptabilite_globale->compte, 0 , 3) );
                        if( substr($comptabilite_globale->compte, 0 , 2) == substr($Produits_des_placement , 0 , 2) && (substr($comptabilite_globale->compte, 0 , 3) != '776') ){
                            if (!array_key_exists(substr($Produits_des_placement, 0, 2),$verif_Produits_des_placements)) {
                              $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin ,  $Produits_des_placement) :  self::n_calcul_solde($base, $annee_debut, $annee_fin , $Produits_des_placement);
                              $val_Produits_des_placements += (intVal($soldes->debit) - intVal($soldes->credit));
                              $verif_Produits_des_placements[substr($Produits_des_placement, 0, 2)] = 0;
                            }
                        }   
                    } 
                    //Autres_gains_ordinaires 
                    foreach (self::$Autres_gains_ordinaires as $Autres_gains_ordinaire) {
                        //var_dump(substr($comptabilite_globale->compte, 0 , 3) );
                        if( substr($comptabilite_globale->compte, 0 , 3) == substr($Autres_gains_ordinaire , 0 , 3)){

                            if(!array_key_exists($Autres_gains_ordinaire, $verif_Autres_gains_ordinaires)){
                              $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , $Autres_gains_ordinaire) :  self::n_calcul_solde($base, $annee_debut, $annee_fin , $Autres_gains_ordinaire);
                              $val_Autres_gains_ordinaires += (intVal($soldes->debit) - intVal($soldes->credit));
                              $verif_Autres_gains_ordinaires[$Autres_gains_ordinaire] = 0;
                            }
                        }   
                    } 
                    //Autres_pertes_ordinaires 
                    foreach (self::$Autres_pertes_ordinaires as $Autres_pertes_ordinaire) {
                        //var_dump(substr($comptabilite_globale->compte, 0 , 3) );
                        if( substr($comptabilite_globale->compte, 0 , 3) == substr($Autres_pertes_ordinaire , 0 , 3)){
                           
                            if (!array_key_exists($Autres_pertes_ordinaire, $verif_Autres_pertes_ordinaires)) {
                              $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , $Autres_pertes_ordinaire) :  self::n_calcul_solde($base, $annee_debut, $annee_fin , $Autres_pertes_ordinaire);
                              $val_Autres_pertes_ordinaires += (intVal($soldes->debit) - intVal($soldes->credit));
                              $verif_Autres_pertes_ordinaires[$Autres_pertes_ordinaire] = 0;
                            }
                        }   
                    } 
                    //Impot_sur_les_benefices 
                    foreach (self::$Impot_sur_les_benefices as $Impot_sur_les_benefice) {
                        //var_dump(substr($comptabilite_globale->compte, 0 , 3) );
                        if( substr($comptabilite_globale->compte, 0 , 3) == substr($Impot_sur_les_benefice , 0 , 3)){

                            if (!array_key_exists($Impot_sur_les_benefice, $verif_Impot_sur_les_benefices)) {
                              $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , $Impot_sur_les_benefice) :  self::n_calcul_solde($base, $annee_debut, $annee_fin , $Impot_sur_les_benefice);
                              $val_Impot_sur_les_benefices += (intVal($soldes->debit) - intVal($soldes->credit));
                              $verif_Impot_sur_les_benefices[$Impot_sur_les_benefice] = 0;
                            }
                        }   
                    } 
                    //Elements_extraordinaires
                    foreach (self::$Elements_extraordinaires as $Elements_extraordinaire) {
                        //var_dump(substr($comptabilite_globale->compte, 0 , 3) );
                        if( substr($comptabilite_globale->compte, 0 , 2) == substr($Elements_extraordinaire , 0 , 2)){
            
                            if (!array_key_exists(substr($Elements_extraordinaire , 0 , 2), $verif_Elements_extraordinaires)) {
                              $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , $Elements_extraordinaire) :  self::n_calcul_solde($base, $annee_debut, $annee_fin , $Elements_extraordinaire);
                              $val_Elements_extraordinaires += (intVal($soldes->debit) - intVal($soldes->credit));
                              $verif_Elements_extraordinaires[substr($Elements_extraordinaire , 0 , 2)] = 0;
                            }
                        }   
                    }
                    //Resultat_nets
                    foreach (self::$Resultat_nets as $Resultat_net) {
                        //var_dump(substr($comptabilite_globale->compte, 0 , 3) );
                        if( substr($comptabilite_globale->compte, 0 , 2) == substr($Resultat_net , 0 , 2)){

                            if (!array_key_exists($Resultat_net, $verif_Resultat_nets)) {
                              $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , $Resultat_net) :  self::n_calcul_solde($base, $annee_debut, $annee_fin , $Resultat_net);
                              $val_Resultat_nets += (intVal($soldes->debit) - intVal($soldes->credit));
                              $verif_Resultat_nets[$Resultat_net] = 0;
                            }
                            
                        }   
                    }
                    //Effet_des_modifications
                    foreach (self::$Effet_des_modifications as $Effet_des_modification) {
                        //var_dump(substr($comptabilite_globale->compte, 0 , 3) );
                        if( substr($comptabilite_globale->compte, 0 , 3) == substr($Effet_des_modification , 0 , 3)){
                            
                            if (!array_key_exists($Effet_des_modification,$verif_Effet_des_modifications)) {
                              $soldes = $base == 'CONSOLIDEE' ? self::n_calcul_solde_consolide($annee_debut, $annee_fin , $Effet_des_modification) :  self::n_calcul_solde($base, $annee_debut, $annee_fin , $Effet_des_modification);
                              $val_Effet_des_modifications += (intVal($soldes->debit) - intVal($soldes->credit));
                              $verif_Effet_des_modifications[$Effet_des_modification] = 0;
                            }
                        }   
                    }  
                }
            
            }

            $comptabilites_globales_n = $selection_n->get();
            if(count($comptabilites_globales_n)){
                $annee = ( intval(substr($annee_debut, 0, 4) ) - 1);
                foreach($comptabilites_globales_n as $comptabilite_globale_n){
                    //Revenus 
                    foreach (self::$Revenus as $Revenu) {
                        //var_dump(substr($journal->compte, 0 , 3) );
                        if( substr($comptabilite_globale_n->compte, 0 , 3) == substr($Revenu , 0 , 3)){
                            if (!array_key_exists($Revenu, $verif_Revenus_n)) {
                
                                $soldes = $base == 'CONSOLIDEE' ? self::calcul_solde_consolide($annee ,  $Revenu) :  self::calcul_solde($base, $annee , $Revenu);
                                $val_Revenus_n += (intVal($soldes->credit) - intVal($soldes->debit));
                                $verif_Revenus_n[$Revenu] = 0;
                            }
                        }   
                    } 
                    //Autres_produits_exploitations
                    foreach (self::$Autres_produits_exploitations as $Autre_produit_exploitation) {
                        //var_dump(substr($comptabilite_globale_n->compte, 0 , 3) );
                        if (!array_key_exists($Autre_produit_exploitation, $verif_Autres_produits_exploitations_n)) {
                            if( substr($comptabilite_globale_n->compte, 0 , 3) == substr($Autre_produit_exploitation , 0 , 3)){
                                $soldes = $base == 'CONSOLIDEE' ? self::calcul_solde_consolide($annee , $Autre_produit_exploitation) :  self::calcul_solde($base, $annee , $Autre_produit_exploitation);
                                $val_Autres_produits_exploitations_n += (intVal($soldes->credit) - intVal($soldes->debit));
                                $verif_Autres_produits_exploitations_n[$Autre_produit_exploitation] = 0;
                            } 
                        }  
                        if (!array_key_exists('74', $verif_Autres_produits_exploitations_n)) {
                            if( substr($comptabilite_globale_n->compte, 0 , 2) == '74'){
                                $soldes = $base == 'CONSOLIDEE' ? self::calcul_solde_consolide($annee , '74') :  self::calcul_solde($base, $annee , '74');
                                $val_Autres_produits_exploitations_n += (intVal($soldes->credit) - intVal($soldes->debit));
                                $verif_Autres_produits_exploitations_n['74'] = 0;
                            } 
                        }
                        if (!array_key_exists('79', $verif_Autres_produits_exploitations_n)) {
                            if( substr($comptabilite_globale_n->compte, 0 , 2) == '79'){
                                
                                $soldes = $base == 'CONSOLIDEE' ? self::calcul_solde_consolide($annee , '79') :  self::calcul_solde($base, $annee , '79');
                                $val_Autres_produits_exploitations_n += (intVal($soldes->credit) - intVal($soldes->debit));
                                $verif_Autres_produits_exploitations_n['79'] = 0;
                            } 
                        } 
                    } 
                    //Production_immobilisees_2 
                    foreach (self::$Production_immobilisees_2 as $Production_immobilisee) {
                        if (!array_key_exists($Production_immobilisee, $verif_Production_immobilisees_n)) {
                            if( substr($comptabilite_globale_n->compte, 0 , 2) == substr($Production_immobilisee , 0 , 2)){
                               
                                $soldes = $base == 'CONSOLIDEE' ? self::calcul_solde_consolide($annee , $Production_immobilisee) :  self::calcul_solde($base, $annee , $Production_immobilisee);
                                $val_Production_immobilisees_n += (intVal($soldes->credit) - intVal($soldes->debit));
                                $verif_Production_immobilisees_n[$Production_immobilisee] = 0;
                            }  
                        } 
                    } 
                    //Variation_des_stocks_2 
                    foreach (self::$Variation_des_stocks_2 as $Variation_de_stock) {
                        //var_dump(substr($comptabilite_globale_n->compte, 0 , 3) );
                        if (!array_key_exists($Variation_de_stock, $verif_Variation_des_stocks_n)) {
                            if( substr($comptabilite_globale_n->compte, 0 , 2) == substr($Variation_de_stock , 0 , 2)){
                                $soldes = $base == 'CONSOLIDEE' ? self::calcul_solde_consolide($annee , $Variation_de_stock) :  self::calcul_solde($base, $annee , $Variation_de_stock);
                                $val_Variation_des_stocks_n += (intVal($soldes->debit) - intVal($soldes->credit));
                                $verif_Variation_des_stocks_n[$Variation_de_stock] = 0;
                            }   
                        }
                    } 
                    //Achat_marchandise 
                    foreach (self::$Achat_marchandises as $Achat_marchandise) {
                        //var_dump(substr($comptabilite_globale_n->compte, 0 , 3) );
                        if (!array_key_exists($Achat_marchandise, $verif_Achat_marchandises_n)) {
                            if( substr($comptabilite_globale_n->compte, 0 , 2) == substr($Achat_marchandise , 0 , 2)){
                                $soldes = $base == 'CONSOLIDEE' ? self::calcul_solde_consolide($annee , $Achat_marchandise) :  self::calcul_solde($base, $annee , $Achat_marchandise);
                                $val_Achat_marchandises_n += (intVal($soldes->debit) - intVal($soldes->credit));
                                $verif_Achat_marchandises_n[$Achat_marchandise] = 0;
                            }   
                        }
                    } 
                    //Charges_de_personnels 
                    foreach (self::$Charges_de_personnels as $Charges_de_personnel) {
                        //var_dump(substr($comptabilite_globale_n->compte, 0 , 3) );
                        if (!array_key_exists($Charges_de_personnel, $verif_Charges_de_personnels_n)) {
                            if( substr($comptabilite_globale_n->compte, 0 , 2) == substr($Charges_de_personnel , 0 , 2)){
                                $soldes = $base == 'CONSOLIDEE' ? self::calcul_solde_consolide($annee , $Charges_de_personnel) :  self::calcul_solde($base, $annee , $Charges_de_personnel);
                                $verif_Charges_de_personnels_n += (intVal($soldes->debit) - intVal($soldes->credit));
                                $verif_Charges_de_personnels_n[$Charges_de_personnel] = 0;
                            }   
                        }
                    } 
                    //Dotations_aux_amortissement 
                    foreach (self::$Dotations_aux_amortissements as $Dotations_aux_amortissement) {
                        //var_dump(substr($comptabilite_globale_n->compte, 0 , 3) );
                        if (!array_key_exists($Dotations_aux_amortissement, $verif_Dotations_aux_amortissements_n)) {
                            if( substr($comptabilite_globale_n->compte, 0 , 3) == substr($Dotations_aux_amortissement , 0 , 3)){
                                $soldes = $base == 'CONSOLIDEE' ? self::calcul_solde_consolide($annee , $Dotations_aux_amortissement) :  self::calcul_solde($base, $annee , $Dotations_aux_amortissement);
                                $val_Dotations_aux_amortissements_n += (intVal($soldes->debit) - intVal($soldes->credit));
                                $verif_Dotations_aux_amortissements_n[$Dotations_aux_amortissement] = 0;
                            }    
                        }
                    } 
                    //Autres_charges_exploitations 
                    foreach (self::$Autres_charges_exploitations as $Autres_charges_exploitation) {
                        //var_dump(substr($comptabilite_globale_n->compte, 0 , 3) );
                        if (!array_key_exists($Autres_charges_exploitation, $verif_Autres_charges_exploitations_n)) {
                            if( substr($comptabilite_globale_n->compte, 0 , 2) == substr($Autres_charges_exploitation , 0 , 2)){
                                $soldes = $base == 'CONSOLIDEE' ? self::calcul_solde_consolide($annee , $Autres_charges_exploitation) :  self::calcul_solde($base, $annee , $Autres_charges_exploitation);
                                $val_Autres_charges_exploitations_n += (intVal($soldes->debit) - intVal($soldes->credit));
                                $verif_Autres_charges_exploitations_n[$Autres_charges_exploitation] = 0;
                            }   
                        }
                    } 
                    //Charges_financieres_nettes 
                    foreach (self::$Charges_financieres_nettes as $Charges_financieres_nette) {
                       //var_dump(substr($comptabilite_globale_n->compte, 0 , 3) );
                       if (!array_key_exists($Charges_financieres_nette, $verif_Charges_financieres_nettes_n)) {
                            if( substr($comptabilite_globale_n->compte, 0 , 2) == substr($Charges_financieres_nette , 0 , 2)){
                                $soldes = $base == 'CONSOLIDEE' ? self::calcul_solde_consolide($annee , $Charges_financieres_nette) :  self::calcul_solde($base, $annee , $Charges_financieres_nette);
                                $val_Charges_financieres_nettes_n += (intVal($soldes->debit) - intVal($soldes->credit));
                                $verif_Charges_financieres_nettes_n[$Charges_financieres_nette] = 0;
                            }  
                        } 
                        if (!array_key_exists($Charges_financieres_nette, $verif_Charges_financieres_nettes_n)) {
                            if( substr($comptabilite_globale_n->compte, 0 , 4) == '6865'){
                                $soldes = $base == 'CONSOLIDEE' ? self::calcul_solde_consolide($annee , '6865') :  self::calcul_solde($base, $annee , '6865');
                                $val_Charges_financieres_nettes_n += (intVal($soldes->debit) - intVal($soldes->credit));
                                $verif_Charges_financieres_nettes_n['6865'] = 0;
                            } 
                        }
                    } 
                    //Produits_des_placements 
                    foreach (self::$Produits_des_placements_2 as $Produits_des_placement) {
                        //var_dump(substr($comptabilite_globale_n->compte, 0 , 3) );
                        if (!array_key_exists($Produits_des_placement, $verif_Produits_des_placements_n)) {
                            if( substr($comptabilite_globale_n->compte, 0 , 3) == substr($Autres_gains_ordinaire , 0 , 3)){
                                $soldes = $base == 'CONSOLIDEE' ? self::calcul_solde_consolide($annee , $Produits_des_placement) :  self::calcul_solde($base, $annee , $Produits_des_placement);
                                $val_Produits_des_placements_n += (intVal($soldes->debit) - intVal($soldes->credit));
                                $verif_Produits_des_placements_n[$Produits_des_placement] = 0;
                            }  
                        }       
                    } 
                    //Autres_gains_ordinaires 
                    foreach (self::$Autres_gains_ordinaires as $Autres_gains_ordinaire) {
                        //var_dump(substr($comptabilite_globale_n->compte, 0 , 3) );
                        if (!array_key_exists($Autres_gains_ordinaire, $verif_Autres_gains_ordinaires_n)) {
                            if( substr($comptabilite_globale_n->compte, 0 , 3) == substr($Autres_pertes_ordinaire , 0 , 3)){
                                $soldes = $base == 'CONSOLIDEE' ? self::calcul_solde_consolide($annee , $Autres_gains_ordinaire) :  self::calcul_solde($base, $annee , $Autres_pertes_ordinaire);
                                $val_Autres_gains_ordinaires_n += (intVal($soldes->debit) - intVal($soldes->credit));
                                $verif_Autres_gains_ordinaires_n[$Autres_gains_ordinaire] = 0;
                            }  
                        }  
                    } 
                    //Autres_pertes_ordinaires 
                    foreach (self::$Autres_pertes_ordinaires as $Autres_pertes_ordinaire) {
                        //var_dump(substr($comptabilite_globale_n->compte, 0 , 3) );
                        if (!array_key_exists($Autres_pertes_ordinaire, $verif_Autres_pertes_ordinaires_n)) {
                            if( substr($comptabilite_globale_n->compte, 0 , 3) == substr($Autres_pertes_ordinaire , 0 , 3)){
                                $soldes = $base == 'CONSOLIDEE' ? self::calcul_solde_consolide($annee , $Autres_pertes_ordinaire) :  self::calcul_solde($base, $annee , $Autres_pertes_ordinaire);
                                $val_Autres_pertes_ordinaires_n += (intVal($soldes->debit) - intVal($soldes->credit));
                                $verif_Autres_pertes_ordinaires_n[$Autres_pertes_ordinaire] = 0;
                            }  
                        }  
                    } 
                    //Impot_sur_les_benefices 
                    foreach (self::$Impot_sur_les_benefices as $Impot_sur_les_benefice) {
                        //var_dump(substr($comptabilite_globale_n->compte, 0 , 3) );
                        if (!array_key_exists($Impot_sur_les_benefice, $verif_Impot_sur_les_benefices_n)) {
                            if( substr($comptabilite_globale_n->compte, 0 , 3) == substr($Impot_sur_les_benefice , 0 , 3)){
                                $soldes = $base == 'CONSOLIDEE' ? self::calcul_solde_consolide($annee , $Impot_sur_les_benefice) :  self::calcul_solde($base, $annee , $Impot_sur_les_benefice);
                                $val_Impot_sur_les_benefices_n += (intVal($soldes->debit) - intVal($soldes->credit));
                                $verif_Impot_sur_les_benefices_n[$Impot_sur_les_benefice] = 0;
                            }    
                        }
                    } 
                    //Elements_extraordinaires
                    foreach (self::$Elements_extraordinaires as $Elements_extraordinaire) {
                        //var_dump(substr($comptabilite_globale_n->compte, 0 , 3) );
                        if (!array_key_exists($Elements_extraordinaire, $verif_Elements_extraordinaires_n)) {
                            if( substr($comptabilite_globale_n->compte, 0 , 2) == substr($Elements_extraordinaire , 0 , 2)){
                                $soldes = $base == 'CONSOLIDEE' ? self::calcul_solde_consolide($annee , $Elements_extraordinaire) :  self::calcul_solde($base, $annee , $Elements_extraordinaire);
                                $val_Elements_extraordinaires_n += (intVal($soldes->debit) - intVal($soldes->credit));
                                $verif_Elements_extraordinaire_n[$Elements_extraordinaire] = 0;
                            }  
                        } 
                    }
                    //Resultat_nets
                    foreach (self::$Resultat_nets as $Resultat_net) {
                        //var_dump(substr($comptabilite_globale_n->compte, 0 , 3) );
                        if (!array_key_exists($Resultat_net, $verif_Resultat_nets_n)) {
                            if( substr($comptabilite_globale_n->compte, 0 , 2) == substr($Resultat_net , 0 , 2)){
                                $soldes = $base == 'CONSOLIDEE' ? self::calcul_solde_consolide($annee , $Resultat_net) :  self::calcul_solde($base, $annee , $Resultat_net);
                                $val_Resultat_nets_n += (intVal($soldes->debit) - intVal($soldes->credit));
                                $verif_Resultat_nets_n[$Resultat_net] = 0;
                            }  
                        } 
                    }
                    //Effet_des_modifications
                    foreach (self::$Effet_des_modifications as $Effet_des_modification) {
                        //var_dump(substr($comptabilite_globale_n->compte, 0 , 3) );
                        if (!array_key_exists($Effet_des_modification, $verif_Effet_des_modifications_n)) {
                            if( substr($comptabilite_globale_n->compte, 0 , 3) == substr($Effet_des_modification , 0 , 3)){
                                $soldes = $base == 'CONSOLIDEE' ? self::calcul_solde_consolide($annee , $Effet_des_modification) :  self::calcul_solde($base, $annee , $Effet_des_modification);
                                $val_Effet_des_modifications_n += (intVal($soldes->debit) - intVal($soldes->credit));
                                $verif_Effet_des_modifications_n[$Effet_des_modification] = 0;
                            }  
                        }   
                    }
                }
            }
            $result['donnees'][] = [
                'annee_n-1'=>[
                    [
                        'Produits_exploitations' => abs($val_Produits_exploitations_n),
                        'Revenus' => abs($val_Revenus_n) ,
                        'Autres_produits_exploitations' => abs($val_Autres_produits_exploitations_n) ,
                        'Production_immobilisees' => abs($val_Production_immobilisees_n) ,
                        'Total_produits_exploitations' => abs($val_Total_produits_exploitations_n) ,
                        'Charges_exploitations' => abs($val_Charges_exploitations_n) ,
                        'Variation_des_stocks' => abs($val_Variation_des_stocks_n) ,
                        'Achat_marchandises' => abs($val_Achat_marchandises_n) ,
                        'Charges_de_personnels' => abs($val_Charges_de_personnels_n) ,
                        'Dotations_aux_amortissements' => abs($val_Dotations_aux_amortissements_n) ,
                        'Autres_charges_exploitations' => abs($val_Autres_charges_exploitations_n) ,
                        'Total_charges_exploitations' => abs($val_Total_charges_exploitations_n) ,
                        'Resultat_exploitations' => abs($val_Resultat_exploitations_n) ,
                        'Charges_financieres_nettes' => abs($val_Charges_financieres_nettes_n) ,
                        'Produits_des_placements' => abs($val_Produits_des_placements_n) ,
                        'Autres_gains_ordinaires' => abs($val_Autres_gains_ordinaires_n) ,
                        'Autres_pertes_ordinaires'=> abs($val_Autres_pertes_ordinaires_n) ,
                        'Resultat_des_activites_ordinaires_avant_impots' => abs($val_Resultat_des_activites_ordinaires_avant_impots_n) ,
                        'Impot_sur_les_benefices' => abs($val_Impot_sur_les_benefices_n) ,
                        'Resultat_des_activites_ordinaires_apres_impots' => abs($val_Resultat_des_activites_ordinaires_apres_impots_n) ,
                        'Elements_extraordinaires' => abs($val_Elements_extraordinaires_n) ,
                        'Resultat_nets' => abs($val_Resultat_nets_n) ,
                        'Effet_des_modifications' => abs($val_Effet_des_modifications_n) ,
                        'Resultat_apres_modifications' =>abs($val_Resultat_apres_modifications_n) ,
                    ]
                ],
                'annee_n'=>[
                    [
                        'Produits_exploitations' => abs($val_Produits_exploitations),
                        'Revenus' => abs($val_Revenus) ,
                        'Autres_produits_exploitations' => abs($val_Autres_produits_exploitations) ,
                        'Production_immobilisees' => abs($val_Production_immobilisees) ,
                        'Total_produits_exploitations' => abs($val_Total_produits_exploitations) ,
                        'Charges_exploitations' => abs($val_Charges_exploitations) ,
                        'Variation_des_stocks' => abs($val_Variation_des_stocks) ,
                        'Achat_marchandises' => abs($val_Achat_marchandises) ,
                        'Charges_de_personnels' => abs($val_Charges_de_personnels) ,
                        'Dotations_aux_amortissements' => abs($val_Dotations_aux_amortissements) ,
                        'Autres_charges_exploitations' => abs($val_Autres_charges_exploitations) ,
                        'Total_charges_exploitations' => abs($val_Total_charges_exploitations) ,
                        'Resultat_exploitations' => abs($val_Resultat_exploitations) ,
                        'Charges_financieres_nettes' => abs($val_Charges_financieres_nettes) ,
                        'Produits_des_placements' => abs($val_Produits_des_placements) ,
                        'Autres_gains_ordinaires' => abs($val_Autres_gains_ordinaires) ,
                        'Autres_pertes_ordinaires'=> abs($val_Autres_pertes_ordinaires) ,
                        'Resultat_des_activites_ordinaires_avant_impots' => abs($val_Resultat_des_activites_ordinaires_avant_impots) ,
                        'Impot_sur_les_benefices' => abs($val_Impot_sur_les_benefices) ,
                        'Resultat_des_activites_ordinaires_apres_impots' => abs($val_Resultat_des_activites_ordinaires_apres_impots) ,
                        'Elements_extraordinaires' => abs($val_Elements_extraordinaires) ,
                        'Resultat_nets' => abs($val_Resultat_nets) ,
                        'Effet_des_modifications' => abs($val_Effet_des_modifications) ,
                        'Resultat_apres_modifications' =>abs($val_Resultat_apres_modifications) ,
                    ]
                ]
                
         ];
            
         
            TraceActivite::insertOrIgnore([
                [
                    'created_at' => now(),
                    'operation' => TraceActivite::OPERATION_LECTURE,
                    'description' => 'Lecture du compte resultat',
                    'donnees' => json_encode([
                        'entrees' => [],
                        'sorties' => ['donnees'=>$result['donnees']]
                    ]),
                    'table_cible' => 'comptabilite_globales',
                    'utilisateur' => $request->user()->id
                ]
            ]);

            return $result;
        }catch(\Exception $e){
            Log::error('Journaux::bilan a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }
    //lister plan comptable
    public static function lister(Request $request)
    {
    
        try{
            if (!($request instanceof Request)) {
                throw new \Exception('Instance de Illuminate\Http\Request attendue en paramètre, ' . (is_object($request) ? get_class($request) : gettype($request)) . ' trouvé.');
            }

            $result = [
                'code_http' => 200,
                'code_message' => 200,
                'donnees' => []
            ];

            $annee_debut = $request->query('annee_debut', null);
            $annee_fin = $request->query('annee_fin', null);
            $classe = $request->query('classe', null);
            //$annee = $request->query('annee', null);
            $base = $request->query('base', null);
            if(is_null($base)){
                Log::info('Erreur de génération d\'un bilan avec le paramètre base vide.');
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_BASE_VIDE';
                return $result;
            }
            $filtres = json_decode($request->query('filtres', null));

            $limite = $request->query('limite', 50);
            $avant  = $request->query('avant', null);
            $apres  = $request->query('apres', null);

            if ($base == 'CONSOLIDEE') {
                //$selection = Journal::orderBy('journals.id','desc');
                $selection = ComptabiliteGlobale::leftJoin('plan_comptes','plan_comptes.id','=','comptabilite_globales.compte')
                                                ->select('comptabilite_globales.libelle','comptabilite_globales.created_at','comptabilite_globales.id',
                                                'comptabilite_globales.nro_piece','comptabilite_globales.debit','comptabilite_globales.credit',
                                                'comptabilite_globales.date','comptabilite_globales.departement','comptabilite_globales.base','plan_comptes.compte')
                                                ->where('departement','COMPTABILITE')
                                                ->take($limite+1);
                //Pour exportation
                $transactions = ComptabiliteGlobale::leftJoin('compte_bancaires', 'comptabilite_globales.compte_bancaire', '=', 'compte_bancaires.id')
                                  ->leftJoin('banques', 'banques.id', '=', 'compte_bancaires.banque')
                                  ->leftJoin('plan_comptes','plan_comptes.id','=','comptabilite_globales.compte')
                                  ->leftJoin('levee_fonds', 'comptabilite_globales.levee_fond', '=', 'levee_fonds.id')
                                  ->leftJoin('decaissements', 'comptabilite_globales.decaissement', '=', 'decaissements.id')
                                  ->select('comptabilite_globales.id AS id_comptabilite_globale',
                                           'comptabilite_globales.zone_gare', 'comptabilite_globales.gare_peage', 'comptabilite_globales.classe_vehicule',
                                           'comptabilite_globales.type', 'comptabilite_globales.sous_type', 'comptabilite_globales.statut_validation', 'comptabilite_globales.libelle',
                                           'comptabilite_globales.type_recettes_affectes', 'comptabilite_globales.type_revenus', 'comptabilite_globales.type_depenses_peage',
                                           'comptabilite_globales.details_depenses_peage', 'comptabilite_globales.type_depenses_pesage', 'comptabilite_globales.details_depenses_pesage',
                                           'comptabilite_globales.type_depenses_ada', 'comptabilite_globales.details_depenses_ada', 'comptabilite_globales.type_depenses_eft',
                                           'comptabilite_globales.details_depenses_eft', 'decaissements.id AS id_decaissement', 'decaissements.decompte AS decompte','decaissements.ordre_virement AS ordre_virement',
                                           'comptabilite_globales.libelle AS details_releve', 'comptabilite_globales.date AS date_operation',
                                           'comptabilite_globales.debit AS debit', 'comptabilite_globales.credit AS credit','comptabilite_globales.commentaire AS commentaire','comptabilite_globales.reference AS reference', 'plan_comptes.id as id_compte', 'plan_comptes.compte as compte_comptable','plan_comptes.description as description_compte','plan_comptes.type_flux as type_compte','plan_comptes.flux as flux_compte', 'levee_fonds.id AS id_levee_fond',
                                           'compte_bancaires.id AS id_compte_bancaire', 'compte_bancaires.numero AS compte_bancaire', 'banques.id AS id_banque',
                                          'banques.sigle AS sigle', 'banques.denomination AS denomination',
                                           'levee_fonds.objet_dette AS objet_dette', 'levee_fonds.banque AS nom_banque', 'levee_fonds.montant_pret AS montant_pret',
                                           'levee_fonds.montant_tirages AS montant_tirages', 'levee_fonds.date_mise_place AS date_mise_place', 'levee_fonds.nature_pret AS nature_pret',
                                           'levee_fonds.date_fin_remboursement AS date_fin_remboursement', 'levee_fonds.maturite AS maturite', 'levee_fonds.paiement_periodique AS paiement_periodique',
                                           'levee_fonds.taux_commission AS taux_commission', 'levee_fonds.duree_remboursement AS duree_remboursement', 'levee_fonds.taxe_operation_bourse AS taxe_operation_bourse',
                                           'levee_fonds.taux_interet AS taux_interet', 'levee_fonds.periode_differee AS periode_differee',/*'levee_fonds.facilite AS facilite',*/'comptabilite_globales.gare_pesage AS gare_pesage','comptabilite_globales.tranche_levee_fond AS tranche_levee_fond')
                                           ->where('departement','COMPTABILITE');
            }else {
                //$selection = Journal::where('journals.base',$base);
                $selection = ComptabiliteGlobale::leftJoin('plan_comptes','plan_comptes.id','=','comptabilite_globales.compte')
                                                ->select('comptabilite_globales.libelle','comptabilite_globales.created_at','comptabilite_globales.id',
                                                'comptabilite_globales.nro_piece','comptabilite_globales.debit','comptabilite_globales.credit',
                                                'comptabilite_globales.date','comptabilite_globales.departement','comptabilite_globales.base','plan_comptes.compte')
                                                ->where('comptabilite_globales.base',$base)
                                                ->where('departement','COMPTABILITE')
                                                ->take($limite+1);
                //Pour exportation
                $transactions = ComptabiliteGlobale::leftJoin('compte_bancaires', 'comptabilite_globales.compte_bancaire', '=', 'compte_bancaires.id')
                                  ->leftJoin('banques', 'banques.id', '=', 'compte_bancaires.banque')
                                  ->leftJoin('plan_comptes','plan_comptes.id','=','comptabilite_globales.compte')
                                  ->leftJoin('levee_fonds', 'comptabilite_globales.levee_fond', '=', 'levee_fonds.id')
                                  ->leftJoin('decaissements', 'comptabilite_globales.decaissement', '=', 'decaissements.id')
                                  ->select('comptabilite_globales.id AS id_comptabilite_globale',
                                           'comptabilite_globales.zone_gare', 'comptabilite_globales.gare_peage', 'comptabilite_globales.classe_vehicule',
                                           'comptabilite_globales.type', 'comptabilite_globales.sous_type', 'comptabilite_globales.statut_validation', 'comptabilite_globales.libelle',
                                           'comptabilite_globales.type_recettes_affectes', 'comptabilite_globales.type_revenus', 'comptabilite_globales.type_depenses_peage',
                                           'comptabilite_globales.details_depenses_peage', 'comptabilite_globales.type_depenses_pesage', 'comptabilite_globales.details_depenses_pesage',
                                           'comptabilite_globales.type_depenses_ada', 'comptabilite_globales.details_depenses_ada', 'comptabilite_globales.type_depenses_eft',
                                           'comptabilite_globales.details_depenses_eft', 'decaissements.id AS id_decaissement', 'decaissements.decompte AS decompte','decaissements.ordre_virement AS ordre_virement',
                                           'comptabilite_globales.libelle AS details_releve', 'comptabilite_globales.date AS date_operation',
                                           'comptabilite_globales.debit AS debit', 'comptabilite_globales.credit AS credit','comptabilite_globales.commentaire AS commentaire','comptabilite_globales.reference AS reference', 'plan_comptes.id as id_compte', 'plan_comptes.compte as compte_comptable','plan_comptes.description as description_compte','plan_comptes.type_flux as type_compte','plan_comptes.flux as flux_compte', 'levee_fonds.id AS id_levee_fond',
                                           'compte_bancaires.id AS id_compte_bancaire', 'compte_bancaires.numero AS compte_bancaire', 'banques.id AS id_banque',
                                          'banques.sigle AS sigle', 'banques.denomination AS denomination',
                                           'levee_fonds.objet_dette AS objet_dette', 'levee_fonds.banque AS nom_banque', 'levee_fonds.montant_pret AS montant_pret',
                                           'levee_fonds.montant_tirages AS montant_tirages', 'levee_fonds.date_mise_place AS date_mise_place', 'levee_fonds.nature_pret AS nature_pret',
                                           'levee_fonds.date_fin_remboursement AS date_fin_remboursement', 'levee_fonds.maturite AS maturite', 'levee_fonds.paiement_periodique AS paiement_periodique',
                                           'levee_fonds.taux_commission AS taux_commission', 'levee_fonds.duree_remboursement AS duree_remboursement', 'levee_fonds.taxe_operation_bourse AS taxe_operation_bourse',
                                           'levee_fonds.taux_interet AS taux_interet', 'levee_fonds.periode_differee AS periode_differee',/*'levee_fonds.facilite AS facilite',*/'comptabilite_globales.gare_pesage AS gare_pesage','comptabilite_globales.tranche_levee_fond AS tranche_levee_fond')
                                           ->where('comptabilite_globales.base',$base)
                                           ->where('departement','COMPTABILITE');
            }

            //Log::info('NUM',['comptet'=> $selection->get()]);
            if (is_null($avant) && is_null($apres)) {
                $selection->orderBy('comptabilite_globales.id', 'desc');
            }
                else
                {
                if(!is_null($avant)){
                    $selection->where('comptabilite_globales.id', '>', base64_decode($avant))->orderBy('comptabilite_globales.id', 'asc');
                }
                else{
                    $selection->where('comptabilite_globales.id', '<', base64_decode($apres))->orderBy('comptabilite_globales.id', 'desc');
                }
            }

            // if(!is_null($annee))
            // {
            //     $selection->whereYear('comptabilite_globales.date', $annee);
            //     $transactions->whereYear('comptabilite_globales.date', $annee);
            // }
            if(!is_null($annee_debut) && !is_null($annee_fin)){
                $selection->whereBetween('date', [$annee_debut , $annee_fin]);
                $transactions->whereBetween('date', [$annee_debut , $annee_fin]);
            }
            if(!is_null($classe))
            {
                $selection->where('plan_comptes.compte','LIKE',$classe.'%');
                $transactions->where('plan_comptes.compte','LIKE',$classe.'%');
                //var_dump(date('Y', strtotime('2068-06-15')));
            }
            //var_dump(date('Y', strtotime('2068-06-15')));
            if (!is_null($filtres)) {
              foreach ($filtres as $filtre) {
                  if ($filtre->type == "caractere") {
                      Filtre::req_caractere($selection,$filtre);
                      Filtre::req_caractere($transactions,$filtre);
                  }elseif ($filtre->type == "numeric") {
                      Filtre::req_caractere($selection,$filtre);
                      Filtre::req_numeric($transactions,$filtre);
                  }elseif ($filtre->type == "date") {
                      Filtre::req_caractere($selection,$filtre);
                      Filtre::req_date($transactions,$filtre);
                  }
              }
            }

            $comptabilites_globales = $selection->get();
            $transactionsExport =$transactions->get();
            //création du csv
            $chemin_destination = base_path() . "/downloads";
            $nom_fichier = 'Liste_transactions_comptables' . $request->user()->id . '_' . date('YmdHis') . '.csv';
            $fichier = fopen($chemin_destination . '/' . $nom_fichier, 'w');

            fputcsv($fichier, array_map('utf8_decode', ['Banque', 'Compte bancaire','Compte','Libellé','Débit', 'Crédit', 'Date de la transaction', 'Type', 'Sous-type','Zone de la gare','Gare de péage','Classe de véhicule','Gare pesage','Référence','Commentaire']), ';');

            foreach($transactionsExport as $transaction){
                fputcsv($fichier, array_map('utf8_decode', [$transaction->denomination . '('.$transaction->sigle.')',$transaction->compte_bancaire, $transaction->compte_comptable, $transaction->libelle,$transaction->debit,$transaction->credit, $transaction->date_operation,$transaction->type,$transaction->sous_type, $transaction->zone_gare,$transaction->gare_peage,$transaction->classe_vehicule,$transaction->gare_pesage,$transaction->reference, $transaction->commentaire]), ';');
            }

            fclose($fichier);
            $result['url'] = '/downloads/' . $nom_fichier;
            if(count($comptabilites_globales)){
                foreach($comptabilites_globales as $comptabilite_globale){
                    $result['donnees'][] = [
                        'id' => $comptabilite_globale->id,
                        'nro_piece'=> $comptabilite_globale->nro_piece,
                        'compte' => $comptabilite_globale->compte,
                        'libelle' => $comptabilite_globale->libelle,
                        'date' => $comptabilite_globale->date,
                        'debit' => $comptabilite_globale->debit,
                        'credit' => $comptabilite_globale->credit,
                        'date_creation' => $comptabilite_globale->created_at,
                        'departement' => $comptabilite_globale->departement,
                    ];
                }

                //pagination
                $result['pagination'] = [];

                if(count($result['donnees']) > $limite) {
                    if(!is_null($avant) && !is_null($apres)){
                        $result['donnees'] = array_slice($result['donnees'], 0, $limite);
                        $apres = $result['donnees'][count($result['donnees']) - 1]['id'];
                        $result['pagination']['curseurs'] = [
                                'apres' => base64_encode($apres)
                        ];
                    }else {
                        if (!is_null($avant)) {
                            $result['donnees'] = array_slice($result['donnees'], 0, $limite);
                            $result['donnees'] = array_reverse($result['donnees']);
                            $avant = $result['donnees'][0]['id'];
                            $apres = $result['donnees'][count($result['donnees']) - 1]['id'];
                            $result['pagination']['curseurs'] = [
                                'apres' => base64_encode($apres),
                                'avant' => base64_encode($avant)
                            ];
                        } else {
                            if (!is_null($apres)) {
                                $result['donnees'] = array_slice($result['donnees'], 0, $limite);
                                $avant = $result['donnees'][0]['id'];
                                $apres = $result['donnees'][count($result['donnees']) - 1]['id'];
                                $result['pagination']['curseurs'] = [
                                    'apres' => base64_encode($apres),
                                    'avant' => base64_encode($avant)
                                ];
                            }else{
                                $result['donnees'] = array_slice($result['donnees'], 0, $limite);
                                $apres = $result['donnees'][count($result['donnees']) - 1]['id'];
                                $result['pagination']['curseurs'] = [
                                    'apres' => base64_encode($apres)
                                ];
                            }
                        }
                    }
                }else{
                    if(!is_null($apres)){
                        $avant = $result['donnees'][0]['id'];
                        $result['pagination']['curseurs'] = [
                            'avant' => base64_encode($avant)
                        ];
                    }elseif(!is_null($avant)){
                        $result['donnees'] = array_reverse($result['donnees']);
                        $apres = $result['donnees'][count($result['donnees']) - 1]['id'];
                        $result['pagination']['curseurs'] = [
                            'apres' => base64_encode($apres)
                        ];
                    }
                }

                $parametres_url_suivante = [
                'limite' => $limite
                ];
                $parametres_url_precedente = [
                'limite' => $limite
                ];

                if(isset($result['pagination']['curseurs']['apres'])) {
                    $parametres_url_suivante['apres'] = $result['pagination']['curseurs']['apres'];
                    $result['pagination']['suivant'] = sprintf('/journaux?%s', http_build_query($parametres_url_suivante));
                }

                if(isset($result['pagination']['curseurs']['avant'])) {
                    $parametres_url_precedente['avant'] = $result['pagination']['curseurs']['avant'];
                    $result['pagination']['precedent'] = sprintf('/journaux?%s', http_build_query($parametres_url_precedente));
                }

                if(empty($result['pagination'])){
                        unset($result['pagination']);
                }

            }

            TraceActivite::insertOrIgnore([
                [
                    'created_at' => now(),
                    'operation' => TraceActivite::OPERATION_LECTURE,
                    'description' => 'Lecture des journaux',
                    'donnees' => json_encode([
                        'entrees' => [],
                        'sorties' => ['donnees'=>$result['donnees']]
                    ]),
                    'table_cible' => 'journals',
                    'utilisateur' => $request->user()->id
                ]
            ]);

            return $result;
        }catch(\Exception $e){
            Log::error('Plan comptable::lister a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }

    }
    //lister par les premiers digites
    public static function balance(Request $request)
    {
        try{
            if (!($request instanceof Request)) {
                throw new \Exception('Instance de Illuminate\Http\Request attendue en paramètre, ' . (is_object($request) ? get_class($request) : gettype($request)) . ' trouvé.');
            }

            $result = [
                'code_http' => 200,
                'code_message' => 200,
                'donnees' => []
            ];

            //$type_comptabilite = $request->query('type_comptabilite', null);

            $annee_debut = $request->query('annee_debut', null);
            $annee_fin = $request->query('annee_fin', null);
            $nombre_chiffre = $request->query('nombre_chiffre', null);
            $base = $request->query('base', null);
            if(is_null($base)){
                Log::info('Erreur de génération d\'un bilan avec le paramètre base vide.');
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_BASE_VIDE';
                return $result;
            }

            $x = 0;
            $compte = array();
            $montantCompte = array();
            $debit = array();
            $credit = array();

            $x_n = 0;
            $compte_n = array();
            $montantCompte_n = array();
            $debit_n = array();
            $credit_n = array();

            $annee = Carbon::createFromFormat('Y-m-d', $annee_debut)->format('Y');
            $mois_debut = Carbon::createFromFormat('Y-m-d', $annee_debut)->format('M');
            $mois_fin = Carbon::createFromFormat('Y-m-d', $annee_fin)->format('M');


            if ($base == 'CONSOLIDEE') {
                $selection = ComptabiliteGlobale::leftJoin('plan_comptes','plan_comptes.id','=','comptabilite_globales.compte')
                                                    ->where('departement','COMPTABILITE')
                                                    ->orderBy('plan_comptes.compte');
                if ($mois_debut == 'Jan' && $mois_fin == 'Dec') {   
                    $selection_n = ComptabiliteGlobale::leftJoin('plan_comptes','plan_comptes.id','=','comptabilite_globales.compte')
                                                    ->where('departement','COMPTABILITE')
                                                    ->orderBy('plan_comptes.compte');
                }
            }else {
                $selection = ComptabiliteGlobale::leftJoin('plan_comptes','plan_comptes.id','=','comptabilite_globales.compte')
                                                    ->where('comptabilite_globales.base',$base)
                                                    ->where('departement','COMPTABILITE')
                                                    ->orderBy('plan_comptes.compte');
                if ($mois_debut == 'Jan' && $mois_fin == 'Dec') {                                    
                    $selection_n = ComptabiliteGlobale::leftJoin('plan_comptes','plan_comptes.id','=','comptabilite_globales.compte')
                                                    ->where('comptabilite_globales.base',$base)
                                                    ->where('departement','COMPTABILITE')
                                                    ->orderBy('plan_comptes.compte');
                }
            }
           
            if(!is_null($annee_debut) && !is_null($annee_fin)){
                $selection->whereBetween('date', [$annee_debut , $annee_fin]);

                if ($mois_debut == 'Jan' && $mois_fin == 'Dec') {
                    $selection_n->whereYear('date', ($annee -1)); 
                    //Log::info('date',['Verification'=>'test']);
                }
            }

            $balances = $selection->get();
            
            foreach ($balances as $balance) {

                if ($nombre_chiffre == 1) {
                    if (!is_null($balance->compte)) {
                        if (empty($compte)) {
                            $compte[$x] = substr($balance->compte, 0, $nombre_chiffre);
                            $debit[$x] = $balance->debit;
                            $credit[$x] = $balance->credit;
                            $montantCompte[$x] = ($balance->debit - $balance->credit);
                            $x += 1;  
                        }else {
                            if (substr($balance->compte, 0, $nombre_chiffre)  == substr($compte[($x-1)], 0, $nombre_chiffre)) {
                                $debit[($x-1)] += $balance->debit;
                                $credit[($x-1)] += $balance->credit;
                                $montantCompte[($x-1)] += ($balance->debit - $balance->credit);
                            }else {
                                $compte[$x] = substr($balance->compte, 0, $nombre_chiffre);
                                $debit[$x] = $balance->debit;
                                $credit[$x] = $balance->credit;
                                $montantCompte[$x] = ($balance->debit - $balance->credit);
                                $x += 1;
                            }
                        }
                    }
                }elseif ($nombre_chiffre == 2) {
                    if (!is_null($balance->compte)) {
                        if (empty($compte)) {
                            $compte[$x] = substr($balance->compte, 0, $nombre_chiffre);
                            $debit[$x] = $balance->debit;
                            $credit[$x] = $balance->credit;
                            $montantCompte[$x] = ($balance->debit - $balance->credit);
                            $x += 1;  
                        }else {
                            if (substr($balance->compte, 0, $nombre_chiffre)  == substr($compte[($x-1)], 0, $nombre_chiffre)) {
                                $debit[($x-1)] += $balance->debit;
                                $credit[($x-1)] += $balance->credit;
                                $montantCompte[($x-1)] += ($balance->debit - $balance->credit);
                            }else {
                                $compte[$x] = substr($balance->compte, 0, $nombre_chiffre);
                                $debit[$x] = $balance->debit;
                                $credit[$x] = $balance->credit;
                                $montantCompte[$x] = ($balance->debit - $balance->credit);
                                $x += 1;
                            }
                        }
                    }
                }elseif ($nombre_chiffre == 3) {
                    if (!is_null($balance->compte)) {
                        if (empty($compte)) {
                            $compte[$x] = substr($balance->compte, 0, $nombre_chiffre);
                            $debit[$x] = $balance->debit;
                            $credit[$x] = $balance->credit;
                            $montantCompte[$x] = ($balance->debit - $balance->credit);
                            $x += 1;  
                        }else {
                            if (substr($balance->compte, 0, $nombre_chiffre)  == substr($compte[($x-1)], 0, $nombre_chiffre)) {
                                $debit[($x-1)] += $balance->debit;
                                $credit[($x-1)] += $balance->credit;
                                $montantCompte[($x-1)] += ($balance->debit - $balance->credit);
                            }else {
                                $compte[$x] = substr($balance->compte, 0, $nombre_chiffre);
                                $debit[$x] = $balance->debit;
                                $credit[$x] = $balance->credit;
                                $montantCompte[$x] = ($balance->debit - $balance->credit);
                                $x += 1;
                            }
                        }
                    }
                }elseif ($nombre_chiffre == 4) {
                    if (!is_null($balance->compte)) {
                        if (empty($compte)) {
                            $compte[$x] = substr($balance->compte, 0, $nombre_chiffre);
                            $debit[$x] = $balance->debit;
                            $credit[$x] = $balance->credit;
                            $montantCompte[$x] = ($balance->debit - $balance->credit);
                            $x += 1;  
                        }else {
                            if (substr($balance->compte, 0, $nombre_chiffre)  == substr($compte[($x-1)], 0, $nombre_chiffre)) {
                                $debit[($x-1)] += $balance->debit;
                                $credit[($x-1)] += $balance->credit;
                                $montantCompte[($x-1)] += ($balance->debit - $balance->credit);
                            }else {
                                $compte[$x] = substr($balance->compte, 0, $nombre_chiffre);
                                $debit[$x] = $balance->debit;
                                $credit[$x] = $balance->credit;
                                $montantCompte[$x] = ($balance->debit - $balance->credit);
                                $x += 1;
                            }
                        }
                    }
                }elseif ($nombre_chiffre == 5) {
                    if (!is_null($balance->compte)) {
                        if (empty($compte)) {
                            $compte[$x] = substr($balance->compte, 0, $nombre_chiffre);
                            $debit[$x] = $balance->debit;
                            $credit[$x] = $balance->credit;
                            $montantCompte[$x] = ($balance->debit - $balance->credit);
                            $x += 1;  
                        }else {
                            if (substr($balance->compte, 0, $nombre_chiffre)  == substr($compte[($x-1)], 0, $nombre_chiffre)) {
                                $debit[($x-1)] += $balance->debit;
                                $credit[($x-1)] += $balance->credit;
                                $montantCompte[($x-1)] += ($balance->debit - $balance->credit);
                            }else {
                                $compte[$x] = substr($balance->compte, 0, $nombre_chiffre);
                                $debit[$x] = $balance->debit;
                                $credit[$x] = $balance->credit;
                                $montantCompte[$x] = ($balance->debit - $balance->credit);
                                $x += 1;
                            }
                        }
                    }
                }elseif ($nombre_chiffre == 6) {
                    if (!is_null($balance->compte)) {
                        if (empty($compte)) {
                            $compte[$x] = substr($balance->compte, 0, $nombre_chiffre);
                            $debit[$x] = $balance->debit;
                            $credit[$x] = $balance->credit;
                            $montantCompte[$x] = ($balance->debit - $balance->credit);
                            $x += 1;  
                        }else {
                            if (substr($balance->compte, 0, $nombre_chiffre)  == substr($compte[($x-1)], 0, $nombre_chiffre)) {
                                $debit[($x-1)] += $balance->debit;
                                $credit[($x-1)] += $balance->credit;
                                $montantCompte[($x-1)] += ($balance->debit - $balance->credit);
                            }else {
                                $compte[$x] = substr($balance->compte, 0, $nombre_chiffre);
                                $debit[$x] = $balance->debit;
                                $credit[$x] = $balance->credit;
                                $montantCompte[$x] = ($balance->debit - $balance->credit);
                                $x += 1;
                            }
                        }
                    }
                }elseif ($nombre_chiffre == 7) {
                    if (!is_null($balance->compte)) {
                        if (empty($compte)) {
                            $compte[$x] = substr($balance->compte, 0, $nombre_chiffre);
                            $debit[$x] = $balance->debit;
                            $credit[$x] = $balance->credit;
                            $montantCompte[$x] = ($balance->debit - $balance->credit);
                            $x += 1;  
                        }else {
                            if (substr($balance->compte, 0, $nombre_chiffre)  == substr($compte[($x-1)], 0, $nombre_chiffre)) {
                                $debit[($x-1)] += $balance->debit;
                                $credit[($x-1)] += $balance->credit;
                                $montantCompte[($x-1)] += ($balance->debit - $balance->credit);
                            }else {
                                $compte[$x] = substr($balance->compte, 0, $nombre_chiffre);
                                $debit[$x] = $balance->debit;
                                $credit[$x] = $balance->credit;
                                $montantCompte[$x] = ($balance->debit - $balance->credit);
                                $x += 1;
                            }
                        }
                    }
                }elseif ($nombre_chiffre == 8) {
                    if (!is_null($balance->compte) && (strlen($balance->compte) == $nombre_chiffre)) {
                        if (empty($compte)) {
                            $compte[$x] = substr($balance->compte, 0, $nombre_chiffre);
                            $debit[$x] = $balance->debit;
                            $credit[$x] = $balance->credit;
                            $montantCompte[$x] = ($balance->debit - $balance->credit);
                            $x += 1;  
                        }else {
                            if (substr($balance->compte, 0, $nombre_chiffre)  == substr($compte[($x-1)], 0, $nombre_chiffre)) {
                                $debit[($x-1)] += $balance->debit;
                                $credit[($x-1)] += $balance->credit;
                                $montantCompte[($x-1)] += ($balance->debit - $balance->credit);
                            }else {
                                //Log::info('Compte',['Verification'=>substr($balance->compte, 0, $nombre_chiffre)]);
                                $compte[$x] = substr($balance->compte, 0, $nombre_chiffre);
                                $debit[$x] = $balance->debit;
                                $credit[$x] = $balance->credit;
                                $montantCompte[$x] = ($balance->debit - $balance->credit);
                                $x += 1;
                            }
                        }
                    }
                }
            }
            if ($mois_debut == 'Jan' && $mois_fin == 'Dec') { 
                $balances_n = $selection_n->get();
                
                foreach ($balances_n as $balance_n) {
                    Log::info('date',['Verification'=>'testoo']);
                    if ($nombre_chiffre == 1) {
                        if (!is_null($balance_n->compte)) {
                            if (empty($compte_n)) {
                                $compte_n[$x] = substr($balance_n->compte, 0, $nombre_chiffre);
                                $montantCompte_n[$x] = ($balance_n->debit - $balance_n->credit);
                                $x += 1;  
                            }else {
                                if (substr($balance_n->compte, 0, $nombre_chiffre)  == substr($compte_n[($x-1)], 0, $nombre_chiffre)) {
                                    $montantCompte_n[($x-1)] += ($balance_n->debit - $balance_n->credit);
                                }else {
                                    $compte_n[$x] = substr($balance_n->compte, 0, $nombre_chiffre);
                                    $montantCompte_n[$x] = ($balance_n->debit - $balance_n->credit);
                                    $x += 1;
                                }
                            }
                        }
                    }elseif ($nombre_chiffre == 2) {
                        if (!is_null($balance_n->compte)) {
                            if (empty($compte_n)) {
                                $compte_n[$x] = substr($balance_n->compte, 0, $nombre_chiffre);
                                $montantCompte_n[$x] = ($balance_n->debit - $balance_n->credit);
                                $x += 1;  
                            }else {
                                if (substr($balance_n->compte, 0, $nombre_chiffre)  == substr($compte_n[($x-1)], 0, $nombre_chiffre)) {
                                    $montantCompte_n[($x-1)] += ($balance_n->debit - $balance_n->credit);
                                }else {
                                    $compte_n[$x] = substr($balance_n->compte, 0, $nombre_chiffre);
                                    $montantCompte_n[$x] = ($balance_n->debit - $balance_n->credit);
                                    $x += 1;
                                }
                            }
                        }
                    }elseif ($nombre_chiffre == 3) {
                        if (!is_null($balance_n->compte)) {
                            if (empty($compte_n)) {
                                $compte_n[$x] = substr($balance_n->compte, 0, $nombre_chiffre);
                                $montantCompte_n[$x] = ($balance_n->debit - $balance_n->credit);
                                $x += 1;  
                            }else {
                                if (substr($balance_n->compte, 0, $nombre_chiffre)  == substr($compte_n[($x-1)], 0, $nombre_chiffre)) {
                                    $montantCompte_n[($x-1)] += ($balance_n->debit - $balance_n->credit);
                                }else {
                                    $compte_n[$x] = substr($balance_n->compte, 0, $nombre_chiffre);
                                    $montantCompte_n[$x] = ($balance_n->debit - $balance_n->credit);
                                    $x += 1;
                                }
                            }
                        }
                    }elseif ($nombre_chiffre == 4) {
                        if (!is_null($balance_n->compte)) {
                            if (empty($compte_n)) {
                                $compte_n[$x] = substr($balance_n->compte, 0, $nombre_chiffre);
                                $montantCompte_n[$x] = ($balance_n->debit - $balance_n->credit);
                                $x += 1;  
                            }else {
                                if (substr($balance_n->compte, 0, $nombre_chiffre)  == substr($compte_n[($x-1)], 0, $nombre_chiffre)) {
                                    $montantCompte_n[($x-1)] += ($balance_n->debit - $balance_n->credit);
                                }else {
                                    $compte_n[$x] = substr($balance_n->compte, 0, $nombre_chiffre);
                                    $montantCompte_n[$x] = ($balance_n->debit - $balance_n->credit);
                                    $x += 1;
                                }
                            }
                        }
                    }elseif ($nombre_chiffre == 5) {
                        if (!is_null($balance_n->compte)) {
                            if (empty($compte_n)) {
                                $compte_n[$x] = substr($balance_n->compte, 0, $nombre_chiffre);
                                $montantCompte_n[$x] = ($balance_n->debit - $balance_n->credit);
                                $x += 1;  
                            }else {
                                if (substr($balance_n->compte, 0, $nombre_chiffre)  == substr($compte_n[($x-1)], 0, $nombre_chiffre)) {
                                    $montantCompte_n[($x-1)] += ($balance_n->debit - $balance_n->credit);
                                }else {
                                    $compte_n[$x] = substr($balance_n->compte, 0, $nombre_chiffre);
                                    $montantCompte_n[$x] = ($balance_n->debit - $balance_n->credit);
                                    $x += 1;
                                }
                            }
                        }
                    }elseif ($nombre_chiffre == 6) {
                        if (!is_null($balance_n->compte)) {
                            if (empty($compte_n)) {
                                $compte_n[$x] = substr($balance_n->compte, 0, $nombre_chiffre);
                                $montantCompte_n[$x] = ($balance_n->debit - $balance_n->credit);
                                $x += 1;  
                            }else {
                                if (substr($balance_n->compte, 0, $nombre_chiffre)  == substr($compte_n[($x-1)], 0, $nombre_chiffre)) {
                                    $montantCompte_n[($x-1)] += ($balance_n->debit - $balance_n->credit);
                                }else {
                                    $compte_n[$x] = substr($balance_n->compte, 0, $nombre_chiffre);
                                    $montantCompte_n[$x] = ($balance_n->debit - $balance_n->credit);
                                    $x += 1;
                                }
                            }
                        }
                    }elseif ($nombre_chiffre == 7) {
                        if (!is_null($balance_n->compte)) {
                            if (empty($compte_n)) {
                                $compte_n[$x] = substr($balance_n->compte, 0, $nombre_chiffre);
                                $montantCompte_n[$x] = ($balance_n->debit - $balance_n->credit);
                                $x += 1;  
                            }else {
                                if (substr($balance_n->compte, 0, $nombre_chiffre)  == substr($compte_n[($x-1)], 0, $nombre_chiffre)) {
                                    $montantCompte_n[($x-1)] += ($balance_n->debit - $balance_n->credit);
                                }else {
                                    $compte_n[$x] = substr($balance_n->compte, 0, $nombre_chiffre);
                                    $montantCompte_n[$x] = ($balance_n->debit - $balance_n->credit);
                                    $x += 1;
                                }
                            }
                        }
                    }elseif ($nombre_chiffre == 8) {
                        if (!is_null($balance_n->compte)) {
                            if (empty($compte_n)) {
                                $compte_n[$x] = substr($balance_n->compte, 0, $nombre_chiffre);
                                $montantCompte_n[$x] = ($balance_n->debit - $balance_n->credit);
                                $x += 1;  
                            }else {
                                if (substr($balance_n->compte, 0, $nombre_chiffre)  == substr($compte_n[($x-1)], 0, $nombre_chiffre)) {
                                    $montantCompte_n[($x-1)] += ($balance_n->debit - $balance_n->credit);
                                }else {
                                    $compte_n[$x] = substr($balance_n->compte, 0, $nombre_chiffre);
                                    $montantCompte_n[$x] = ($balance_n->debit - $balance_n->credit);
                                    $x += 1;
                                }
                            }
                        }
                    }elseif ($nombre_chiffre == 9) {
                        if (!is_null($balance_n->compte)) {
                            if (empty($compte_n)) {
                                $compte_n[$x] = substr($balance_n->compte, 0, $nombre_chiffre);
                                $montantCompte_n[$x] = ($balance_n->debit - $balance_n->credit);
                                $x += 1;  
                            }else {
                                if (substr($balance_n->compte, 0, $nombre_chiffre)  == substr($compte_n[($x-1)], 0, $nombre_chiffre)) {
                                    $montantCompte_n[($x-1)] += ($balance_n->debit - $balance_n->credit);
                                }else {
                                    $compte_n[$x] = substr($balance_n->compte, 0, $nombre_chiffre);
                                    $montantCompte_n[$x] = ($balance_n->debit - $balance_n->credit);
                                    $x += 1;
                                }
                            }
                        }
                    }
                }
            }

            $select_plans = PlanCompte::get();

            foreach ($compte as $key => $compt) {
                //Log::info('compte',['type_1'=>$compt.' '.$montantCompte[$key]]);
                $description = "";
                $montant_n = 0;
                foreach ($select_plans as $select_plan) {
                    if ( $compt == $select_plan->compte) {
                        $description = $select_plan->description;
                    }
                }
                if (!empty($compte_n)) {
                    foreach ($compte_n as $ke => $compt_n) {
                        if ($compt_n == $compt) {
                            //Log::info('compte_n',['valeur'=>$compt_n]);
                            $montant_n = $montantCompte_n[$ke];
                        }
                    }
                }
                
                $solde = (abs(intVal($debit[$key])) + (intVal($montant_n) > 0 ? abs(intVal($montant_n)) : 0)) - (abs(intVal($credit[$key])) + (intVal($montant_n) < 0 ? abs(intVal($montant_n)) : 0));
                //Log::info('compte_n',['valeur'=>$solde]);

                $result['donnees'][]=[
                    'numero_compte'=>$compt,
                    'libelle' => str_replace('   ','',$description),
                    'a_nouveau_debit' => intVal($montant_n) > 0 ? abs(intVal($montant_n)) : 0,
                    'a_nouveau_credit' => intVal($montant_n) < 0 ? abs(intVal($montant_n)) : 0,
                    'mouvement_debit' =>abs(intVal($debit[$key])),
                    'mouvement_credit' =>abs(intVal($credit[$key])),
                    'total_debit' => abs(intVal($debit[$key])) + (intVal($montant_n) > 0 ? abs(intVal($montant_n)) : 0),
                    'total_credit' => abs(intVal($credit[$key])) + (intVal($montant_n) < 0 ? abs(intVal($montant_n)) : 0),
                    'solde_debit'=> $solde > 0 ? abs($solde) : 0,
                    'solde_credit'=>  $solde < 0 ? abs($solde) : 0,

                ];
            }
            
            TraceActivite::insertOrIgnore([
                [
                    'created_at' => now(),
                    'operation' => TraceActivite::OPERATION_LECTURE,
                    'description' => 'Lecture de la balance',
                    'donnees' => json_encode([
                        'entrees' => [],
                        'sorties' => ['donnees'=>$result['donnees']]
                    ]),
                    'table_cible' => 'comptabiliteGlobale',
                    'utilisateur' => $request->user()->id
                ]
            ]);

            return $result;
        }catch(\Exception $e){
            Log::error('Plan comptable::lister a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }
    //compta analytique
    public static function analytique(Request $request)
    {
        try{
            if (!($request instanceof Request)) {
                throw new \Exception('Instance de Illuminate\Http\Request attendue en paramètre, ' . (is_object($request) ? get_class($request) : gettype($request)) . ' trouvé.');
            }

            $result = [
                'code_http' => 200,
                'code_message' => 200,
                'donnees' => []
            ];

            $annee_debut = $request->query('annee_debut', null);
            $annee_fin = $request->query('annee_fin', null);
            $base = $request->query('base', null);
            if(is_null($base)){
                Log::info('Erreur de génération d\'un bilan avec le paramètre base vide.');
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_BASE_VIDE';
                return $result;
            }

            if ($base == 'CONSOLIDEE') {
                $selection = ComptabiliteGlobale::leftJoin('plan_comptes','plan_comptes.id','=','comptabilite_globales.compte')
                                                    ->where('departement','COMPTABILITE')
                                                    ->orderBy('plan_comptes.compte'); 

                $selection_analytique = CompteComptaAnalytique::leftJoin('groupes_compta_analytiques','groupes_compta_analytiques.id','=','comptes_compta_analytiques.groupe_compta_analytique')
                                                    ->select('groupes_compta_analytiques.libelle AS groupe_libelle','groupes_compta_analytiques.departement','comptes_compta_analytiques.libelle AS compte_libelle','comptes_compta_analytiques.numero');
               
            }else {
                $selection = ComptabiliteGlobale::leftJoin('plan_comptes','plan_comptes.id','=','comptabilite_globales.compte')
                                                    ->where('comptabilite_globales.base',$base)
                                                    ->where('departement','COMPTABILITE')
                                                    ->orderBy('plan_comptes.compte');
                $selection_analytique = CompteComptaAnalytique::leftJoin('groupes_compta_analytiques','groupes_compta_analytiques.id','=','comptes_compta_analytiques.groupe_compta_analytique')
                                                    ->where('groupes_compta_analytiques.departement',$base)                                    
                                                    ->select('groupes_compta_analytiques.libelle AS groupe_libelle','groupes_compta_analytiques.departement','comptes_compta_analytiques.libelle AS compte_libelle','comptes_compta_analytiques.numero');

            }
            if(!is_null($annee_debut) && !is_null($annee_fin)){
                $selection->whereBetween('date', [$annee_debut , $annee_fin]);
            }

            $x = 0;
            $compte = array();
            $libelleCompte = array();
            $libelleGroupe = array();
            $debit = array();
            $credit = array();

            $balances = $selection->get();
            $analytiques = $selection_analytique->get();

            foreach ($analytiques as $analytique) {
                //Log::info('analytique',['voir'=>$analytique]);

                $compte[$x] = $analytique->numero;
                $debit[$x] = 0;
                $credit[$x] = 0;
                $libelleCompte[$x] = $analytique->compte_libelle;
                $libelleGroupe[$x] = $analytique->groupe_libelle;
                $x += 1;

                    foreach ($balances as $balance) {
                        if ($balance->compte == $analytique->numero) {
                            $debit[($x-1)] += $balance->debit;
                            $credit[($x-1)] += $balance->credit;
                        }
                }
            }
            //Log::info('ana',['voir'=>$selection->get()]);
            $controle = array();
            $v = 0;
            foreach ($compte as $key => $compt) {
                if (!in_array($compt,$controle)) {
                    $result['donnees'][]=[
                        'numero_compte'=>$compt,
                        'groupe' => $libelleGroupe[$key],
                        'libelle' => $libelleCompte[$key],
                        'montant' => abs(intVal($debit[$key]) - intVal($credit[$key])), 
                        'solde' => (intVal($debit[$key]) - intVal($credit[$key])) > 0 ? 'debiteur' : 'crediteur',
                    ];
                }
                $controle[$v] = $compt;
                $v += 1;  
            }
            return $result;
        }catch(\Exception $e){
            Log::error('Plan comptable::lister a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }
    //importer journal
    public static function importer(Request $request)
    {
      try {
        if (!($request instanceof Request)) {
            throw new \Exception('Instance de Illuminate\Http\Request attendue en paramètre, ' . (is_object($request) ? get_class($request) : gettype($request)) . ' trouvé.');
        }

        $result = [
            'code_http'    => 200,
            'code_message' => 200  
        ];
        //$result = Upload::enregistrer($request);
		$inputs = json_decode($request->getContent(), true);

		if(!is_array($inputs)){
			$result['code_http']    = 400;
			$result['code_message'] = 'ERR_VALIDATION';
			$result['erreurs']      = 'Corps de la requête vide.';
			return $result;
		}
		$rules = [
			'url' => 'required|url',
		];

		$validator = Validator::make($inputs, $rules);
		if(!$validator->passes()){
			$result['code_http']    = 400;
			$result['code_message'] = 'ERR_VALIDATION';
			$result['erreurs']      = $validator->errors()->all();
			return $result;
		}

		$file_path_parts = explode('uploads/', $inputs['url']);
		if(count($file_path_parts)!=2){
			$result['code_http']    = 400;
			$result['code_message'] = 'ERR_URL_INVALIDE';
			$result['erreurs']      = 'Url du fichier invalide.';
			return $result;
		}
        // $var = '01/01/2020 - 040001000';
        // $varriable = substr($var,13);
        // var_dump($varriable); exit();
		try {
          Excel::import($import = new ImporterJournaux($request->user()->id), base_path('uploads/' . $file_path_parts[1]));
        } catch (\Exception $e) {

          if (isset($result['url'])) {
            unlink(base_path().$result['url']);
          }
          
          Log::error('Erreur d\'insertion des journaux. ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
          $result['code_http'] = 400;
          $result['code_message'] = 'ERR_DONNEES_INVALIDES';
          $result['erreurs'] = $e->getMessage();
          return $result;
        }

        TraceActivite::insertOrIgnore([
            [
                'created_at'  => now(),
                'operation'   => TraceActivite::OPERATION_AJOUT,
                'description' => 'Insertion de nouveaux journaux',
                'donnees'     => json_encode([
                    'entrees' => $inputs['url'],
                    'sorties' => ''
                ]),
                'table_cible' => 'journals',
                'utilisateur' => $request->user()->id,
            ]
        ]);
      } catch (\Exception $e) {
        Log::error('Erreur d\'insertion de journal .' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        $result['code_http'] = 400;
        $result['code_message'] = 'ERR_JOURNAL_INVALIDE';
      }

      return  $result;
    }

    //calcul de solde pour les bases Siège, Peage et Pesage
    private static function calcul_solde($base, $annee , $compte)
    {
        $solde = ComptabiliteGlobale::leftJoin('plan_comptes','plan_comptes.id','=','comptabilite_globales.compte')
            ->select('comptabilite_globales.date', DB::raw('SUM(comptabilite_globales.credit) AS credit'), DB::raw('SUM(comptabilite_globales.debit) AS debit'))
            ->where('plan_comptes.compte','LIKE',$compte.'%')
            ->where('comptabilite_globales.base', $base)
            ->where('departement','COMPTABILITE')
            ->whereYear('comptabilite_globales.date',$annee)
            ->first();

        return $solde;
    }

    //calcul de solde pour la base consolidee
    private static function calcul_solde_consolide($annee , $compte)
    {
        $solde = ComptabiliteGlobale::leftJoin('plan_comptes','plan_comptes.id','=','comptabilite_globales.compte')
            ->select('comptabilite_globales.date', DB::raw('SUM(comptabilite_globales.credit) AS credit'), DB::raw('SUM(comptabilite_globales.debit) AS debit'))
            ->where('plan_comptes.compte','LIKE',$compte.'%')
            ->where('departement','COMPTABILITE')
            ->whereYear('comptabilite_globales.date',$annee)
            ->first();

        return $solde;
    }

    //calcul de solde pour les bases Siège, Peage et Pesage
    private static function n_calcul_solde($base, $annee_debut ,$annee_fin , $compte)
    {
        $solde = ComptabiliteGlobale::leftJoin('plan_comptes','plan_comptes.id','=','comptabilite_globales.compte')
            ->select('comptabilite_globales.date', DB::raw('SUM(comptabilite_globales.credit) AS credit'), DB::raw('SUM(comptabilite_globales.debit) AS debit'))
            ->where('plan_comptes.compte','LIKE',$compte.'%')
            ->where('comptabilite_globales.base', $base)
            ->where('departement','COMPTABILITE')
            ->whereBetween('comptabilite_globales.date',[$annee_debut ,$annee_fin])
            ->first();

        return $solde;
    }

    //calcul de solde pour la base consolidee
    private static function n_calcul_solde_consolide($annee_debut ,$annee_fin , $compte)
    {
        $solde = ComptabiliteGlobale::leftJoin('plan_comptes','plan_comptes.id','=','comptabilite_globales.compte')
            ->select('comptabilite_globales.date', DB::raw('SUM(comptabilite_globales.credit) AS credit'), DB::raw('SUM(comptabilite_globales.debit) AS debit'))
            ->where('plan_comptes.compte','LIKE',$compte.'%')
            ->where('departement','COMPTABILITE')
            ->whereBetween('comptabilite_globales.date',[$annee_debut ,$annee_fin])
            ->first();

        return $solde;
    }
}
