<?php
namespace App\Utility\Comptabilites;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\ComptabiliteGlobale;
use Validator;

class TauxAccroissementRecettesPeage{
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
              'type' => 'required|string|in:i_taux_accroissement_recette_peage2',
            ];

            $validator = Validator::make($inputs, $rules);

            if(!$validator->passes()){
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_VALIDATION';
                $result['erreurs'] = $validator->errors()->all();
                return $result;
            }

            //année n
            $selection_n = ComptabiliteGlobale::select("credit","debit")
            ->whereBetween("comptabilite_globales.date",[$inputs["annee_debut"],$inputs["annee_fin"]])
            ->where("comptabilite_globales.sous_type","RECETTES_PEAGE")
            ->get();
            Log::info("test",["sum"=>$selection_n]);
            // année n-1
            $selection_n1 = ComptabiliteGlobale::select("credit")
            ->whereYear("comptabilite_globales.date",$inputs["annee_fin"])
            ->where("comptabilite_globales.sous_type","RECETTES_PEAGE")
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
                $total_n1+= $selection->credit-$selection->debit;
              }
            }
            $result["valeur"]=($total_n/$total_n1)*100;

            return $result;
        }catch(\Exception $e){
            Log::error('TauxAccroissementRecettesPeage::generer a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }
}
