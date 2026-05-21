<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class Entreprise extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'TB_ENTREPRISE';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'NON_SOCIETE',
        'SITE_WEB',
        'TELEPHONE',
        'FICHIER_LOGO',
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
                $query->where('NON_SOCIETE', 'like', '%' . $search . '%')
                    ->orWhere('SITE_WEB', 'like', '%' . $search . '%')
                    ->orWhere('TELEPHONE', 'like', '%' . $search . '%');
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
            Log::error('Entreprise::lister a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la recuperation des entreprises.'
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
                'NON_SOCIETE' => 'required|string|max:500|unique:TB_ENTREPRISE,NON_SOCIETE',
                'SITE_WEB' => 'nullable|string|max:500',
                'TELEPHONE' => 'nullable|string|max:500',
                'FICHIER_LOGO' => 'nullable|string',
                'IS_DELETE' => 'nullable|boolean',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $entreprise = new self($inputs);
            $entreprise->save();

            return [
                'code_http' => 201,
                'code_message' => 201,
                'data' => $entreprise
            ];
        } catch (\Exception $e) {
            Log::error('Entreprise::ajouter a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la creation de l\'entreprise.'
            ];
        }
    }

    public static function recuperer($id)
    {
        try {
            $entreprise = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$entreprise) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'entreprise n\'existe pas.'
                ];
            }

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $entreprise
            ];
        } catch (\Exception $e) {
            Log::error('Entreprise::recuperer a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la recuperation de l\'entreprise.'
            ];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $entreprise = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$entreprise) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'entreprise n\'existe pas.'
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
                'NON_SOCIETE' => 'nullable|string|max:500|unique:TB_ENTREPRISE,NON_SOCIETE,' . $id . ',ID',
                'SITE_WEB' => 'nullable|string|max:500',
                'TELEPHONE' => 'nullable|string|max:500',
                'FICHIER_LOGO' => 'nullable|string',
                'IS_DELETE' => 'nullable|boolean',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $entreprise->update($inputs);

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $entreprise
            ];
        } catch (\Exception $e) {
            Log::error('Entreprise::modifier a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la modification de l\'entreprise.'
            ];
        }
    }

    public static function supprimer($id)
    {
        try {
            $entreprise = self::find($id);

            if (!$entreprise) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'entreprise n\'existe pas.'
                ];
            }

            $entreprise->IS_DELETE = true;
            $entreprise->save();
            $entreprise->delete();

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $entreprise
            ];
        } catch (\Exception $e) {
            Log::error('Entreprise::supprimer a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la suppression de l\'entreprise.'
            ];
        }
    }

    public static function listerRessources($id)
    {
        try {
            $entreprise = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$entreprise) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'entreprise n\'existe pas.'
                ];
            }

            $bases = Base::where('ENTREPRISE_ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->get();

            $abonnements = Abonnement::where('ENTREPRISE_ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->get();

            $employes = Employe::where('ENTREPRISE_ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->get();

            $agrements = Agrement::where('ENTREPRISE_ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->get();

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => [
                    'entreprise' => $entreprise,
                    'bases' => $bases,
                    'agrements' => $agrements,
                    'abonnements' => $abonnements,
                    'employes' => $employes,
                    'totaux' => [
                        'bases' => $bases->count(),
                        'agrements' => $agrements->count(),
                        'abonnements' => $abonnements->count(),
                        'employes' => $employes->count(),
                    ]
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Entreprise::listerRessources a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la recuperation des ressources de l\'entreprise.'
            ];
        }
    }

    /**
     * Relation avec les employés
     */
    public function employes()
    {
        return $this->hasMany(Employe::class, 'ENTREPRISE_ID', 'ID');
    }
}
