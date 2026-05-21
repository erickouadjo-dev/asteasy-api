<?php

namespace App\Http\Controllers\Api\V1\Finances;

use App\Utility\PolicyResources\Finances\Charges as ChargesResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class ChargesController extends Controller
{
    public function index(Request $request)
    {
      try{
          $this->authorize('viewAny', ChargesResource::class);
          $result = Transaction::analyse_charges($request);
          return response()->json($result, $result['code_http']);
      }catch(\Illuminate\Auth\Access\AuthorizationException $e){
          Log::error('Finances/ChargesResource@index a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
          return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
      }catch(\Exception $e){
          Log::error('Finances/ChargesResource@index a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
      }
    }
}
