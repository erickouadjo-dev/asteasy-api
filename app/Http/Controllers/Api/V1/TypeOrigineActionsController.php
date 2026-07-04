<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TypeOrigineAction;
use App\Utility\PolicyResources\TypeOrigineActions as TypeOrigineActionsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TypeOrigineActionsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', TypeOrigineActionsResource::class);
            $result = TypeOrigineAction::lister($request);

            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('TypeOrigineActionsController@index a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requete non autorisee.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('TypeOrigineActionsController@index a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

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
            $this->authorize('create', TypeOrigineActionsResource::class);
            $result = TypeOrigineAction::ajouter($request);

            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('TypeOrigineActionsController@store a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requete non autorisee.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('TypeOrigineActionsController@store a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

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
            $typeOrigineAction = TypeOrigineAction::find($id);

            if (!$typeOrigineAction) {
                return response()->json([
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'Le type d\'origine d\'action n\'existe pas.'
                ], 404);
            }

            $this->authorize('view', $typeOrigineAction);
            $result = TypeOrigineAction::recuperer($id);

            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('TypeOrigineActionsController@show a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requete non autorisee.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('TypeOrigineActionsController@show a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

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
            $typeOrigineAction = TypeOrigineAction::find($id);

            if (!$typeOrigineAction) {
                return response()->json([
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'Le type d\'origine d\'action n\'existe pas.'
                ], 404);
            }

            $this->authorize('update', $typeOrigineAction);
            $result = TypeOrigineAction::modifier($request, $id);

            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('TypeOrigineActionsController@update a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requete non autorisee.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('TypeOrigineActionsController@update a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

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
            $typeOrigineAction = TypeOrigineAction::find($id);

            if (!$typeOrigineAction) {
                return response()->json([
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'Le type d\'origine d\'action n\'existe pas.'
                ], 404);
            }

            $this->authorize('delete', $typeOrigineAction);
            $result = TypeOrigineAction::supprimer($id);

            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('TypeOrigineActionsController@destroy a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requete non autorisee.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('TypeOrigineActionsController@destroy a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue.'
            ], 500);
        }
    }
}
