<?php

namespace App\Http\Controllers\Api\V1\ExercicesFiscaux;

use App\Http\Controllers\Controller;
use App\Models\ExerciceFiscal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class ExerciceFiscalController extends Controller
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try{
            $exercice_fiscal = ExerciceFiscal::findOrFail($id);
            $this->authorize('view', $exercice_fiscal);
            $result = $exercice_fiscal->lire($request);
            return response()->json($result, $result['code_http']);
        }catch(\Illuminate\Auth\Access\AuthorizationException $e){
            Log::error('ExercicesFiscaux/ExerciceFiscalController@show a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
        }catch(\Exception $e){
            Log::error('ExercicesFiscaux/ExerciceFiscalController@show a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
