<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SafetyAction;
use App\Utility\PolicyResources\SafetyActions as SafetyActionsResource;
use Illuminate\Support\Facades\Log;

class SafetyActionsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', SafetyActionsResource::class);
            $result = SafetyAction::lister($request);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('SafetyActionsController@index a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('SafetyActionsController@index a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue.'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $this->authorize('create', SafetyActionsResource::class);
            $result = SafetyAction::ajouter($request);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('SafetyActionsController@store a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('SafetyActionsController@store a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue.'], 500);
        }
    }

    public function show($id)
    {
        try {
            $safetyAction = SafetyAction::find($id);
            if (!$safetyAction) {
                return response()->json(['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'L\'action de sécurité n\'existe pas.'], 404);
            }

            $this->authorize('view', $safetyAction);
            $result = SafetyAction::recuperer($id);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('SafetyActionsController@show a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('SafetyActionsController@show a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $safetyAction = SafetyAction::find($id);
            if (!$safetyAction) {
                return response()->json(['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'L\'action de sécurité n\'existe pas.'], 404);
            }

            $this->authorize('update', $safetyAction);
            $result = SafetyAction::modifier($request, $id);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('SafetyActionsController@update a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('SafetyActionsController@update a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $safetyAction = SafetyAction::find($id);
            if (!$safetyAction) {
                return response()->json(['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'L\'action de sécurité n\'existe pas.'], 404);
            }

            $this->authorize('delete', $safetyAction);
            $result = SafetyAction::supprimer($id);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('SafetyActionsController@destroy a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('SafetyActionsController@destroy a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue.'], 500);
        }
    }
}
