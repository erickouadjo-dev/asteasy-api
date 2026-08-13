<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MatriceRisque extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'TB_MATRICE_RISQUE';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'CODE',
        'COULEUR_CODE',
        'IS_DELETE',
    ];

    protected $casts = [
        'IS_DELETE' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

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
                    $q->where('CODE', 'like', '%' . $search . '%')
                      ->orWhere('COULEUR_CODE', 'like', '%' . $search . '%');
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
            Log::error('MatriceRisque::lister a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération des matrices de risque.'
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
                'CODE'         => 'required|string|max:255|unique:TB_MATRICE_RISQUE,CODE',
                'COULEUR_CODE' => 'required|string|max:255',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $matriceRisque = new self($inputs);
            $matriceRisque->save();

            return [
                'code_http' => 201,
                'code_message' => 201,
                'data' => $matriceRisque
            ];
        } catch (\Exception $e) {
            Log::error('MatriceRisque::ajouter a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la création de la matrice de risque.'
            ];
        }
    }

    public static function recuperer($id)
    {
        try {
            $matriceRisque = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$matriceRisque) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La matrice de risque n\'existe pas.'
                ];
            }

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $matriceRisque
            ];
        } catch (\Exception $e) {
            Log::error('MatriceRisque::recuperer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération de la matrice de risque.'
            ];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $matriceRisque = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$matriceRisque) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La matrice de risque n\'existe pas.'
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
                'CODE'         => 'nullable|string|max:255|unique:TB_MATRICE_RISQUE,CODE,' . $id . ',ID',
                'COULEUR_CODE' => 'nullable|string|max:255',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $matriceRisque->update($inputs);

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $matriceRisque
            ];
        } catch (\Exception $e) {
            Log::error('MatriceRisque::modifier a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la modification de la matrice de risque.'
            ];
        }
    }

    public static function supprimer($id)
    {
        try {
            $matriceRisque = self::find($id);

            if (!$matriceRisque) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La matrice de risque n\'existe pas.'
                ];
            }

            $matriceRisque->IS_DELETE = true;
            $matriceRisque->save();
            $matriceRisque->delete();

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $matriceRisque
            ];
        } catch (\Exception $e) {
            Log::error('MatriceRisque::supprimer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la suppression de la matrice de risque.'
            ];
        }
    }
}
