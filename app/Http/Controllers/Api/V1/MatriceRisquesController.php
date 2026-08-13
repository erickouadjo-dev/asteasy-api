<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MatriceRisque;
use App\Utility\PolicyResources\MatriceRisques as MatriceRisquesResource;
use Illuminate\Support\Facades\Log;

class MatriceRisquesController extends Controller
{
    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', MatriceRisquesResource::class);
            $result = MatriceRisque::lister($request);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('MatriceRisquesController@index a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('MatriceRisquesController@index a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue.'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $this->authorize('create', MatriceRisquesResource::class);
            $result = MatriceRisque::ajouter($request);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('MatriceRisquesController@store a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('MatriceRisquesController@store a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue.'], 500);
        }
    }

    public function show($id)
    {
        try {
            $matriceRisque = MatriceRisque::find($id);
            if (!$matriceRisque) {
                return response()->json(['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'La matrice de risque n\'existe pas.'], 404);
            }

            $this->authorize('view', $matriceRisque);
            $result = MatriceRisque::recuperer($id);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('MatriceRisquesController@show a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('MatriceRisquesController@show a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $matriceRisque = MatriceRisque::find($id);
            if (!$matriceRisque) {
                return response()->json(['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'La matrice de risque n\'existe pas.'], 404);
            }

            $this->authorize('update', $matriceRisque);
            $result = MatriceRisque::modifier($request, $id);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('MatriceRisquesController@update a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('MatriceRisquesController@update a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $matriceRisque = MatriceRisque::find($id);
            if (!$matriceRisque) {
                return response()->json(['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'La matrice de risque n\'existe pas.'], 404);
            }

            $this->authorize('delete', $matriceRisque);
            $result = MatriceRisque::supprimer($id);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('MatriceRisquesController@destroy a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('MatriceRisquesController@destroy a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue.'], 500);
        }
    }
}
