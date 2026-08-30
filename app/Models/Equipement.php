<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Traits\BelongsToTenant;

class Equipement extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'TB_EQUIPEMENTS';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'MARQUE',
        'TYPE_MODELE',
        'IMMATRICULATION',
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
                      ->orWhere('IMMATRICULATION', 'like', '%' . $search . '%');
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
            Log::error('Equipement::lister a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération des équipements.'
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
                'MARQUE'               => 'required|string|max:500',
                'TYPE_MODELE'          => 'required|string|max:500',
                'IMMATRICULATION'      => 'nullable|string|max:500',
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

            $equipement = new self($inputs);
            $equipement->save();

            return [
                'code_http' => 201,
                'code_message' => 201,
                'data' => $equipement
            ];
        } catch (\Exception $e) {
            Log::error('Equipement::ajouter a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la création de l\'équipement.'
            ];
        }
    }

    public static function recuperer($id)
    {
        try {
            $equipement = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$equipement) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'équipement n\'existe pas.'
                ];
            }

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $equipement
            ];
        } catch (\Exception $e) {
            Log::error('Equipement::recuperer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération de l\'équipement.'
            ];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $equipement = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$equipement) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'équipement n\'existe pas.'
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
                'MARQUE'               => 'nullable|string|max:500',
                'TYPE_MODELE'          => 'nullable|string|max:500',
                'IMMATRICULATION'      => 'nullable|string|max:500',
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

            $equipement->update($inputs);

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $equipement
            ];
        } catch (\Exception $e) {
            Log::error('Equipement::modifier a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la modification de l\'équipement.'
            ];
        }
    }

    public static function supprimer($id)
    {
        try {
            $equipement = self::find($id);

            if (!$equipement) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'équipement n\'existe pas.'
                ];
            }

            $equipement->IS_DELETE = true;
            $equipement->save();
            $equipement->delete();

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $equipement
            ];
        } catch (\Exception $e) {
            Log::error('Equipement::supprimer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la suppression de l\'équipement.'
            ];
        }
    }
}
