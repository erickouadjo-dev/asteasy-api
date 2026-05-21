<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProfilEmploye;
use App\Utility\PolicyResources\ProfilEmployes as ProfilEmployesResource;
use Illuminate\Support\Facades\Log;

class ProfilEmployesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', ProfilEmployesResource::class);
            $result = ProfilEmploye::lister($request);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('ProfilEmployesController@index a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requête non autorisée.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('ProfilEmployesController@index a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue.'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('create', ProfilEmployesResource::class);
            $result = ProfilEmploye::ajouter($request);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('ProfilEmployesController@store a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requête non autorisée.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('ProfilEmployesController@store a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue.'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $profilEmploye = ProfilEmploye::find($id);
            if (!$profilEmploye) {
                return response()->json([
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'Le profil n\'existe pas.'
                ], 404);
            }

            $this->authorize('view', $profilEmploye);
            $result = ProfilEmploye::recuperer($id);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('ProfilEmployesController@show a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requête non autorisée.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('ProfilEmployesController@show a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue.'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $profilEmploye = ProfilEmploye::find($id);
            if (!$profilEmploye) {
                return response()->json([
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'Le profil n\'existe pas.'
                ], 404);
            }

            $this->authorize('update', $profilEmploye);
            $result = ProfilEmploye::modifier($request, $id);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('ProfilEmployesController@update a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requête non autorisée.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('ProfilEmployesController@update a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $profilEmploye = ProfilEmploye::find($id);
            if (!$profilEmploye) {
                return response()->json([
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'Le profil n\'existe pas.'
                ], 404);
            }

            $this->authorize('delete', $profilEmploye);
            $result = ProfilEmploye::supprimer($id);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('ProfilEmployesController@destroy a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requête non autorisée.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('ProfilEmployesController@destroy a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue.'
            ], 500);
        }
    }
}
