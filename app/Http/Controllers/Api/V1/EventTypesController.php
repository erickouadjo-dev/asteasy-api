<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EventType;
use App\Utility\PolicyResources\EventTypes as EventTypesResource;
use Illuminate\Support\Facades\Log;

class EventTypesController extends Controller
{
    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', EventTypesResource::class);
            $result = EventType::lister($request);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('EventTypesController@index a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('EventTypesController@index a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue.'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $this->authorize('create', EventTypesResource::class);
            $result = EventType::ajouter($request);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('EventTypesController@store a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('EventTypesController@store a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue.'], 500);
        }
    }

    public function show($id)
    {
        try {
            $eventType = EventType::find($id);
            if (!$eventType) {
                return response()->json(['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'Le type d\'événement n\'existe pas.'], 404);
            }

            $this->authorize('view', $eventType);
            $result = EventType::recuperer($id);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('EventTypesController@show a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('EventTypesController@show a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $eventType = EventType::find($id);
            if (!$eventType) {
                return response()->json(['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'Le type d\'événement n\'existe pas.'], 404);
            }

            $this->authorize('update', $eventType);
            $result = EventType::modifier($request, $id);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('EventTypesController@update a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('EventTypesController@update a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $eventType = EventType::find($id);
            if (!$eventType) {
                return response()->json(['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'Le type d\'événement n\'existe pas.'], 404);
            }

            $this->authorize('delete', $eventType);
            $result = EventType::supprimer($id);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('EventTypesController@destroy a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('EventTypesController@destroy a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue.'], 500);
        }
    }
}
