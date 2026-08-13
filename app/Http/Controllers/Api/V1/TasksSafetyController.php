<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TasksSafety;
use App\Utility\PolicyResources\TasksSafety as TasksSafetyResource;
use Illuminate\Support\Facades\Log;

class TasksSafetyController extends Controller
{
    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', TasksSafetyResource::class);
            $result = TasksSafety::lister($request);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('TasksSafetyController@index a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('TasksSafetyController@index a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue.'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $this->authorize('create', TasksSafetyResource::class);
            $result = TasksSafety::ajouter($request);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('TasksSafetyController@store a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('TasksSafetyController@store a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue.'], 500);
        }
    }

    public function show($id)
    {
        try {
            $tasksSafety = TasksSafety::find($id);
            if (!$tasksSafety) {
                return response()->json(['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'La tâche n\'existe pas.'], 404);
            }

            $this->authorize('view', $tasksSafety);
            $result = TasksSafety::recuperer($id);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('TasksSafetyController@show a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('TasksSafetyController@show a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $tasksSafety = TasksSafety::find($id);
            if (!$tasksSafety) {
                return response()->json(['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'La tâche n\'existe pas.'], 404);
            }

            $this->authorize('update', $tasksSafety);
            $result = TasksSafety::modifier($request, $id);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('TasksSafetyController@update a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('TasksSafetyController@update a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $tasksSafety = TasksSafety::find($id);
            if (!$tasksSafety) {
                return response()->json(['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'La tâche n\'existe pas.'], 404);
            }

            $this->authorize('delete', $tasksSafety);
            $result = TasksSafety::supprimer($id);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('TasksSafetyController@destroy a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('TasksSafetyController@destroy a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue.'], 500);
        }
    }
}
