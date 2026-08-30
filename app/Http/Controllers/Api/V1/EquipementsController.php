<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Equipement;
use App\Utility\PolicyResources\Equipements as EquipementsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EquipementsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', EquipementsResource::class);
            $result = Equipement::lister($request);

            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('EquipementsController@index a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requete non autorisee.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('EquipementsController@index a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue.'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $this->authorize('create', EquipementsResource::class);
            $result = Equipement::ajouter($request);

            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('EquipementsController@store a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requete non autorisee.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('EquipementsController@store a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue.'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $equipement = Equipement::find($id);

            if (!$equipement) {
                return response()->json([
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'équipement n\'existe pas.'
                ], 404);
            }

            $this->authorize('view', $equipement);
            $result = Equipement::recuperer($id);

            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('EquipementsController@show a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requete non autorisee.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('EquipementsController@show a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue.'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $equipement = Equipement::find($id);

            if (!$equipement) {
                return response()->json([
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'équipement n\'existe pas.'
                ], 404);
            }

            $this->authorize('update', $equipement);
            $result = Equipement::modifier($request, $id);

            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('EquipementsController@update a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requete non autorisee.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('EquipementsController@update a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $equipement = Equipement::find($id);

            if (!$equipement) {
                return response()->json([
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'équipement n\'existe pas.'
                ], 404);
            }

            $this->authorize('delete', $equipement);
            $result = Equipement::supprimer($id);

            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('EquipementsController@destroy a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requete non autorisee.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('EquipementsController@destroy a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue.'
            ], 500);
        }
    }
}
