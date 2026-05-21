<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class Formation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'TB_FORMATION';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'INTITULE',
        'DESCRIPTION',
        'VALIDITE_TYPE',
        'VALIDITE_DATE',
        'VALIDITE_MODIFIABLE',
        'FICHIERS_IMAGES',
        'MODIFICATION',
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
            $page = $request->input('page', 1);
            $search = $request->input('search', '');

            $query = self::where('IS_DELETE', false)
                ->whereNull('deleted_at');

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('INTITULE', 'like', '%' . $search . '%')
                        ->orWhere('DESCRIPTION', 'like', '%' . $search . '%');
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
            Log::error('Formation::lister a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la recuperation des formations.'
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
                'INTITULE' => 'required|string|max:200',
                'DESCRIPTION' => 'nullable|string|max:500',
                'VALIDITE_TYPE' => 'nullable|in:6M,12M,24M,36M',
                'VALIDITE_DATE' => 'nullable|in:DATE_A_DATE,FIN_DE_MOIS',
                'VALIDITE_MODIFIABLE' => 'nullable|in:OUI,NON',
                'FICHIERS_IMAGES' => 'nullable|string',
                'MODIFICATION' => 'nullable|string',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $formation = new self($inputs);
            $formation->save();

            return [
                'code_http' => 201,
                'code_message' => 201,
                'data' => $formation
            ];
        } catch (\Exception $e) {
            Log::error('Formation::ajouter a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la creation de la formation.'
            ];
        }
    }

    public static function recuperer($id)
    {
        try {
            $formation = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$formation) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La formation n\'existe pas.'
                ];
            }

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $formation
            ];
        } catch (\Exception $e) {
            Log::error('Formation::recuperer a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la recuperation de la formation.'
            ];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $formation = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$formation) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La formation n\'existe pas.'
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
                'INTITULE' => 'nullable|string|max:200',
                'DESCRIPTION' => 'nullable|string|max:500',
                'VALIDITE_TYPE' => 'nullable|in:6M,12M,24M,36M',
                'VALIDITE_DATE' => 'nullable|in:DATE_A_DATE,FIN_DE_MOIS',
                'VALIDITE_MODIFIABLE' => 'nullable|in:OUI,NON',
                'FICHIERS_IMAGES' => 'nullable|string',
                'MODIFICATION' => 'nullable|string',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $formation->update($inputs);

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $formation
            ];
        } catch (\Exception $e) {
            Log::error('Formation::modifier a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la modification de la formation.'
            ];
        }
    }

    public static function supprimer($id)
    {
        try {
            $formation = self::find($id);

            if (!$formation) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La formation n\'existe pas.'
                ];
            }

            $formation->IS_DELETE = true;
            $formation->save();
            $formation->delete();

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $formation
            ];
        } catch (\Exception $e) {
            Log::error('Formation::supprimer a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la suppression de la formation.'
            ];
        }
    }
}
