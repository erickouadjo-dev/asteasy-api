<?php

namespace App\Http\Controllers\Api\v1\MissionControls;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MissionControle;
use Illuminate\Support\Facades\Log;

class MissionControlController extends Controller
{
    //
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
      try{
        $mission=MissionControle::findOrFail($id);
        $this->authorize('view', $mission);
        $result = $mission->lire($request);
        return response()->json($result, $result['code_http']);
      }catch(\Illuminate\Auth\Access\AuthorizationException $e){
          Log::error('MissionControls\MissionControlController@show a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
          return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
      }catch(\Exception $e){
          Log::error('MissionControls\MissionControlController@show a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
      }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
      try{
        $mission=MissionControle::findOrFail($id);
        $this->authorize('update', $mission);
        $result = $mission->modifier($request);
        return response()->json($result, $result['code_http']);
      }catch(\Illuminate\Auth\Access\AuthorizationException $e){
          Log::error('MissionControls\MissionControlController@update a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
          return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
      }catch(\Exception $e){
          Log::error('MissionControls\MissionControlController@update a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
      }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
      try{
        $mission=MissionControle::findOrFail($id);
        $this->authorize('delete', $mission);
        $result = $mission->supprimer($request);
        return response()->json($result, $result['code_http']);
      }catch(\Illuminate\Auth\Access\AuthorizationException $e){
          Log::error('MissionControls\MissionControlController@destroy a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
          return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
      }catch(\Exception $e){
          Log::error('MissionControls\MissionControlController@destroy a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
      }
    }
}
