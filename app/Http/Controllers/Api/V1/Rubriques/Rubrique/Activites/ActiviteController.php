<?php

namespace App\Http\Controllers\Api\V1\Rubriques\Rubrique\Activites;

use App\Http\Controllers\Controller;
use App\Models\Activite as ActiviteResource;
use App\Models\Activite;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ActiviteController extends Controller
{
    //
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{

            $activite = Activite::findOrFail($id);
            $this->authorize('update', $activite); 
            $result = $activite->modifier($request);
            return response()->json($result, $result['code_http']);
        }catch(\Illuminate\Auth\Access\AuthorizationException $e){
            Log::error('Rubriques/Rubrique/Activites/ActiviteController@update a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
        }catch(\Exception $e){
            Log::error('Rubriques/Rubrique/Activites/ActiviteController@update a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }

    public function destroy(Request $request, $id)
    {
        try{

            $activite = Activite::findOrFail($id);
            $this->authorize('delete', $activite); 
            $result = $activite->supprimer($request);
            return response()->json($result, $result['code_http']);
        }catch(\Illuminate\Auth\Access\AuthorizationException $e){
            Log::error('Rubriques/Rubrique/Activites/ActiviteController@destroy a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
        }catch(\Exception $e){
            Log::error('Rubriques/Rubrique/Activites/ActiviteController@destroy a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }
}
