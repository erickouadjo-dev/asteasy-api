<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Base;
use App\Utility\PolicyResources\Bases as BasesResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BasesController extends Controller
{
    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', BasesResource::class);
            $result = Base::lister($request);

            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('BasesController@index a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requete non autorisee.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('BasesController@index a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

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
            $this->authorize('create', BasesResource::class);
            $result = Base::ajouter($request);

            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('BasesController@store a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requete non autorisee.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('BasesController@store a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

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
            $base = Base::find($id);

            if (!$base) {
                return response()->json([
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La base n\'existe pas.'
                ], 404);
            }

            $this->authorize('view', $base);
            $result = Base::recuperer($id);

            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('BasesController@show a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requete non autorisee.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('BasesController@show a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

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
            $base = Base::find($id);

            if (!$base) {
                return response()->json([
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La base n\'existe pas.'
                ], 404);
            }

            $this->authorize('update', $base);
            $result = Base::modifier($request, $id);

            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('BasesController@update a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requete non autorisee.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('BasesController@update a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

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
            $base = Base::find($id);

            if (!$base) {
                return response()->json([
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La base n\'existe pas.'
                ], 404);
            }

            $this->authorize('delete', $base);
            $result = Base::supprimer($id);

            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('BasesController@destroy a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requete non autorisee.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('BasesController@destroy a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue.'
            ], 500);
        }
    }
}
