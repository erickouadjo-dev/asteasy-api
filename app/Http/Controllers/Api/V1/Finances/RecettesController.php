<?php

namespace App\Http\Controllers\Api\V1\Finances;

use App\Utility\PolicyResources\Finances\Recettes as RecettesResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class RecettesController extends Controller
{
    public function index(Request $request)
    {
      try{
          $this->authorize('viewAny', RecettesResource::class);
          $result = Transaction::analyse_recettes($request);
          return response()->json($result, $result['code_http']);
      }catch(\Illuminate\Auth\Access\AuthorizationException $e){
          Log::error('Finances/RecettesController@index a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
          return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
      }catch(\Exception $e){
          Log::error('Finances/RecettesController@index a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
      }
    }
}
