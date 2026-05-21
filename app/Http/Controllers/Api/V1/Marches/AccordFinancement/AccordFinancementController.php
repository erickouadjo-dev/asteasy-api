<?php

namespace App\Http\Controllers\Api\V1\Marches\AccordFinancement;

use App\Http\Controllers\Controller;
use App\Models\AccordFinancement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AccordFinancementController extends Controller
{
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
    public function show(Request $request,$id_marche, $id_af)
    {
        try{
            $accord_financement = AccordFinancement::findOrFail($id_af);
            $this->authorize('view', $accord_financement);
            $result = $accord_financement->lire($request,$id_marche, $id_af);
            return response()->json($result, $result['code_http']);
        }catch(\Illuminate\Auth\Access\AuthorizationException $e){
            Log::error('Marches/AccordFinancement/AccordFinancementController@show a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
        }catch(\Exception $e){
            Log::error('Marches/AccordFinancement/AccordFinancementController@show a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_marche, $id_af)
    {
        try{
            $accord_financement = AccordFinancement::findOrFail($id_af);
            $this->authorize('update', $accord_financement);
            $result = $accord_financement->modifier($request);
            //return response()->json($result, $result['code_http']);
        }catch(\Illuminate\Auth\Access\AuthorizationException $e){
            Log::error('Marches/AccordFinancement/AccordFinancementController@show a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
        }catch(\Exception $e){
            Log::error('Marches/AccordFinancement/AccordFinancementController@show a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id_marche, $id_af)
    {
        try{
            $accord_financement = AccordFinancement::findOrFail($id_af);
            $this->authorize('delete', $accord_financement);
            $result = $accord_financement->supprimer($request, $id_marche, $id_af);
            return response()->json($result, $result['code_http']);
        }catch(\Illuminate\Auth\Access\AuthorizationException $e){
            Log::error('Marches/AccordFinancement/AccordFinancementController@show a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
        }catch(\Exception $e){
            Log::error('Marches/AccordFinancement/AccordFinancementController@show a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }
}