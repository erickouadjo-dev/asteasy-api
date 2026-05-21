<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class Agrement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'TB_AGREMENT';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'INTITULE',
        'DESCRIPTION',
        'DATE_OBTENTION',
        'DATE_VALIDITE',
        'DELAI_RENOUVELLEMENT',
        'DOCUMENTATION_ID',
        'FICHIERS_IMAGES',
        'ENTREPRISE_ID',
        'IS_DELETE',
    ];

    protected $casts = [
        'DATE_OBTENTION' => 'date',
        'DATE_VALIDITE' => 'date',
        'DELAI_RENOUVELLEMENT' => 'integer',
        'DOCUMENTATION_ID' => 'integer',
        'ENTREPRISE_ID' => 'integer',
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
                $query->where('INTITULE', 'like', '%' . $search . '%')
                    ->orWhere('DESCRIPTION', 'like', '%' . $search . '%')
                    ->orWhere('ENTREPRISE_ID', 'like', '%' . $search . '%');
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
            Log::error('Agrement::lister a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la recuperation des agrements.'
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
                'INTITULE' => 'required|string|max:500',
                'DESCRIPTION' => 'nullable|string',
                'DATE_OBTENTION' => 'nullable|date',
                'DATE_VALIDITE' => 'nullable|date',
                'DELAI_RENOUVELLEMENT' => 'nullable|integer',
                'DOCUMENTATION_ID' => 'nullable|integer|exists:TB_DOCUMENTATION,ID',
                'FICHIERS_IMAGES' => 'required|string',
                'ENTREPRISE_ID' => 'nullable|integer|exists:TB_ENTREPRISE,ID',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $agrement = new self($inputs);
            $agrement->save();

            return [
                'code_http' => 201,
                'code_message' => 201,
                'data' => $agrement
            ];
        } catch (\Exception $e) {
            Log::error('Agrement::ajouter a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la creation de l\'agrement.'
            ];
        }
    }

    public static function recuperer($id)
    {
        try {
            $agrement = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$agrement) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'agrement n\'existe pas.'
                ];
            }

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $agrement
            ];
        } catch (\Exception $e) {
            Log::error('Agrement::recuperer a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la recuperation de l\'agrement.'
            ];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $agrement = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$agrement) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'agrement n\'existe pas.'
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
                'INTITULE' => 'nullable|string|max:500',
                'DESCRIPTION' => 'nullable|string',
                'DATE_OBTENTION' => 'nullable|date',
                'DATE_VALIDITE' => 'nullable|date',
                'DELAI_RENOUVELLEMENT' => 'nullable|integer',
                'DOCUMENTATION_ID' => 'nullable|integer|exists:TB_DOCUMENTATION,ID',
                'FICHIERS_IMAGES' => 'nullable|string',
                'ENTREPRISE_ID' => 'nullable|integer|exists:TB_ENTREPRISE,ID',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $agrement->update($inputs);

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $agrement
            ];
        } catch (\Exception $e) {
            Log::error('Agrement::modifier a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la modification de l\'agrement.'
            ];
        }
    }

    public static function supprimer($id)
    {
        try {
            $agrement = self::find($id);

            if (!$agrement) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'agrement n\'existe pas.'
                ];
            }

            $agrement->IS_DELETE = true;
            $agrement->save();
            $agrement->delete();

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $agrement
            ];
        } catch (\Exception $e) {
            Log::error('Agrement::supprimer a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la suppression de l\'agrement.'
            ];
        }
    }
}
