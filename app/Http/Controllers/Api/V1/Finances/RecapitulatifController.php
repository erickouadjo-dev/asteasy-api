<?php

namespace App\Http\Controllers\Api\V1\Finances;

use App\Utility\PolicyResources\Finances\Recapitulatif as RecapitulatifResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class RecapitulatifController extends Controller
{
    public function index(Request $request)
    {
      try{
          $this->authorize('viewAny', RecapitulatifResource::class);
          $result = Transaction::finances_recapitulatif($request);
          return response()->json($result, $result['code_http']);
      }catch(\Illuminate\Auth\Access\AuthorizationException $e){
          Log::error('Finances/RecapitulatifController@index a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
          return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
      }catch(\Exception $e){
          Log::error('Finances/RecapitulatifController@index a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
      }
    }
}
