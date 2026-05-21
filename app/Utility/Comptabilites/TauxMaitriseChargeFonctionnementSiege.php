<?php
namespace App\Utility\Comptabilites;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\ComptabiliteGlobale;
use App\Models\Decompte;
use App\Models\Decaissement;
use Validator;

class TauxMaitriseChargeFonctionnementSiege{
    //générer graphe
    public static function generer(Request $request){
        try{
            $result = [
                'code_http' => 201,
                'code_message' => 201,
                'valeur' => 0
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
              'annee_debut'  => 'required|date',
              'annee_fin'  => 'required|date',
              'type' => 'required|string|in:i_taux_maitrise_charges_fonctionnement_siege2',
            ];

            $validator = Validator::make($inputs, $rules);

            if(!$validator->passes()){
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_VALIDATION';
                $result['erreurs'] = $validator->errors()->all();
                return $result;
            }
            
            $charge= self::totalCharge($inputs);
            $decompte=self::decomptesPayes($inputs);
            if ($charge != 0) {
              # code...
              $result["valeur"]=($decompte/$charge)*100;
            }
            
            return $result;
        }catch(\Exception $e){
            Log::error('TauxMaitriseChargeFonctionnementSiege::generer a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }

    public static function decomptesPayes($inputs)
    {
      # code...
      try{
        $total_montant_facture=0;
        $decomptes= Decompte::join("decaissements","decomptes.id","=","decaissements.decompte")
        ->join("comptabilite_globales","decaissement.id","=","comptabilite_globales.decaissement")
        ->select("decomptes.montant_facture_decompte")
        ->whereBetween("decomptes.date_facture",[$inputs["annee_debut"],$inputs["annee_fin"]])
        ->get();

        if(count($decomptes)){
          foreach ($decomptes as $decompte) {
            # code...
            $total_montant_facture+=$decompte->montant_facture_decompte;
          }
        }

        return $total_montant_facture;
      }catch(\Exception $e){
        Log::error('TauxMaitriseChargeFonctionnementSiege::decomptesPayes a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
      }
    }

    public static function totalCharge($inputs)
    {
      # code...
      try{
        $total_montant_charge=0;
        $charges= ComptabiliteGlobale::join("plan_comptes","comptabilite_globales.compte","=","plan_comptes.id")
        ->select("comptabilite_globales.credit","plan_comptes.compte")
        ->whereBetween("comptabilite_globales.date", [$inputs["annee_debut"],$inputs["annee_fin"]])
        ->where("comptabilite_globales.type","CHARGES")
        ->Where("plan_comptes.compte","LIKE","47%")
        ->orWhere("plan_comptes.compte","LIKE","60%")
        ->orWhere("plan_comptes.compte","LIKE","61%")
        ->orWhere("plan_comptes.compte","LIKE","62%")
        ->orWhere("plan_comptes.compte","LIKE","63%")
        ->orWhere("plan_comptes.compte","LIKE","64%")
        ->orWhere("plan_comptes.compte","LIKE","65%")
        ->orWhere("plan_comptes.compte","LIKE","66%")
        ->orWhere("plan_comptes.compte","LIKE","67%")
        ->get();

        if(count($charges)){
          foreach ($charges as $charge) {
            # code...
            $total_montant_charge+=$charge->montant_facture_charge;
          }
        }

        return $total_montant_charge;
      }catch(\Exception $e){
        Log::error('TauxMaitriseChargeFonctionnementSiege::totalCharge a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
      }
    }
}
