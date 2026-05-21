<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class Fonctionnalite extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'TB_FONCTIONNALITE';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'LIBELLE',
        'DESCRIPTION',
        'LIEN',
        'MODULE_ID',
        'IS_DELETE',
    ];

    protected $casts = [
        'MODULE_ID' => 'integer',
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
                $query->where('LIBELLE', 'like', '%' . $search . '%')
                    ->orWhere('DESCRIPTION', 'like', '%' . $search . '%')
                    ->orWhere('MODULE_ID', 'like', '%' . $search . '%');
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
            Log::error('Fonctionnalite::lister a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération des fonctionnalités.'
            ];
        }
    }

    public static function ajouter(Request $request)
    {
        try {
            $inputs = json_decode($request->getContent(), true);

            if (!is_array($inputs)) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => 'Corps de la requête vide.'];
            }

            $validator = Validator::make($inputs, [
                'LIBELLE' => 'nullable|string|max:50|unique:TB_FONCTIONNALITE,LIBELLE',
                'DESCRIPTION' => 'nullable|string|max:255',
                'LIEN' => 'nullable|string',
                'MODULE_ID' => 'nullable|integer|exists:TB_MODULE,ID',
            ]);

            if (!$validator->passes()) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => $validator->errors()->all()];
            }

            $fonctionnalite = new self($inputs);
            $fonctionnalite->save();

            return ['code_http' => 201, 'code_message' => 201, 'data' => $fonctionnalite];
        } catch (\Exception $e) {
            Log::error('Fonctionnalite::ajouter a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la création de la fonctionnalité.'
            ];
        }
    }

    public static function recuperer($id)
    {
        try {
            $fonctionnalite = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$fonctionnalite) {
                return ['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'La fonctionnalité n\'existe pas.'];
            }

            return ['code_http' => 200, 'code_message' => 200, 'data' => $fonctionnalite];
        } catch (\Exception $e) {
            Log::error('Fonctionnalite::recuperer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération de la fonctionnalité.'
            ];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $fonctionnalite = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$fonctionnalite) {
                return ['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'La fonctionnalité n\'existe pas.'];
            }

            $inputs = json_decode($request->getContent(), true);

            if (!is_array($inputs)) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => 'Corps de la requête vide.'];
            }

            $validator = Validator::make($inputs, [
                'LIBELLE' => 'nullable|string|max:50|unique:TB_FONCTIONNALITE,LIBELLE,' . $id . ',ID',
                'DESCRIPTION' => 'nullable|string|max:255',
                'LIEN' => 'nullable|string',
                'MODULE_ID' => 'nullable|integer|exists:TB_MODULE,ID',
            ]);

            if (!$validator->passes()) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => $validator->errors()->all()];
            }

            $fonctionnalite->update($inputs);

            return ['code_http' => 200, 'code_message' => 200, 'data' => $fonctionnalite];
        } catch (\Exception $e) {
            Log::error('Fonctionnalite::modifier a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la modification de la fonctionnalité.'
            ];
        }
    }

    public static function supprimer($id)
    {
        try {
            $fonctionnalite = self::find($id);

            if (!$fonctionnalite) {
                return ['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'La fonctionnalité n\'existe pas.'];
            }

            $fonctionnalite->IS_DELETE = true;
            $fonctionnalite->save();
            $fonctionnalite->delete();

            return ['code_http' => 200, 'code_message' => 200, 'data' => $fonctionnalite];
        } catch (\Exception $e) {
            Log::error('Fonctionnalite::supprimer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la suppression de la fonctionnalité.'
            ];
        }
    }
}