<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class Base extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'TB_BASE';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'INTITULE',
        'ADRESSE_1',
        'ADRESSE_2',
        'ADRESSE_3',
        'CODE_POSTAL',
        'VILLE',
        'PAYS',
        'TELEPHONE',
        'COURRIEL',
        'FICHIERS_IMAGES',
        'TYPE_BASE',
        'ACTIVITES',
        'ENTREPRISE_ID',
        'IS_DELETE',
    ];

    protected $casts = [
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
                    ->orWhere('VILLE', 'like', '%' . $search . '%')
                    ->orWhere('PAYS', 'like', '%' . $search . '%')
                    ->orWhere('TELEPHONE', 'like', '%' . $search . '%')
                    ->orWhere('COURRIEL', 'like', '%' . $search . '%')
                    ->orWhere('TYPE_BASE', 'like', '%' . $search . '%');
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
            Log::error('Base::lister a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la recuperation des bases.'
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
                'INTITULE' => 'required|string|max:255|unique:TB_BASE,INTITULE',
                'ADRESSE_1' => 'nullable|string|max:255',
                'ADRESSE_2' => 'nullable|string|max:255',
                'ADRESSE_3' => 'nullable|string|max:255',
                'CODE_POSTAL' => 'nullable|string|max:255',
                'VILLE' => 'nullable|string|max:255',
                'PAYS' => 'nullable|string|max:255',
                'TELEPHONE' => 'nullable|string|max:255',
                'COURRIEL' => 'nullable|email|max:255',
                'FICHIERS_IMAGES' => 'nullable|string',
                'TYPE_BASE' => 'nullable|in:PRINCIPALE,SCONDAIRE,SITE_EN_LIGNE',
                'ACTIVITES' => 'nullable|string',
                'ENTREPRISE_ID' => 'nullable|integer|exists:TB_ENTREPRISE,ID',
                'IS_DELETE' => 'nullable|boolean',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $base = new self($inputs);
            $base->save();

            return [
                'code_http' => 201,
                'code_message' => 201,
                'data' => $base
            ];
        } catch (\Exception $e) {
            Log::error('Base::ajouter a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la creation de la base.'
            ];
        }
    }

    public static function recuperer($id)
    {
        try {
            $base = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$base) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La base n\'existe pas.'
                ];
            }

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $base
            ];
        } catch (\Exception $e) {
            Log::error('Base::recuperer a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la recuperation de la base.'
            ];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $base = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$base) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La base n\'existe pas.'
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
                'INTITULE' => 'nullable|string|max:255|unique:TB_BASE,INTITULE,' . $id . ',ID',
                'ADRESSE_1' => 'nullable|string|max:255',
                'ADRESSE_2' => 'nullable|string|max:255',
                'ADRESSE_3' => 'nullable|string|max:255',
                'CODE_POSTAL' => 'nullable|string|max:255',
                'VILLE' => 'nullable|string|max:255',
                'PAYS' => 'nullable|string|max:255',
                'TELEPHONE' => 'nullable|string|max:255',
                'COURRIEL' => 'nullable|email|max:255',
                'FICHIERS_IMAGES' => 'nullable|string',
                'TYPE_BASE' => 'nullable|in:PRINCIPALE,SCONDAIRE,SITE_EN_LIGNE',
                'ACTIVITES' => 'nullable|string',
                'ENTREPRISE_ID' => 'nullable|integer|exists:TB_ENTREPRISE,ID',
                'IS_DELETE' => 'nullable|boolean',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $base->update($inputs);

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $base
            ];
        } catch (\Exception $e) {
            Log::error('Base::modifier a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la modification de la base.'
            ];
        }
    }

    public static function supprimer($id)
    {
        try {
            $base = self::find($id);

            if (!$base) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La base n\'existe pas.'
                ];
            }

            $base->IS_DELETE = true;
            $base->save();
            $base->delete();

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $base
            ];
        } catch (\Exception $e) {
            Log::error('Base::supprimer a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la suppression de la base.'
            ];
        }
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'ENTREPRISE_ID', 'ID');
    }
}
