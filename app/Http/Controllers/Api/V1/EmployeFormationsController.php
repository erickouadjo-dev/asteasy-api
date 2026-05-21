<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\EmployeFormation;
use App\Utility\PolicyResources\EmployeFormations as EmployeFormationsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmployeFormationsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', EmployeFormationsResource::class);
            $result = EmployeFormation::lister($request);

            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('EmployeFormationsController@index a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requete non autorisee.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('EmployeFormationsController@index a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

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
            $this->authorize('create', EmployeFormationsResource::class);
            $result = EmployeFormation::ajouter($request);

            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('EmployeFormationsController@store a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requete non autorisee.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('EmployeFormationsController@store a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

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
            $employeFormation = EmployeFormation::find($id);

            if (!$employeFormation) {
                return response()->json([
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La formation de l\'employe n\'existe pas.'
                ], 404);
            }

            $this->authorize('view', $employeFormation);
            $result = EmployeFormation::recuperer($id);

            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('EmployeFormationsController@show a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requete non autorisee.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('EmployeFormationsController@show a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

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
            $employeFormation = EmployeFormation::find($id);

            if (!$employeFormation) {
                return response()->json([
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La formation de l\'employe n\'existe pas.'
                ], 404);
            }

            $this->authorize('update', $employeFormation);
            $result = EmployeFormation::modifier($request, $id);

            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('EmployeFormationsController@update a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requete non autorisee.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('EmployeFormationsController@update a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

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
            $employeFormation = EmployeFormation::find($id);

            if (!$employeFormation) {
                return response()->json([
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La formation de l\'employe n\'existe pas.'
                ], 404);
            }

            $this->authorize('delete', $employeFormation);
            $result = EmployeFormation::supprimer($id);

            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('EmployeFormationsController@destroy a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'http_code' => 403,
                'code' => 403,
                'code_message' => 'Requete non autorisee.'
            ], 403);
        } catch (\Exception $e) {
            Log::error('EmployeFormationsController@destroy a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue.'
            ], 500);
        }
    }
}
