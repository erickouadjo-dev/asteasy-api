<?php

namespace App\Http\Controllers\api\v1\ExecutionBudgetaires;

use App\Http\Controllers\Controller;
use App\Models\ComptabiliteGlobale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ExecutionBudgetaireController extends Controller
{
    //
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try{
            $comptabiliteGlobale = ComptabiliteGlobale::findOrFail($id);
            $this->authorize('view', $comptabiliteGlobale);
            $result = $comptabiliteGlobale->lire($request);
            return response()->json($result, $result['code_http']);
        }catch(\Illuminate\Auth\Access\AuthorizationException $e){
            Log::error('ComptabiliteGlobales/ComptabiliteGlobaleController@show a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
        }catch(\Exception $e){
            Log::error('ComptabiliteGlobales/ComptabiliteGlobaleController@show a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
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
            $comptabiliteGlobale = ComptabiliteGlobale::findOrFail($id);
            $this->authorize('update', $comptabiliteGlobale);
            $result = $comptabiliteGlobale->modifier($request);
            return response()->json($result, $result['code_http']);
        }catch(\Illuminate\Auth\Access\AuthorizationException $e){
            Log::error('ExecutionBudgetaires/ExecutionBudgetaireController@update a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
        }catch(\Exception $e){
            Log::error('ExecutionBudgetaires/ExecutionBudgetaireController@update a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        try{
            $comptabiliteGlobale = ComptabiliteGlobale::findOrFail($id);
            $this->authorize('delete', $comptabiliteGlobale);
            $result = $comptabiliteGlobale->supprimer($request);
            return response()->json($result, $result['code_http']);
        }catch(\Illuminate\Auth\Access\AuthorizationException $e){
            Log::error('ComptabiliteGlobales/ComptabiliteGlobaleController@destroy a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
        }catch(\Exception $e){
            Log::error('ComptabiliteGlobales/ComptabiliteGlobaleController@destroy a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }
}
