<?php
namespace App\Utility\Finances;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;
use DB;

class Indicateur{
    CONST PARAMS = ['i_taux_execution_financiere_decomptes_recu2',
                    'i_taux_execution_financiere_decomptes_anterieurs2',
                    'i_taux_accroissement_recette_peage2',
                    'i_taux_couverture_besoins2',
                    'i_taux_maitrise_charges_fonctionnement_siege2',
                    'i_taux_couverture_engagements_financiers2',
                    'i_taux_autonomie_financiere2',
                    'i_taux_liquidite2',
                    'i_taux_mobilisation_resources2',
                    'i_taux_mobilisation_resources_affectees2'
                  ];

    public static function lister(Request $request){
        try{
            $result = [
                'code_http' => 200
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
                'annee'  => 'required|numeric',
                'type' => 'required|string|in:'.implode(',', self::PARAMS).'',
            ];

            $validator = Validator::make($inputs, $rules);
            if(!$validator->passes()){
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_VALIDATION';
                $result['erreurs'] = $validator->errors()->all();
                return $result;
            }

            try {
              $result['valeur'] = DB::select('SELECT  '.$inputs['type'].'(?) as result',[$inputs['annee']])[0]->result;
            } catch (\Exception $e) {
              $result['code_http'] = 400;
              $result['code_message'] = 'ERR_CALL_FUNCTION';
              $result['erreurs'] = $e->getMessage();
              return $result;
            }

            return $result;
        }catch(\Exception $e){
            Log::error('Indicateur::lister a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }
}
