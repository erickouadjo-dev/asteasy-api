<?php

namespace App\Http\Controllers\Api\V1\Utilisateurs\Utilisateur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utilisateur as UtilisateurResource;
use Illuminate\Support\Facades\Log;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Validator;
use App\Utility\PolicyResources\Utilisateurs\ReinitialiserMotDePasse as UtilisateursReinitialiserMotDePasseResource;

class MotDePasseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    try{
            // instancier un élément utilisateur avec id en paramètre
            $utilisateur = Utilisateur::findOrFail($id);
            $this->authorize('update', $utilisateur);
            $result = $utilisateur->modifierMotDePasse($request);

            return response()->json($result, $result['code_http']);

        }catch(\Illuminate\Auth\Access\AuthorizationException $e){
            
            Log::error('Utilisateurs\Utilisateur\MotDePasseController@update a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
        }catch(\Exception $e){

            Log::error('Utilisateurs\Utilisateur\MotDePasseController@update a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return response()->json([
                'http_code' => 500,
                'code' => 500,
                'code_message' => 'Une erreur est survenue.'
            ], 500);
        }
      
    
    }

    /**
     * Finaliser la creation du mot de passe via lien email (sans session active).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function finaliser(Request $request, $id)
    {
        try {
            // Endpoint public: autorise uniquement si utilisateur non connecte.
            $this->authorize('create', UtilisateursReinitialiserMotDePasseResource::class);

            $inputs = $request->all();

            if (!is_array($inputs) || empty($inputs)) {
                $inputs = json_decode($request->getContent(), true);
            }

            if (!is_array($inputs)) {
                return response()->json([
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => 'Corps de la requête vide.',
                ], 400);
            }

            if (!array_key_exists('mot_de_passe', $inputs) && array_key_exists('password', $inputs)) {
                $inputs['mot_de_passe'] = $inputs['password'];
            }

            if (!array_key_exists('mot_de_passe_confirmation', $inputs) && array_key_exists('password_confirmation', $inputs)) {
                $inputs['mot_de_passe_confirmation'] = $inputs['password_confirmation'];
            }

            if (!array_key_exists('token', $inputs) || empty($inputs['token'])) {
                $bearer_token = $request->bearerToken();

                if (!empty($bearer_token)) {
                    $inputs['token'] = $bearer_token;
                } elseif ($request->query('token')) {
                    $inputs['token'] = $request->query('token');
                }
            }

            $validator = Validator::make((array) $inputs, [
                'token' => 'required|string',
                'mot_de_passe' => 'required|string|min:8|confirmed',
                'photo' => 'sometimes|string',
            ]);

            if (!$validator->passes()) {
                Log::warning('Utilisateurs\Utilisateur\MotDePasseController@finaliser - validation echouee.', ['erreurs' => $validator->errors()->all()]);
                return response()->json([
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all(),
                ], 400);
            }

            // Authentifie le token du lien email sans exiger une session connectee.
            $request->merge($inputs);
            $request->headers->set('Authorization', 'Bearer ' . $inputs['token']);
            $utilisateur_auth = auth('api')->user();

            if (is_null($utilisateur_auth)) {
                return response()->json([
                    'code_http' => 401,
                    'code_message' => 'ERR_UNAUTHORIZED',
                    'erreurs' => 'Token invalide ou expire.'
                ], 401);
            }

            if ((int) $utilisateur_auth->id !== (int) $id) {
                return response()->json([
                    'http_code' => 403,
                    'code' => 403,
                    'code_message' => 'Requête non autorisée.'
                ], 403);
            }

            $utilisateur = Utilisateur::findOrFail($id);
            $result = $utilisateur->modifierMotDePasse($request);

            return response()->json($result, $result['code_http']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('Utilisateurs\Utilisateur\MotDePasseController@finaliser a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['http_code' => 403, 'code' => 403, 'code_message' => 'Requête non autorisée.'], 403);
        } catch (\Exception $e) {
            Log::error('Utilisateurs\Utilisateur\MotDePasseController@finaliser a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'http_code' => 500,
                'code' => 500,
                'code_message' => 'Une erreur est survenue.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}