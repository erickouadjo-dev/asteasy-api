<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Avancement;
use App\Utility\PolicyResources\Avancements as AvancementsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AvancementsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', AvancementsResource::class);
            $result = Avancement::lister($request);

            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('AvancementsController@index a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requete non autorisee.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('AvancementsController@index a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

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
            $this->authorize('create', AvancementsResource::class);
            $result = Avancement::ajouter($request);

            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('AvancementsController@store a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requete non autorisee.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('AvancementsController@store a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

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
            $avancement = Avancement::find($id);

            if (!$avancement) {
                return response()->json([
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'avancement n\'existe pas.'
                ], 404);
            }

            $this->authorize('view', $avancement);
            $result = Avancement::recuperer($id);

            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('AvancementsController@show a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requete non autorisee.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('AvancementsController@show a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

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
            $avancement = Avancement::find($id);

            if (!$avancement) {
                return response()->json([
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'avancement n\'existe pas.'
                ], 404);
            }

            $this->authorize('update', $avancement);
            $result = Avancement::modifier($request, $id);

            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('AvancementsController@update a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requete non autorisee.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('AvancementsController@update a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

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
            $avancement = Avancement::find($id);

            if (!$avancement) {
                return response()->json([
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'avancement n\'existe pas.'
                ], 404);
            }

            $this->authorize('delete', $avancement);
            $result = Avancement::supprimer($id);

            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('AvancementsController@destroy a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requete non autorisee.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('AvancementsController@destroy a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue.'
            ], 500);
        }
    }
}
