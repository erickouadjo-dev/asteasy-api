<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RolePermission extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'TB_ROLE_PERMISSION';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'ROLE_ID',
        'PERMISSION_ID',
        'FONCTIONNALITE_ID',
        'IS_DELETE',
    ];

    protected $casts = [
        'ROLE_ID' => 'integer',
        'PERMISSION_ID' => 'integer',
        'FONCTIONNALITE_ID' => 'integer',
        'IS_DELETE' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    public static function lister(Request $request)
    {
        try {
            $per_page = $request->input('per_page', 15);
            $page = $request->input('page', 1);
            $search = $request->input('search', '');

            $query = self::where('IS_DELETE', false)
                ->whereNull('deleted_at');

            if (!empty($search)) {
                $query->where('ROLE_ID', 'like', '%' . $search . '%')
                    ->orWhere('PERMISSION_ID', 'like', '%' . $search . '%')
                    ->orWhere('FONCTIONNALITE_ID', 'like', '%' . $search . '%');
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
            Log::error('RolePermission::lister a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération des liaisons rôle-permission.'
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
                'ROLE_ID' => 'required|integer|exists:TB_ROLE,ID',
                'PERMISSION_ID' => 'required|integer|exists:TB_PERMISSION,ID',
                'FONCTIONNALITE_ID' => 'nullable|integer|exists:TB_FONCTIONNALITE,ID',
            ]);

            if (!$validator->passes()) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => $validator->errors()->all()];
            }

            $rolePermission = new self($inputs);
            $rolePermission->save();

            return ['code_http' => 201, 'code_message' => 201, 'data' => $rolePermission];
        } catch (\Exception $e) {
            Log::error('RolePermission::ajouter a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la création de la liaison rôle-permission.'
            ];
        }
    }

    public static function recuperer($id)
    {
        try {
            $rolePermission = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$rolePermission) {
                return ['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'La liaison rôle-permission n\'existe pas.'];
            }

            return ['code_http' => 200, 'code_message' => 200, 'data' => $rolePermission];
        } catch (\Exception $e) {
            Log::error('RolePermission::recuperer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération de la liaison rôle-permission.'
            ];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $rolePermission = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$rolePermission) {
                return ['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'La liaison rôle-permission n\'existe pas.'];
            }

            $inputs = json_decode($request->getContent(), true);

            if (!is_array($inputs)) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => 'Corps de la requête vide.'];
            }

            $validator = Validator::make($inputs, [
                'ROLE_ID' => 'nullable|integer|exists:TB_ROLE,ID',
                'PERMISSION_ID' => 'nullable|integer|exists:TB_PERMISSION,ID',
                'FONCTIONNALITE_ID' => 'nullable|integer|exists:TB_FONCTIONNALITE,ID',
            ]);

            if (!$validator->passes()) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => $validator->errors()->all()];
            }

            $rolePermission->update($inputs);

            return ['code_http' => 200, 'code_message' => 200, 'data' => $rolePermission];
        } catch (\Exception $e) {
            Log::error('RolePermission::modifier a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la modification de la liaison rôle-permission.'
            ];
        }
    }

    public static function supprimer($id)
    {
        try {
            $rolePermission = self::find($id);

            if (!$rolePermission) {
                return ['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'La liaison rôle-permission n\'existe pas.'];
            }

            $rolePermission->IS_DELETE = true;
            $rolePermission->save();
            $rolePermission->delete();

            return ['code_http' => 200, 'code_message' => 200, 'data' => $rolePermission];
        } catch (\Exception $e) {
            Log::error('RolePermission::supprimer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la suppression de la liaison rôle-permission.'
            ];
        }
    }
}