<?php
namespace App\Utility\Comptabilites;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\ComptabiliteGlobale;
use Validator;

class TauxMobilisationRessources{
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
              'type' => 'required|string|in:i_taux_mobilisation_ressources2',
            ];

            $validator = Validator::make($inputs, $rules);

            if(!$validator->passes()){
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_VALIDATION';
                $result['erreurs'] = $validator->errors()->all();
                return $result;
            }

            //année n
            $selection_n = ComptabiliteGlobale::join("plan_comptes","comptabilite_globales.compte","=","plan_comptes.id")
            ->select("comptabilite_globales.credit")
            ->whereBetween("comptabilite_globales.date",[$inputs["annee_debut"],$inputs["annee_fin"]])
            ->where("comptabilite_globales.type","REVENUS")
            ->Where("plan_comptes.compte","LIKE","706%")
            ->orWhere("plan_comptes.compte","LIKE","771%")
            ->orWhere("plan_comptes.compte","LIKE","75%")
            ->orWhere("plan_comptes.compte","LIKE","1629%")
            ->orWhere("plan_comptes.compte","LIKE","8460%")
            ->orWhere("plan_comptes.compte","LIKE","4739%")
            ->get();
            // année n-1
            $selection_n1 = ComptabiliteGlobale::join("plan_comptes","comptabilite_globales.compte","=","plan_comptes.id")
            ->select("comptabilite_globales.budget_annuel")
            ->whereBetween("comptabilite_globales.date",[$inputs["annee_debut"],$inputs["annee_fin"]])
            ->where("comptabilite_globales.sous_type","REVENUS")
            ->Where("plan_comptes.compte","LIKE","706%")
            ->orWhere("plan_comptes.compte","LIKE","771%")
            ->orWhere("plan_comptes.compte","LIKE","75%")
            ->orWhere("plan_comptes.compte","LIKE","1629%")
            ->orWhere("plan_comptes.compte","LIKE","8460%")
            ->orWhere("plan_comptes.compte","LIKE","4739%")
            ->get();
            $total_n=0;
            if (count($selection_n)) {
              foreach ($selection_n as $selection) {
                $total_n+= $selection->credit-$selection->debit;
              }
            }
            $total_n1=0;
            if (count($selection_n1)) {
              foreach ($selection_n1 as $selection) {
                $total_n1+= $selection->budget_annuel;
              }
            }
            $result["valeur"]=($total_n/$total_n1)*100;

            return $result;
        }catch(\Exception $e){
            Log::error('TauxMobilisationRessources::generer a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }
}
