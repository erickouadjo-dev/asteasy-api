<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Utility\PolicyResources\LeveeFonds as LeveeFondsResource;
use App\Models\LeveeFond;
use Log;

class LeveeFondsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $this->authorize('viewAny', LeveeFondsResource::class);
            $result = LeveeFond::lister($request);
            return response()->json($result, $result['code_http']);
        }catch(\Illuminate\Auth\Access\AuthorizationException $e){
            Log::error('LeveeFondsController@index a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
        }catch(\Exception $e){
            Log::error('LeveeFondsController@index a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }
    public function store(Request $request)
    {
        try {
            $this->authorize('create', LeveeFondsResource::class);
            $result = LeveeFond::ajouter($request);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
             Log::error('LeveeFondsController@store a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
        }catch(\Exception $e){
            Log::error('LeveeFondsController@store a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }
}
