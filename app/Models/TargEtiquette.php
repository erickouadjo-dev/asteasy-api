<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TargEtiquette extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'TB_TARG_ETIQUETTE';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'TAG',
        'DESCRIPTION',
        'FAMILLE_ID',
        'IS_DELETE',
    ];

    protected $casts = [
        'FAMILLE_ID' => 'integer',
        'IS_DELETE' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    public function famille()
    {
        return $this->belongsTo(Famille::class, 'FAMILLE_ID', 'ID');
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
                    $q->where('TAG', 'like', '%' . $search . '%')
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
            Log::error('TargEtiquette::lister a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération des étiquettes.'
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
                'TAG'         => 'required|string|max:255|unique:TB_TARG_ETIQUETTE,TAG',
                'DESCRIPTION' => 'required|string',
                'FAMILLE_ID'  => 'nullable|integer|exists:TB_FAMILLE,ID',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $targEtiquette = new self($inputs);
            $targEtiquette->save();

            return [
                'code_http' => 201,
                'code_message' => 201,
                'data' => $targEtiquette
            ];
        } catch (\Exception $e) {
            Log::error('TargEtiquette::ajouter a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la création de l\'étiquette.'
            ];
        }
    }

    public static function recuperer($id)
    {
        try {
            $targEtiquette = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$targEtiquette) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'étiquette n\'existe pas.'
                ];
            }

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $targEtiquette
            ];
        } catch (\Exception $e) {
            Log::error('TargEtiquette::recuperer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération de l\'étiquette.'
            ];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $targEtiquette = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$targEtiquette) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'étiquette n\'existe pas.'
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
                'TAG'         => 'nullable|string|max:255|unique:TB_TARG_ETIQUETTE,TAG,' . $id . ',ID',
                'DESCRIPTION' => 'nullable|string',
                'FAMILLE_ID'  => 'nullable|integer|exists:TB_FAMILLE,ID',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $targEtiquette->update($inputs);

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $targEtiquette
            ];
        } catch (\Exception $e) {
            Log::error('TargEtiquette::modifier a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la modification de l\'étiquette.'
            ];
        }
    }

    public static function supprimer($id)
    {
        try {
            $targEtiquette = self::find($id);

            if (!$targEtiquette) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'étiquette n\'existe pas.'
                ];
            }

            $targEtiquette->IS_DELETE = true;
            $targEtiquette->save();
            $targEtiquette->delete();

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $targEtiquette
            ];
        } catch (\Exception $e) {
            Log::error('TargEtiquette::supprimer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la suppression de l\'étiquette.'
            ];
        }
    }
}
