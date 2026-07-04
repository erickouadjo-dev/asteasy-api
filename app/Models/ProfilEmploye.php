<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Traits\BelongsToTenant;

class ProfilEmploye extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'TB_PROFIL_EMPLOYE';
    protected $primaryKey = 'ID';
    protected $guarded = ['MODIFICATION'];
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'INTITULE',
        'DESCRIPTION',
        'MODIFICATION',
        'ENTREPRISE_ID'
    ];

    protected $casts = [
        'ENTREPRISE_ID' => 'integer',
        'MODIFICATION' => 'integer'
    ];

    protected $dates = ['deleted_at'];

    /**
     * Récupère la liste des profils avec pagination
     */
    public static function lister(Request $request)
    {
        try {
            $result = [
                'code_http' => 200,
                'code_message' => 200,
                'data' => [],
                'pagination' => []
            ];

            // Paramètres de pagination
            $per_page = $request->input('per_page', 15);
            $page = $request->input('page', 1);
            $search = $request->input('search', '');

            // Construction de la requête
            $query = self::query();

            // Recherche
            if (!empty($search)) {
                $query->where('INTITULE', 'like', '%' . $search . '%')
                    ->orWhere('DESCRIPTION', 'like', '%' . $search . '%');
            }

            // Pagination
            $paginated = $query->paginate($per_page, ['*'], 'page', $page);

            $result['data'] = $paginated->items();
            $result['pagination'] = [
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'from' => $paginated->firstItem(),
                'to' => $paginated->lastItem()
            ];

            return $result;
        } catch (\Exception $e) {
            Log::error('ProfilEmploye::lister a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération des profils.'
            ];
        }
    }

    /**
     * Ajoute un nouveau profil
     */
    public static function ajouter(Request $request)
    {
        try {
            $result = [
                'code_http' => 201,
                'code_message' => 201
            ];

            $inputs = json_decode($request->getContent(), true);

            if (!is_array($inputs)) {
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_VALIDATION';
                $result['erreurs'] = 'Corps de la requête vide.';
                return $result;
            }

            // Validation
            $rules = [
                'INTITULE' => 'required|string|max:200|unique:TB_PROFIL_EMPLOYE,INTITULE',
                'DESCRIPTION' => 'nullable|string|max:500',
            ];

            $validator = Validator::make($inputs, $rules);

            if (!$validator->passes()) {
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_VALIDATION';
                $result['erreurs'] = $validator->errors()->all();
                return $result;
            }

            // Création
            $profilEmploye = new self($inputs);
            $profilEmploye->save();

            $result['data'] = $profilEmploye;
            return $result;
        } catch (\Exception $e) {
            Log::error('ProfilEmploye::ajouter a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la création du profil.'
            ];
        }
    }

    /**
     * Récupère un profil spécifique
     */
    public static function recuperer($id)
    {
        try {
            $result = [
                'code_http' => 200,
                'code_message' => 200
            ];

            $profilEmploye = self::find($id);

            if (!$profilEmploye) {
                $result['code_http'] = 404;
                $result['code_message'] = 'ERR_NOT_FOUND';
                $result['erreurs'] = 'Le profil n\'existe pas.';
                return $result;
            }

            $result['data'] = $profilEmploye;
            return $result;
        } catch (\Exception $e) {
            Log::error('ProfilEmploye::recuperer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération du profil.'
            ];
        }
    }

    /**
     * Modifie un profil
     */
    public static function modifier(Request $request, $id)
    {
        try {
            $result = [
                'code_http' => 200,
                'code_message' => 200
            ];

            $profilEmploye = self::find($id);

            if (!$profilEmploye) {
                $result['code_http'] = 404;
                $result['code_message'] = 'ERR_NOT_FOUND';
                $result['erreurs'] = 'Le profil n\'existe pas.';
                return $result;
            }

            $inputs = json_decode($request->getContent(), true);

            if (!is_array($inputs)) {
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_VALIDATION';
                $result['erreurs'] = 'Corps de la requête vide.';
                return $result;
            }

            // Validation
            $rules = [
                'INTITULE' => 'nullable|string|max:200|unique:TB_PROFIL_EMPLOYE,INTITULE,' . $id . ',ID',
                'DESCRIPTION' => 'nullable|string|max:500',
                'MODIFICATION' => 'nullable|integer'
            ];

            $validator = Validator::make($inputs, $rules);

            if (!$validator->passes()) {
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_VALIDATION';
                $result['erreurs'] = $validator->errors()->all();
                return $result;
            }

            // Modification
            $profilEmploye->update($inputs);

            $result['data'] = $profilEmploye;
            return $result;
        } catch (\Exception $e) {
            Log::error('ProfilEmploye::modifier a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la modification du profil.'
            ];
        }
    }

    /**
     * Supprime un profil
     */
    public static function supprimer($id)
    {
        try {
            $result = [
                'code_http' => 204,
                'code_message' => 204
            ];

            $profilEmploye = self::find($id);

            if (!$profilEmploye) {
                $result['code_http'] = 404;
                $result['code_message'] = 'ERR_NOT_FOUND';
                $result['erreurs'] = 'Le profil n\'existe pas.';
                return $result;
            }

            $profilEmploye->delete();

            return $result;
        } catch (\Exception $e) {
            Log::error('ProfilEmploye::supprimer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la suppression du profil.'
            ];
        }
    }
}
