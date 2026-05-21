<?php
namespace App\Utility;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;

class Upload{
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
            $fichier = substr(pathinfo($inputs['fichier']->getClientOriginalName(),PATHINFO_FILENAME), 0, 20).'_f_' . $request->user()->id . '_' . date('YmdHis') . '.' . $inputs['fichier']->getClientOriginalExtension();
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
}
