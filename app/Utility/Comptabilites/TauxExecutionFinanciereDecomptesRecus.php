<?php
namespace App\Utility\Comptabilites;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\ComptabiliteGlobale;
use App\Models\Decompte;
use App\Models\Decaissement;
use Validator;

class TauxExecutionFinanciereDecomptesRecus{
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
              'type' => 'required|string|in:i_taux_execution_financiere_decomptes_recu2',
            ];

            $validator = Validator::make($inputs, $rules);

            if(!$validator->passes()){
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_VALIDATION';
                $result['erreurs'] = $validator->errors()->all();
                return $result;
            }
            $decompte_impaye=self::decomptesRecusImpayes($inputs);
            $decompte_paye=self::decomptesRecusPayes($inputs);
            if ($decompte_impaye != 0 ) {
              # code...
              $result["valeur"]=($decompte_paye/$decompte_impaye)*100;
            }
            
            return $result;
        }catch(\Exception $e){
            Log::error('TauxExecutionFinanciereDecomptesRecus::generer a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }

    public static function decomptesRecusPayes($inputs)
    {
      # code...
      try{
        $total_decompte_paye=0;
        $decomptes= Decompte::join("decaissements","decomptes.id","=","decaissements.decompte")
        ->join("comptabilite_globales","decaissement.id","=","comptabilite_globales.decaissement")
        ->select("decomptes.montant_facture_decompte")
        ->whereBetween("decomptes.date_facture",[$inputs["annee_debut"],$inputs["annee_fin"]])
        ->get();

        if(count($decomptes)){
          foreach ($decomptes as $decompte) {
            # code...
            $total_decompte_paye+=$decompte->montant_facture_decompte;
          }
        }

        return $total_decompte_paye;
      }catch(\Exception $e){
        Log::error('TauxExecutionFinanciereDecomptesRecus::decomptesRecusPayes a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
      }
    }

    public static function decomptesRecusImpayes($inputs)
    {
      # code...
      try{
        $total_decompte_impaye=0;
        $decomptes= Decompte::select("decomptes.montant_facture_decompte")
        ->whereBetween("decomptes.date_facture",[$inputs["annee_debut"],$inputs["annee_fin"]])
        ->get();

        if(count($decomptes)){
          foreach ($decomptes as $decompte) {
            # code...
            $total_decompte_impaye+=$decompte->montant_facture_decompte;
          }
        }

        return $total_decompte_impaye;
      }catch(\Exception $e){
        Log::error('TauxExecutionFinanciereDecomptesRecus::decomptesRecusImpayes a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
      }
    }

}
