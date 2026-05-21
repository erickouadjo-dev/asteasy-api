<?php

namespace App\Http\Controllers\api\v1\PlanComptes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PlanCompte as PlanCompteResource;
use App\Models\PlanCompte;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Log;


class PlancompteController extends Controller
{
    //
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try{
            $planCompte = PlanCompte::findOrFail($id);
            $this->authorize('view', $planCompte);
            $result = $planCompte->lire($request);
            return response()->json($result, $result['code_http']);
        }catch(\Illuminate\Auth\Access\AuthorizationException $e){
            Log::error('PlanComptes/PlanCompteController@show a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
        }catch(\Exception $e){
            Log::error('PlanComptes/PlanCompteController@show a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }

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
            $this->authorize('update', PlanCompte::class);
            $planCompte = PlanCompte::findOrFail($id);
            $result = $planCompte->modifier($request);
            return response()->json($result, $result['code_http']);
        }catch(\Illuminate\Auth\Access\AuthorizationException $e){
            Log::error('PlanComptes/PlanCompteController@update a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
        }catch(\Exception $e){
            Log::error('PlanComptes/PlanCompteController@update a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try{
            $this->authorize('delete', PlanCompteResource::class);

            $planCompte = PlanCompte::findOrFail($id);
            $result = $planCompte->supprimer($request);
            return response()->json($result, $result['code_http']);
        }catch(\Illuminate\Auth\Access\AuthorizationException $e){
            Log::error('PlanComptes/PlanCompteController@destroy a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
        }catch(\Exception $e){
            Log::error('PlanComptes/PlanCompteController@destroy a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }
}
