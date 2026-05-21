<?php

namespace App\Http\Controllers\Api\V1\Utilisateurs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Utility\PolicyResources\Utilisateurs\Deconnecter as UtilisateursDeconnecterResource;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Log;

class DeconnecterController extends Controller
{
    public function store(Request $request)
    {
        try {
            $this->authorize('create', UtilisateursDeconnecterResource::class);
            $result = Utilisateur::deconnecter($request);
            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('DeconnecterController@store a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('DeconnecterController@store a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue.'], 500);
        }
    }
}
