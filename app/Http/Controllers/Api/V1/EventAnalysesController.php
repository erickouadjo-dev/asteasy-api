<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EventAnalyse;
use App\Utility\PolicyResources\EventAnalyses as EventAnalysesResource;
use Illuminate\Support\Facades\Log;

class EventAnalysesController extends Controller
{
    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', EventAnalysesResource::class);
            $result = EventAnalyse::lister($request);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('EventAnalysesController@index a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('EventAnalysesController@index a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue.'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $this->authorize('create', EventAnalysesResource::class);
            $result = EventAnalyse::ajouter($request);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('EventAnalysesController@store a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('EventAnalysesController@store a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue.'], 500);
        }
    }

    public function show($id)
    {
        try {
            $eventAnalyse = EventAnalyse::find($id);
            if (!$eventAnalyse) {
                return response()->json(['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'L\'analyse d\'événement n\'existe pas.'], 404);
            }

            $this->authorize('view', $eventAnalyse);
            $result = EventAnalyse::recuperer($id);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('EventAnalysesController@show a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('EventAnalysesController@show a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $eventAnalyse = EventAnalyse::find($id);
            if (!$eventAnalyse) {
                return response()->json(['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'L\'analyse d\'événement n\'existe pas.'], 404);
            }

            $this->authorize('update', $eventAnalyse);
            $result = EventAnalyse::modifier($request, $id);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('EventAnalysesController@update a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('EventAnalysesController@update a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $eventAnalyse = EventAnalyse::find($id);
            if (!$eventAnalyse) {
                return response()->json(['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'L\'analyse d\'événement n\'existe pas.'], 404);
            }

            $this->authorize('delete', $eventAnalyse);
            $result = EventAnalyse::supprimer($id);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('EventAnalysesController@destroy a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('EventAnalysesController@destroy a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue.'], 500);
        }
    }
}
