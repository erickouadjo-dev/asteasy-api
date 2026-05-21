<?php

namespace App\Http\Controllers\Api\V1\Finances;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Utility\Comptabilites\TauxAccroissementRecettesPeage;
use App\Utility\PolicyResources\Finances\Indicateurs as IndicateursResource;
use Log;

class TauxAccroissementRecettesPeageController extends Controller
{
  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
      try{
          $this->authorize('viewAny', IndicateursResource::class);
          $result = TauxAccroissementRecettesPeage::generer($request);
          return response()->json($result, $result['code_http']);
      }catch(\Illuminate\Auth\Access\AuthorizationException $e){
          Log::error('Finances/TauxAccroissementRecettesPeageController@index a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
          return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
      }catch(\Exception $e){
          Log::error('Finances/TauxAccroissementRecettesPeageController@index a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
      }
  }
}
