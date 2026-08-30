<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Traits\BelongsToTenant;

class Aeronef extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'TB_AERONEFS';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'MARQUE',
        'TYPE_MODELE',
        'IMMATRICULATION',
        'SN',
        'DATE_MISE_EN_SERVICE',
        'DOCUMENT_ID',
        'ENTREPRISE_ID',
        'IS_DELETE',
    ];

    protected $casts = [
        'ENTREPRISE_ID' => 'integer',
        'IS_DELETE' => 'boolean',
        'DATE_MISE_EN_SERVICE' => 'date:Y-m-d',
    ];

    protected $dates = ['deleted_at', 'DATE_MISE_EN_SERVICE'];

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
                    $q->where('MARQUE', 'like', '%' . $search . '%')
                      ->orWhere('TYPE_MODELE', 'like', '%' . $search . '%')
                      ->orWhere('IMMATRICULATION', 'like', '%' . $search . '%')
                      ->orWhere('SN', 'like', '%' . $search . '%');
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
            Log::error('Aeronef::lister a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération des aéronefs.'
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
                'MARQUE'               => 'required|string|max:250',
                'TYPE_MODELE'          => 'required|string|max:500',
                'IMMATRICULATION'      => 'required|string|max:500',
                'SN'                   => 'nullable|string|max:500',
                'DATE_MISE_EN_SERVICE' => 'nullable|date_format:Y-m-d',
                'DOCUMENT_ID'          => 'nullable|integer|exists:TB_DOCUMENTS,ID',
                'ENTREPRISE_ID'        => 'nullable|integer|exists:TB_ENTREPRISE,ID',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $aeronef = new self($inputs);
            $aeronef->save();

            return [
                'code_http' => 201,
                'code_message' => 201,
                'data' => $aeronef
            ];
        } catch (\Exception $e) {
            Log::error('Aeronef::ajouter a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la création de l\'aéronef.'
            ];
        }
    }

    public static function recuperer($id)
    {
        try {
            $aeronef = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$aeronef) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'aéronef n\'existe pas.'
                ];
            }

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $aeronef
            ];
        } catch (\Exception $e) {
            Log::error('Aeronef::recuperer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération de l\'aéronef.'
            ];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $aeronef = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$aeronef) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'aéronef n\'existe pas.'
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
                'MARQUE'               => 'nullable|string|max:250',
                'TYPE_MODELE'          => 'nullable|string|max:500',
                'IMMATRICULATION'      => 'nullable|string|max:500',
                'SN'                   => 'nullable|string|max:500',
                'DATE_MISE_EN_SERVICE' => 'nullable|date_format:Y-m-d',
                'DOCUMENT_ID'          => 'nullable|integer|exists:TB_DOCUMENTS,ID',
                'ENTREPRISE_ID'        => 'nullable|integer|exists:TB_ENTREPRISE,ID',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $aeronef->update($inputs);

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $aeronef
            ];
        } catch (\Exception $e) {
            Log::error('Aeronef::modifier a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la modification de l\'aéronef.'
            ];
        }
    }

    public static function supprimer($id)
    {
        try {
            $aeronef = self::find($id);

            if (!$aeronef) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'aéronef n\'existe pas.'
                ];
            }

            $aeronef->IS_DELETE = true;
            $aeronef->save();
            $aeronef->delete();

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $aeronef
            ];
        } catch (\Exception $e) {
            Log::error('Aeronef::supprimer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la suppression de l\'aéronef.'
            ];
        }
    }
}
