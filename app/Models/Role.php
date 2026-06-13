<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Permission;
use App\Models\Utilisateur;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'TB_ROLE';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'LIBELLE',
        'IS_DELETE',
    ];

    protected $casts = [
        'IS_DELETE' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'TB_ROLE_PERMISSION', 'ROLE_ID', 'PERMISSION_ID');
    }

    public function utilisateurs()
    {
        return $this->belongsToMany(Utilisateur::class, 'TB_UTILISATEUR_ROLE', 'ROLE_ID', 'UTILISATEUR_ID');
    }

    public static function lister(Request $request)
    {
        try {
            $per_page = $request->input('per_page', 15);
            $page     = $request->input('page', 1);
            $search   = $request->input('search', '');

            $query = self::where('IS_DELETE', false)
                ->whereNull('deleted_at');

            if (!empty($search)) {
                $query->where('LIBELLE', 'like', '%' . $search . '%');
            }

            $paginated = $query->paginate($per_page, ['*'], 'page', $page);

            return [
                'code_http'    => 200,
                'code_message' => 200,
                'data'         => $paginated->items(),
                'pagination'   => [
                    'total'        => $paginated->total(),
                    'per_page'     => $paginated->perPage(),
                    'current_page' => $paginated->currentPage(),
                    'last_page'    => $paginated->lastPage(),
                    'from'         => $paginated->firstItem(),
                    'to'           => $paginated->lastItem()
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Role::lister a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return ['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue lors de la récupération des rôles.'];
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
                'LIBELLE' => 'nullable|string|max:50|unique:TB_ROLE,LIBELLE',
            ]);

            if (!$validator->passes()) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => $validator->errors()->all()];
            }

            $role = new self($inputs);
            $role->save();

            return ['code_http' => 201, 'code_message' => 201, 'data' => $role];
        } catch (\Exception $e) {
            Log::error('Role::ajouter a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return ['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue lors de la création du rôle.'];
        }
    }

    public static function recuperer($id)
    {
        try {
            $role = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$role) {
                return ['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'Le rôle n\'existe pas.'];
            }

            return ['code_http' => 200, 'code_message' => 200, 'data' => $role];
        } catch (\Exception $e) {
            Log::error('Role::recuperer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return ['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue lors de la récupération du rôle.'];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $role = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$role) {
                return ['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'Le rôle n\'existe pas.'];
            }

            $inputs = json_decode($request->getContent(), true);

            if (!is_array($inputs)) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => 'Corps de la requête vide.'];
            }

            $validator = Validator::make($inputs, [
                'LIBELLE' => 'nullable|string|max:50|unique:TB_ROLE,LIBELLE,' . $id . ',ID',
            ]);

            if (!$validator->passes()) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => $validator->errors()->all()];
            }

            $role->update($inputs);

            return ['code_http' => 200, 'code_message' => 200, 'data' => $role];
        } catch (\Exception $e) {
            Log::error('Role::modifier a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return ['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue lors de la modification du rôle.'];
        }
    }

    public static function supprimer($id)
    {
        try {
            $role = self::find($id);

            if (!$role) {
                return ['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'Le rôle n\'existe pas.'];
            }

            $role->IS_DELETE = true;
            $role->save();
            $role->delete();
            return ['code_http' => 200, 'code_message' => 200, 'data' => $role];
        } catch (\Exception $e) {
            Log::error('Role::supprimer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return ['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue lors de la suppression du rôle.'];
        }
    }
}
