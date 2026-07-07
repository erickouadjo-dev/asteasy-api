<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UtilisateurRole extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'TB_UTILISATEUR_ROLE';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'UTILISATEUR_ID',
        'ROLE_ID',
        'IS_DELETE',
    ];

    protected $casts = [
        'UTILISATEUR_ID' => 'integer',
        'ROLE_ID' => 'integer',
        'IS_DELETE' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'UTILISATEUR_ID', 'id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'ROLE_ID', 'ID');
    }

    public static function lister(Request $request)
    {
        try {
            $per_page = $request->input('per_page', 15);
            $page = $request->input('page', 1);
            $search = $request->input('search', '');

            $query = self::where('IS_DELETE', false)
                ->whereNull('deleted_at');

            if (!empty($search)) {
                $query->where('UTILISATEUR_ID', 'like', '%' . $search . '%')
                    ->orWhere('ROLE_ID', 'like', '%' . $search . '%');
            }

            $paginated = $query->paginate($per_page, ['*'], 'page', $page);

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $paginated->items(),
                'pagination' => [
                    'total' => $paginated->total(),
                    'per_page' => $paginated->perPage(),
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                    'from' => $paginated->firstItem(),
                    'to' => $paginated->lastItem()
                ]
            ];
        } catch (\Exception $e) {
            Log::error('UtilisateurRole::lister a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération des liaisons utilisateur-rôle.'
            ];
        }
    }

    public static function ajouter(Request $request)
    {
        try {
            $inputs = json_decode($request->getContent(), true);

            if (!is_array($inputs)) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => 'Corps de la requête vide.'];
            }

            $validator = Validator::make($inputs, [
                'UTILISATEUR_ID' => 'required|integer|exists:utilisateurs,id',
                'ROLE_ID' => 'required|integer|exists:TB_ROLE,ID',
            ]);

            if (!$validator->passes()) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => $validator->errors()->all()];
            }

            // Vérifier si la liaison existe déjà et n'est pas supprimée
            $exists = self::where('UTILISATEUR_ID', $inputs['UTILISATEUR_ID'])
                ->where('ROLE_ID', $inputs['ROLE_ID'])
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if ($exists) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => ['Cette association utilisateur-rôle existe déjà.']
                ];
            }

            $utilisateurRole = new self($inputs);
            $utilisateurRole->save();

            return ['code_http' => 201, 'code_message' => 201, 'data' => $utilisateurRole];
        } catch (\Exception $e) {
            Log::error('UtilisateurRole::ajouter a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la création de la liaison utilisateur-rôle.'
            ];
        }
    }

    public static function recuperer($id)
    {
        try {
            $utilisateurRole = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$utilisateurRole) {
                return ['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'La liaison utilisateur-rôle n\'existe pas.'];
            }

            return ['code_http' => 200, 'code_message' => 200, 'data' => $utilisateurRole];
        } catch (\Exception $e) {
            Log::error('UtilisateurRole::recuperer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération de la liaison utilisateur-rôle.'
            ];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $utilisateurRole = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$utilisateurRole) {
                return ['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'La liaison utilisateur-rôle n\'existe pas.'];
            }

            $inputs = json_decode($request->getContent(), true);

            if (!is_array($inputs)) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => 'Corps de la requête vide.'];
            }

            $validator = Validator::make($inputs, [
                'UTILISATEUR_ID' => 'nullable|integer|exists:utilisateurs,id',
                'ROLE_ID' => 'nullable|integer|exists:TB_ROLE,ID',
            ]);

            if (!$validator->passes()) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => $validator->errors()->all()];
            }

            $utilisateurRole->update($inputs);

            return ['code_http' => 200, 'code_message' => 200, 'data' => $utilisateurRole];
        } catch (\Exception $e) {
            Log::error('UtilisateurRole::modifier a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la modification de la liaison utilisateur-rôle.'
            ];
        }
    }

    public static function supprimer($id)
    {
        try {
            $utilisateurRole = self::find($id);

            if (!$utilisateurRole) {
                return ['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'La liaison utilisateur-rôle n\'existe pas.'];
            }

            $utilisateurRole->IS_DELETE = true;
            $utilisateurRole->save();
            $utilisateurRole->delete();

            return ['code_http' => 200, 'code_message' => 200, 'data' => $utilisateurRole];
        } catch (\Exception $e) {
            Log::error('UtilisateurRole::supprimer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la suppression de la liaison utilisateur-rôle.'
            ];
        }
    }
}
