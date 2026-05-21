<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class Plan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'TB_PLAN';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'LIBELLE',
        'DESCRIPTION',
        'PRIX',
        'DUREE',
        'LIMITE_UTILISATEURS',
        'IS_DELETE',
    ];

    protected $casts = [
        'PRIX' => 'decimal:2',
        'LIMITE_UTILISATEURS' => 'integer',
        'IS_DELETE' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    public function modules()
    {
        return $this->hasMany(PlanModule::class, 'PLAN_ID', 'ID')
            ->where('IS_DELETE', false)
            ->whereNull('deleted_at');
    }

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
            $page     = $request->input('page', 1);
            $search   = $request->input('search', '');

            $query = self::where('IS_DELETE', false)
                ->whereNull('deleted_at');

            if (!empty($search)) {
                $query->where('LIBELLE', 'like', '%' . $search . '%')
                      ->orWhere('DESCRIPTION', 'like', '%' . $search . '%')
                      ->orWhere('DUREE', 'like', '%' . $search . '%');
            }

            $paginated = $query->paginate($per_page, ['*'], 'page', $page);

            $result['data']       = $paginated->items();
            $result['pagination'] = [
                'total'        => $paginated->total(),
                'per_page'     => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'from'         => $paginated->firstItem(),
                'to'           => $paginated->lastItem()
            ];

            return $result;
        } catch (\Exception $e) {
            Log::error('Plan::lister a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http'    => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs'      => 'Une erreur est survenue lors de la récupération des plans.'
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
                'LIBELLE'            => 'nullable|string|max:50|unique:TB_PLAN,LIBELLE',
                'DESCRIPTION'        => 'nullable|string',
                'PRIX'               => 'required|numeric|min:0',
                'DUREE'              => 'nullable|string',
                'LIMITE_UTILISATEURS'=> 'required|integer|min:1',
                'MODULE_IDS'         => 'nullable|array',
                'MODULE_IDS.*'       => 'integer|exists:TB_MODULE,ID',
            ];

            $validator = Validator::make($inputs, $rules);

            if (!$validator->passes()) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => $validator->errors()->all()];
            }

            $plan = new self($inputs);
            $plan->save();

            if (!empty($inputs['MODULE_IDS'])) {
                foreach ($inputs['MODULE_IDS'] as $moduleId) {
                    $planModule = new PlanModule(['PLAN_ID' => $plan->ID, 'MODULE_ID' => $moduleId]);
                    $planModule->save();
                }
            }

            $result['data'] = $plan->load('modules');
            return $result;
        } catch (\Exception $e) {
            Log::error('Plan::ajouter a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return ['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue lors de la création du plan.'];
        }
    }

    public static function recuperer($id)
    {
        try {
            $result = ['code_http' => 200, 'code_message' => 200];

            $plan = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$plan) {
                return ['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'Le plan n\'existe pas.'];
            }

            $plan->load('modules');
            $result['data'] = $plan;
            return $result;
        } catch (\Exception $e) {
            Log::error('Plan::recuperer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return ['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue lors de la récupération du plan.'];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $result = ['code_http' => 200, 'code_message' => 200];

            $plan = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$plan) {
                return ['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'Le plan n\'existe pas.'];
            }

            $inputs = json_decode($request->getContent(), true);

            if (!is_array($inputs)) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => 'Corps de la requête vide.'];
            }

            $rules = [
                'LIBELLE'            => 'nullable|string|max:50|unique:TB_PLAN,LIBELLE,' . $id . ',ID',
                'DESCRIPTION'        => 'nullable|string',
                'PRIX'               => 'nullable|numeric|min:0',
                'DUREE'              => 'nullable|string',
                'LIMITE_UTILISATEURS'=> 'nullable|integer|min:1',
                'MODULE_IDS'         => 'nullable|array',
                'MODULE_IDS.*'       => 'integer|exists:TB_MODULE,ID',
            ];

            $validator = Validator::make($inputs, $rules);

            if (!$validator->passes()) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => $validator->errors()->all()];
            }

            $plan->update($inputs);

            if (array_key_exists('MODULE_IDS', $inputs)) {
                // Soft-delete existing modules then recreate
                PlanModule::where('PLAN_ID', $id)
                    ->where('IS_DELETE', false)
                    ->whereNull('deleted_at')
                    ->update(['IS_DELETE' => true, 'deleted_at' => now()]);

                foreach ($inputs['MODULE_IDS'] as $moduleId) {
                    $planModule = new PlanModule(['PLAN_ID' => $id, 'MODULE_ID' => $moduleId]);
                    $planModule->save();
                }
            }

            $result['data'] = $plan->load('modules');
            return $result;
        } catch (\Exception $e) {
            Log::error('Plan::modifier a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return ['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue lors de la modification du plan.'];
        }
    }

    public static function supprimer($id)
    {
        try {
            $result = ['code_http' => 200, 'code_message' => 200];

            $plan = self::find($id);

            if (!$plan) {
                return ['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'Le plan n\'existe pas.'];
            }

            $plan->IS_DELETE = true;
            $plan->save();
            $plan->delete();
            $result['data'] = $plan;
            return $result;
        } catch (\Exception $e) {
            Log::error('Plan::supprimer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return ['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue lors de la suppression du plan.'];
        }
    }
}
