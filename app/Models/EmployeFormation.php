<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EmployeFormation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'TB_EMPLOYE_FORMATION';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'EMPLOYE_ID',
        'FORMATION_ID',
        'DATE_REALISATION',
        'DATE_VALIDITE',
        'FICHIERS_IMAGES',
        'STATUT',
        'MODIFICATION',
        'IS_DELETE',
    ];

    protected $casts = [
        'EMPLOYE_ID' => 'integer',
        'FORMATION_ID' => 'integer',
        'MODIFICATION' => 'integer',
        'IS_DELETE' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    public static function lister(Request $request)
    {
        try {
            $per_page = $request->input('per_page', 15);
            $page = $request->input('page', 1);
            $search = $request->input('search', '');
            $employe_id = $request->input('employe_id', null);
            $formation_id = $request->input('formation_id', null);
            $statut = $request->input('statut', null);

            $query = self::where('IS_DELETE', false)
                ->whereNull('deleted_at');

            if (!empty($employe_id)) {
                $query->where('EMPLOYE_ID', $employe_id);
            }

            if (!empty($formation_id)) {
                $query->where('FORMATION_ID', $formation_id);
            }

            if (!empty($statut)) {
                $query->where('STATUT', $statut);
            }

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('EMPLOYE_ID', 'like', '%' . $search . '%')
                        ->orWhere('FORMATION_ID', 'like', '%' . $search . '%');
                });
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
            Log::error('EmployeFormation::lister a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la recuperation des formations des employes.'
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
                    'erreurs' => 'Corps de la requete vide.'
                ];
            }

            $validator = Validator::make($inputs, [
                'EMPLOYE_ID' => 'nullable|integer|exists:TB_EMPLOYE,ID',
                'FORMATION_ID' => 'nullable|integer|exists:TB_FORMATION,ID',
                'DATE_REALISATION' => 'nullable|date',
                'DATE_VALIDITE' => 'nullable|date',
                'FICHIERS_IMAGES' => 'nullable|string',
                'STATUT' => 'nullable|in:ACTIF,EXPIRE',
                'MODIFICATION' => 'nullable|integer',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $employeFormation = new self($inputs);
            $employeFormation->save();

            return [
                'code_http' => 201,
                'code_message' => 201,
                'data' => $employeFormation
            ];
        } catch (\Exception $e) {
            Log::error('EmployeFormation::ajouter a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la creation de la formation de l\'employe.'
            ];
        }
    }

    public static function recuperer($id)
    {
        try {
            $employeFormation = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$employeFormation) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La formation de l\'employe n\'existe pas.'
                ];
            }

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $employeFormation
            ];
        } catch (\Exception $e) {
            Log::error('EmployeFormation::recuperer a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la recuperation de la formation de l\'employe.'
            ];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $employeFormation = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$employeFormation) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La formation de l\'employe n\'existe pas.'
                ];
            }

            $inputs = json_decode($request->getContent(), true);

            if (!is_array($inputs)) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => 'Corps de la requete vide.'
                ];
            }

            $validator = Validator::make($inputs, [
                'EMPLOYE_ID' => 'nullable|integer|exists:TB_EMPLOYE,ID',
                'FORMATION_ID' => 'nullable|integer|exists:TB_FORMATION,ID',
                'DATE_REALISATION' => 'nullable|date',
                'DATE_VALIDITE' => 'nullable|date',
                'FICHIERS_IMAGES' => 'nullable|string',
                'STATUT' => 'nullable|in:ACTIF,EXPIRE',
                'MODIFICATION' => 'nullable|integer',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $employeFormation->update($inputs);

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $employeFormation
            ];
        } catch (\Exception $e) {
            Log::error('EmployeFormation::modifier a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la modification de la formation de l\'employe.'
            ];
        }
    }

    public static function supprimer($id)
    {
        try {
            $employeFormation = self::find($id);

            if (!$employeFormation) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La formation de l\'employe n\'existe pas.'
                ];
            }

            $employeFormation->IS_DELETE = true;
            $employeFormation->save();
            $employeFormation->delete();

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $employeFormation
            ];
        } catch (\Exception $e) {
            Log::error('EmployeFormation::supprimer a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la suppression de la formation de l\'employe.'
            ];
        }
    }
}
