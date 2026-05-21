<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Import\ImporterFichiers;
use App\Models\Transaction;
use App\Utility\PolicyResources\Transactions as TransactionResource;
use Log;

class TransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $this->authorize('viewAny', TransactionResource::class);
            $result = Transaction::lister($request);
            return response()->json($result, $result['code_http']);
        }catch(\Illuminate\Auth\Access\AuthorizationException $e){
            Log::error('TransactionController@index a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
        }catch(\Exception $e){
            Log::error('TransactionController@index a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }

    /**
     * Display a listing of graph datas of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        try{
            $this->authorize('viewAny', TransactionResource::class);
            $result = Transaction::generateGraph($request);
            return response()->json($result, $result['code_http']);
        }catch(\Illuminate\Auth\Access\AuthorizationException $e){
            Log::error('TransactionController@graph a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
        }catch(\Exception $e){
            Log::error('TransactionController@graph a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      try{
          $this->authorize('create', TransactionResource::class);
          $result = Transaction::import($request);
          return response()->json($result, $result['code_http']);
      }catch(\Illuminate\Auth\Access\AuthorizationException $e){
          Log::error('TransactionController@store a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
          return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
      }catch(\Exception $e){
          Log::error('TransactionController@store a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
      }
    }
}
