<?php

namespace App\Http\Controllers\Api\V1\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Utility\PolicyResources\Transactions\Types as TransactionsTypesResource;
use Log;

class TypesController extends Controller
{
    /**
     * Lister les types des transactions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      try{
          $this->authorize('viewAny', TransactionsTypesResource::class);
          $result = Transaction::types($request);
          return response()->json($result, $result['code_http']);
      }catch(\Illuminate\Auth\Access\AuthorizationException $e){
          Log::error('Transactions\TypesController@index a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
          return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
      }catch(\Exception $e){
          Log::error('Transactions\TypesController@index a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
      }
    }
}
