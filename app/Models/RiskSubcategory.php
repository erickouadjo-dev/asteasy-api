<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RiskSubcategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'TB_RISK_SUBCATEGORY';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'INTITULE',
        'DESCRIPTION',
        'ID_RISK_CATEGORY',
        'IS_DELETE',
    ];

    protected $casts = [
        'IS_DELETE' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    public function category()
    {
        return $this->belongsTo(RiskCategory::class, 'ID_RISK_CATEGORY', 'ID');
    }

    public static function lister(Request $request)
    {
        try {
            $per_page = $request->input('per_page', 15);
            $page     = $request->input('page', 1);
            $search   = $request->input('search', '');

            $query = self::where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->with('category');

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
            Log::error('RiskSubcategory::lister a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération des sous-catégories de risque.'
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
                'INTITULE'         => 'required|string|max:255|unique:TB_RISK_SUBCATEGORY,INTITULE',
                'DESCRIPTION'      => 'required|string',
                'ID_RISK_CATEGORY' => 'required|integer|exists:TB_RISK_CATEGORY,ID',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $subcategory = new self($inputs);
            $subcategory->save();

            // Load the category relation for the response
            $subcategory->load('category');

            return [
                'code_http' => 201,
                'code_message' => 201,
                'data' => $subcategory
            ];
        } catch (\Exception $e) {
            Log::error('RiskSubcategory::ajouter a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la création de la sous-catégorie de risque.'
            ];
        }
    }

    public static function recuperer($id)
    {
        try {
            $subcategory = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->with('category')
                ->first();

            if (!$subcategory) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La sous-catégorie de risque n\'existe pas.'
                ];
            }

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $subcategory
            ];
        } catch (\Exception $e) {
            Log::error('RiskSubcategory::recuperer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération de la sous-catégorie de risque.'
            ];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $subcategory = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$subcategory) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La sous-catégorie de risque n\'existe pas.'
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
                'INTITULE'         => 'nullable|string|max:255|unique:TB_RISK_SUBCATEGORY,INTITULE,' . $id . ',ID',
                'DESCRIPTION'      => 'nullable|string',
                'ID_RISK_CATEGORY' => 'nullable|integer|exists:TB_RISK_CATEGORY,ID',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $subcategory->update($inputs);
            $subcategory->load('category');

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $subcategory
            ];
        } catch (\Exception $e) {
            Log::error('RiskSubcategory::modifier a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la modification de la sous-catégorie de risque.'
            ];
        }
    }

    public static function supprimer($id)
    {
        try {
            $subcategory = self::find($id);

            if (!$subcategory) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La sous-catégorie de risque n\'existe pas.'
                ];
            }

            $subcategory->IS_DELETE = true;
            $subcategory->save();
            $subcategory->delete();

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $subcategory
            ];
        } catch (\Exception $e) {
            Log::error('RiskSubcategory::supprimer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la suppression de la sous-catégorie de risque.'
            ];
        }
    }
}
