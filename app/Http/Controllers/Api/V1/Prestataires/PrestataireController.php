<?php

namespace App\Http\Controllers\Api\V1\Prestataires;

use App\Http\Controllers\Controller;
use App\Models\Prestataire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PrestataireController extends Controller
{
    //
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
      try{
        $prestataire = Prestataire::findOrFail($id);
        $this->authorize('view', $prestataire);
        $result = $prestataire->lire($request);
        return response()->json($result, $result['code_http']);
      }catch(\Illuminate\Auth\Access\AuthorizationException $e){
          Log::error('Prestataires/PrestataireController@show a échoué avec le message ' . $e->getMessage(),  ['trace'=>$e->getTraceAsString()]);
          return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
      }catch(\Exception $e){
          Log::error('Prestataires/PrestataireController@show a échoué avec le message ' . $e->getMessage(),  ['trace'=>$e->getTraceAsString()]);
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
        $prestataire = Prestataire::findOrFail($id);
        $this->authorize('update', $prestataire);
        $result = $prestataire->modifier($request);
        return response()->json($result, $result['code_http']);
      }catch(\Illuminate\Auth\Access\AuthorizationException $e){
          Log::error('Prestataires/PrestataireController@update a échoué avec le message ' . $e->getMessage(),  ['trace'=>$e->getTraceAsString()]);
          return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
      }catch(\Exception $e){
          Log::error('Prestataires/PrestataireController@update a échoué avec le message ' . $e->getMessage(),  ['trace'=>$e->getTraceAsString()]);
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
        $prestataire = Prestataire::findOrFail($id);
        $this->authorize('delete', $prestataire);
        $result = $prestataire->supprimer($request);
        return response()->json($result, $result['code_http']);
      }catch(\Illuminate\Auth\Access\AuthorizationException $e){
          Log::error('Prestataires/PrestataireController@destroy a échoué avec le message ' . $e->getMessage(),  ['trace'=>$e->getTraceAsString()]);
          return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
      }catch(\Exception $e){
          Log::error('Prestataires/PrestataireController@destroy a échoué avec le message ' . $e->getMessage(),  ['trace'=>$e->getTraceAsString()]);
      }
    }
}
