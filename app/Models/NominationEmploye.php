<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Traits\BelongsToTenant;

class NominationEmploye extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'TB_NOMINATION_EMPLOYE';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'EMPLOYE_ID',
        'INTITULE_POSTE',
        'DESCRIPTION_POSTE',
        'AGREMENT_CONCERNE',
        'DATE_ACCEPTATION',
        'FICHIERS',
        'DATE_PRISE_DE_FONCTION',
        'DATE_FIN',
        'IS_DELETE',
        'ENTREPRISE_ID',
    ];

    protected $casts = [
        'EMPLOYE_ID' => 'integer',
        'DATE_ACCEPTATION' => 'datetime',
        'DATE_PRISE_DE_FONCTION' => 'datetime',
        'DATE_FIN' => 'datetime',
        'IS_DELETE' => 'boolean',
        'ENTREPRISE_ID' => 'integer',
    ];

    protected $dates = ['deleted_at'];

    public static function lister(Request $request)
    {
        try {
            $per_page = $request->input('per_page', 15);
            $page = $request->input('page', 1);
            $search = $request->input('search', '');
            $employe_id = $request->input('employe_id', null);

            $query = self::where('IS_DELETE', false)
                ->whereNull('deleted_at');

            if (!empty($employe_id)) {
                $query->where('EMPLOYE_ID', $employe_id);
            }

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('INTITULE_POSTE', 'like', '%' . $search . '%')
                        ->orWhere('DESCRIPTION_POSTE', 'like', '%' . $search . '%')
                        ->orWhere('AGREMENT_CONCERNE', 'like', '%' . $search . '%')
                        ->orWhere('EMPLOYE_ID', 'like', '%' . $search . '%');
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
            Log::error('NominationEmploye::lister a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la recuperation des nominations employes.'
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
                'INTITULE_POSTE' => 'nullable|string|max:255',
                'DESCRIPTION_POSTE' => 'nullable|string|max:500',
                'AGREMENT_CONCERNE' => 'nullable|string|max:255',
                'DATE_ACCEPTATION' => 'nullable|date',
                'FICHIERS' => 'nullable|string',
                'DATE_PRISE_DE_FONCTION' => 'nullable|date',
                'DATE_FIN' => 'nullable|date',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $nominationEmploye = new self($inputs);
            $nominationEmploye->save();

            return [
                'code_http' => 201,
                'code_message' => 201,
                'data' => $nominationEmploye
            ];
        } catch (\Exception $e) {
            Log::error('NominationEmploye::ajouter a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la creation de la nomination employe.'
            ];
        }
    }

    public static function recuperer($id)
    {
        try {
            $nominationEmploye = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$nominationEmploye) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La nomination employe n\'existe pas.'
                ];
            }

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $nominationEmploye
            ];
        } catch (\Exception $e) {
            Log::error('NominationEmploye::recuperer a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la recuperation de la nomination employe.'
            ];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $nominationEmploye = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$nominationEmploye) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La nomination employe n\'existe pas.'
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
                'INTITULE_POSTE' => 'nullable|string|max:255',
                'DESCRIPTION_POSTE' => 'nullable|string|max:500',
                'AGREMENT_CONCERNE' => 'nullable|string|max:255',
                'DATE_ACCEPTATION' => 'nullable|date',
                'FICHIERS' => 'nullable|string',
                'DATE_PRISE_DE_FONCTION' => 'nullable|date',
                'DATE_FIN' => 'nullable|date',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $nominationEmploye->update($inputs);

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $nominationEmploye
            ];
        } catch (\Exception $e) {
            Log::error('NominationEmploye::modifier a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la modification de la nomination employe.'
            ];
        }
    }

    public static function supprimer($id)
    {
        try {
            $nominationEmploye = self::find($id);

            if (!$nominationEmploye) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La nomination employe n\'existe pas.'
                ];
            }

            $nominationEmploye->IS_DELETE = true;
            $nominationEmploye->save();
            $nominationEmploye->delete();

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $nominationEmploye
            ];
        } catch (\Exception $e) {
            Log::error('NominationEmploye::supprimer a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la suppression de la nomination employe.'
            ];
        }
    }
}
