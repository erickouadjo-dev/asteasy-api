<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MesureAdditionnelle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'TB_MESURES_ADDITIONNELLES';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'INTITULE',
        'DESCRIPTION',
        'FREQUENCE',
        'GRAVITE',
        'COMMENTAIRES',
        'ID_TAG_ETIQUETTE',
        'DEPARTEMENT_RESPONSABLE',
        'IS_DELETE',
    ];

    protected $casts = [
        'IS_DELETE' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    public function tag()
    {
        return $this->belongsTo(TargEtiquette::class, 'ID_TAG_ETIQUETTE', 'ID');
    }

    public function responsable()
    {
        return $this->belongsTo(Utilisateur::class, 'DEPARTEMENT_RESPONSABLE', 'id');
    }

    public static function lister(Request $request)
    {
        try {
            $per_page = $request->input('per_page', 15);
            $page     = $request->input('page', 1);
            $search   = $request->input('search', '');

            $query = self::where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->with(['tag', 'responsable']);

            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('INTITULE', 'like', '%' . $search . '%')
                      ->orWhere('DESCRIPTION', 'like', '%' . $search . '%')
                      ->orWhere('FREQUENCE', 'like', '%' . $search . '%')
                      ->orWhere('GRAVITE', 'like', '%' . $search . '%')
                      ->orWhere('COMMENTAIRES', 'like', '%' . $search . '%');
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
            Log::error('MesureAdditionnelle::lister a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération des mesures additionnelles.'
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
                'INTITULE'                => 'required|string|max:255|unique:TB_MESURES_ADDITIONNELLES,INTITULE',
                'DESCRIPTION'             => 'required|string',
                'FREQUENCE'               => 'required|string|max:255',
                'GRAVITE'                 => 'required|string|max:255',
                'COMMENTAIRES'            => 'required|string',
                'ID_TAG_ETIQUETTE'        => 'nullable|integer|exists:TB_TARG_ETIQUETTE,ID',
                'DEPARTEMENT_RESPONSABLE' => 'nullable|integer|exists:utilisateurs,id',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $mesure = new self($inputs);
            $mesure->save();
            $mesure->load(['tag', 'responsable']);

            return [
                'code_http' => 201,
                'code_message' => 201,
                'data' => $mesure
            ];
        } catch (\Exception $e) {
            Log::error('MesureAdditionnelle::ajouter a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la création de la mesure additionnelle.'
            ];
        }
    }

    public static function recuperer($id)
    {
        try {
            $mesure = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->with(['tag', 'responsable'])
                ->first();

            if (!$mesure) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La mesure additionnelle n\'existe pas.'
                ];
            }

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $mesure
            ];
        } catch (\Exception $e) {
            Log::error('MesureAdditionnelle::recuperer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération de la mesure additionnelle.'
            ];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $mesure = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$mesure) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La mesure additionnelle n\'existe pas.'
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
                'INTITULE'                => 'nullable|string|max:255|unique:TB_MESURES_ADDITIONNELLES,INTITULE,' . $id . ',ID',
                'DESCRIPTION'             => 'nullable|string',
                'FREQUENCE'               => 'nullable|string|max:255',
                'GRAVITE'                 => 'nullable|string|max:255',
                'COMMENTAIRES'            => 'nullable|string',
                'ID_TAG_ETIQUETTE'        => 'nullable|integer|exists:TB_TARG_ETIQUETTE,ID',
                'DEPARTEMENT_RESPONSABLE' => 'nullable|integer|exists:utilisateurs,id',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $mesure->update($inputs);
            $mesure->load(['tag', 'responsable']);

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $mesure
            ];
        } catch (\Exception $e) {
            Log::error('MesureAdditionnelle::modifier a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la modification de la mesure additionnelle.'
            ];
        }
    }

    public static function supprimer($id)
    {
        try {
            $mesure = self::find($id);

            if (!$mesure) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La mesure additionnelle n\'existe pas.'
                ];
            }

            $mesure->IS_DELETE = true;
            $mesure->save();
            $mesure->delete();

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $mesure
            ];
        } catch (\Exception $e) {
            Log::error('MesureAdditionnelle::supprimer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la suppression de la mesure additionnelle.'
            ];
        }
    }
}
