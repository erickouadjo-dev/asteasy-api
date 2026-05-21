<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Utility\PolicyResources\PlanComptables as PlanComptablesMontantsResource;
use App\Models\PlanComptable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PlanComptablesMontantsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $this->authorize('viewAny', PlanComptablesMontantsResource::class);
            $result = PlanComptable::agregation($request);
            return response()->json($result, $result['code_http']);
        }catch(\Illuminate\Auth\Access\AuthorizationException $e){
            Log::error('PlanComptablesMontantsController@index a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
        }catch(\Exception $e){
            Log::error('PlanComptablesMontantsController@index a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
