<?php

namespace App\Http\Controllers\Api\V1\Decomptes\Commentaires;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utilisateur;
use App\Models\Commentaire;
use App\Utility\PolicyResources\Decomptes\Commentaires\Commentaires as DecomptesCommentairesCommentairesResource;
use Illuminate\Support\Facades\Log;

class CommentairesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index(Request $request, $id)
    {
       try{
            
            $this->authorize('viewAny', DecomptesCommentairesCommentairesResource::class);
            $result = Commentaire::lister($request, $id);
            return response()->json($result, $result['code_http']);

        }catch(\Illuminate\Auth\Access\AuthorizationException $e){
            Log::error('Decomptes\Commentaires\CommentairesController@store a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
        }catch(\Exception $e){
            Log::error('Decomptes\Commentaires\CommentairesController@store a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {

        try{
            $this->authorize('create', DecomptesCommentairesCommentairesResource::class);
            $result = Commentaire::ajouter($request, $id);
            return response()->json($result, $result['code_http']);
        }catch(\Illuminate\Auth\Access\AuthorizationException $e){
            Log::error('Decomptes\Commentaires\CommentairesController@store a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
        }catch(\Exception $e){
            Log::error('Decomptes\Commentaires\CommentairesController@store a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
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
