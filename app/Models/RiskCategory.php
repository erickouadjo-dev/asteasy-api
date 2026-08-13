<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RiskCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'TB_RISK_CATEGORY';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'INTITULE',
        'DESCRIPTION',
        'IS_DELETE',
    ];

    protected $casts = [
        'IS_DELETE' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    public function subcategories()
    {
        return $this->hasMany(RiskSubcategory::class, 'ID_RISK_CATEGORY', 'ID');
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
                $query->where(function($q) use ($search) {
                    $q->where('INTITULE', 'like', '%' . $search . '%')
                      ->orWhere('DESCRIPTION', 'like', '%' . $search . '%');
                });
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
            Log::error('RiskCategory::lister a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération des catégories de risque.'
            ];
        }
    }

    public static function ajouter(Request $request)
    {
        try {
            $inputs = json_decode($request->getContent(), true);

            if (!is_array($inputs)) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => 'Corps de la requête vide.'
                ];
            }

            $validator = Validator::make($inputs, [
                'INTITULE'    => 'required|string|max:255|unique:TB_RISK_CATEGORY,INTITULE',
                'DESCRIPTION' => 'required|string',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $category = new self($inputs);
            $category->save();

            return [
                'code_http' => 201,
                'code_message' => 201,
                'data' => $category
            ];
        } catch (\Exception $e) {
            Log::error('RiskCategory::ajouter a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la création de la catégorie de risque.'
            ];
        }
    }

    public static function recuperer($id)
    {
        try {
            $category = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$category) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La catégorie de risque n\'existe pas.'
                ];
            }

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $category
            ];
        } catch (\Exception $e) {
            Log::error('RiskCategory::recuperer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération de la catégorie de risque.'
            ];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $category = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$category) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La catégorie de risque n\'existe pas.'
                ];
            }

            $inputs = json_decode($request->getContent(), true);

            if (!is_array($inputs)) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => 'Corps de la requête vide.'
                ];
            }

            $validator = Validator::make($inputs, [
                'INTITULE'    => 'nullable|string|max:255|unique:TB_RISK_CATEGORY,INTITULE,' . $id . ',ID',
                'DESCRIPTION' => 'nullable|string',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $category->update($inputs);

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $category
            ];
        } catch (\Exception $e) {
            Log::error('RiskCategory::modifier a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la modification de la catégorie de risque.'
            ];
        }
    }

    public static function supprimer($id)
    {
        try {
            $category = self::find($id);

            if (!$category) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La catégorie de risque n\'existe pas.'
                ];
            }

            $category->IS_DELETE = true;
            $category->save();
            $category->delete();

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $category
            ];
        } catch (\Exception $e) {
            Log::error('RiskCategory::supprimer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la suppression de la catégorie de risque.'
            ];
        }
    }
}
