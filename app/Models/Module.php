<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class Module extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'TB_MODULE';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'LIBELLE',
        'DESCRIPTION',
        'LIEN',
        'IS_DELETE',
    ];

    protected $casts = [
        'IS_DELETE' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    public static function lister(Request $request)
    {
        try {
            $result = [
                'code_http' => 200,
                'code_message' => 200,
                'data' => [],
                'pagination' => []
            ];

            $per_page = $request->input('per_page', 15);
            $page = $request->input('page', 1);
            $search = $request->input('search', '');

            $query = self::where('IS_DELETE', false)
                ->whereNull('deleted_at');

            if (!empty($search)) {
                $query->where('LIBELLE', 'like', '%' . $search . '%')
                    ->orWhere('DESCRIPTION', 'like', '%' . $search . '%');
            }

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
            Log::error('Module::lister a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération des modules.'
            ];
        }
    }

    public static function ajouter(Request $request)
    {
        try {
            $result = ['code_http' => 201, 'code_message' => 201];

            $inputs = json_decode($request->getContent(), true);

            if (!is_array($inputs)) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => 'Corps de la requête vide.'];
            }

            $rules = [
                'LIBELLE' => 'nullable|string|max:50|unique:TB_MODULE,LIBELLE',
                'DESCRIPTION' => 'nullable|string',
                'LIEN' => 'nullable|string',
            ];

            $validator = Validator::make($inputs, $rules);

            if (!$validator->passes()) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => $validator->errors()->all()];
            }

            $module = new self($inputs);
            $module->save();

            $result['data'] = $module;
            return $result;
        } catch (\Exception $e) {
            Log::error('Module::ajouter a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la création du module.'
            ];
        }
    }

    public static function recuperer($id)
    {
        try {
            $module = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$module) {
                return ['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'Le module n\'existe pas.'];
            }

            return ['code_http' => 200, 'code_message' => 200, 'data' => $module];
        } catch (\Exception $e) {
            Log::error('Module::recuperer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération du module.'
            ];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $module = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$module) {
                return ['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'Le module n\'existe pas.'];
            }

            $inputs = json_decode($request->getContent(), true);

            if (!is_array($inputs)) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => 'Corps de la requête vide.'];
            }

            $rules = [
                'LIBELLE' => 'nullable|string|max:50|unique:TB_MODULE,LIBELLE,' . $id . ',ID',
                'DESCRIPTION' => 'nullable|string',
                'LIEN' => 'nullable|string',
            ];

            $validator = Validator::make($inputs, $rules);

            if (!$validator->passes()) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => $validator->errors()->all()];
            }

            $module->update($inputs);

            return ['code_http' => 200, 'code_message' => 200, 'data' => $module];
        } catch (\Exception $e) {
            Log::error('Module::modifier a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la modification du module.'
            ];
        }
    }

    public static function supprimer($id)
    {
        try {
            $module = self::find($id);

            if (!$module) {
                return ['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'Le module n\'existe pas.'];
            }

            $module->IS_DELETE = true;
            $module->save();
            $module->delete();

            return ['code_http' => 200, 'code_message' => 200, 'data' => $module];
        } catch (\Exception $e) {
            Log::error('Module::supprimer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la suppression du module.'
            ];
        }
    }
}