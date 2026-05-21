<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PlanModule extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'TB_PLAN_MODULE';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'PLAN_ID',
        'MODULE_ID',
        'IS_DELETE',
    ];

    protected $casts = [
        'PLAN_ID' => 'integer',
        'MODULE_ID' => 'integer',
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
                $query->where('PLAN_ID', 'like', '%' . $search . '%')
                    ->orWhere('MODULE_ID', 'like', '%' . $search . '%');
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
            Log::error('PlanModule::lister a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération des liaisons plan-module.'
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
                'PLAN_ID' => 'required|integer|exists:TB_PLAN,ID',
                'MODULE_ID' => 'required|integer|exists:TB_MODULE,ID',
            ]);

            if (!$validator->passes()) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => $validator->errors()->all()];
            }

            $planModule = new self($inputs);
            $planModule->save();

            return ['code_http' => 201, 'code_message' => 201, 'data' => $planModule];
        } catch (\Exception $e) {
            Log::error('PlanModule::ajouter a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la création de la liaison plan-module.'
            ];
        }
    }

    public static function recuperer($id)
    {
        try {
            $planModule = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$planModule) {
                return ['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'La liaison plan-module n\'existe pas.'];
            }

            return ['code_http' => 200, 'code_message' => 200, 'data' => $planModule];
        } catch (\Exception $e) {
            Log::error('PlanModule::recuperer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération de la liaison plan-module.'
            ];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $planModule = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$planModule) {
                return ['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'La liaison plan-module n\'existe pas.'];
            }

            $inputs = json_decode($request->getContent(), true);

            if (!is_array($inputs)) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => 'Corps de la requête vide.'];
            }

            $validator = Validator::make($inputs, [
                'PLAN_ID' => 'nullable|integer|exists:TB_PLAN,ID',
                'MODULE_ID' => 'nullable|integer|exists:TB_MODULE,ID',
            ]);

            if (!$validator->passes()) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => $validator->errors()->all()];
            }

            $planModule->update($inputs);

            return ['code_http' => 200, 'code_message' => 200, 'data' => $planModule];
        } catch (\Exception $e) {
            Log::error('PlanModule::modifier a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la modification de la liaison plan-module.'
            ];
        }
    }

    public static function supprimer($id)
    {
        try {
            $planModule = self::find($id);

            if (!$planModule) {
                return ['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'La liaison plan-module n\'existe pas.'];
            }

            $planModule->IS_DELETE = true;
            $planModule->save();
            $planModule->delete();

            return ['code_http' => 200, 'code_message' => 200, 'data' => $planModule];
        } catch (\Exception $e) {
            Log::error('PlanModule::supprimer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la suppression de la liaison plan-module.'
            ];
        }
    }
}
