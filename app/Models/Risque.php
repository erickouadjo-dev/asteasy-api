<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class Risque extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'TB_RISQUES';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'REFERENCE_RISK',
        'INTITULE_RISK',
        'ID_RISK_CATEGORY',
        'ID_RISK_SUBCATEGORY',
        'CONSEQUENSE_ULTIME',
        'ID_MESURES_CONTROLE',
        'FREQUENCE_RISK_INITIAL',
        'GRAVITE_RISK_INITIAL',
        'ID_MATRICE_RISQUE',
        'ID_MESURES_ADDITIONNELLES',
        'FREQUENCE_RISK_FINAL',
        'GRAVITE_RISK_FINAL',
        'NIVEAU_MAITRISE',
        'DATE_STATUT_RISK',
        'STATUT_RISK',
        'RESPONSABLE',
        'DATE_CONTROLE',
        'COMMENTAIRES',
        'IS_DELETE',
    ];

    protected $casts = [
        'IS_DELETE' => 'boolean',
        'DATE_STATUT_RISK' => 'date',
        'DATE_CONTROLE' => 'date',
    ];

    protected $dates = ['deleted_at', 'DATE_STATUT_RISK', 'DATE_CONTROLE'];

    public function category()
    {
        return $this->belongsTo(RiskCategory::class, 'ID_RISK_CATEGORY', 'ID');
    }

    public function subcategory()
    {
        return $this->belongsTo(RiskSubcategory::class, 'ID_RISK_SUBCATEGORY', 'ID');
    }

    public function mesureControle()
    {
        return $this->belongsTo(MesureControle::class, 'ID_MESURES_CONTROLE', 'ID');
    }

    public function mesureAdditionnelle()
    {
        return $this->belongsTo(MesureAdditionnelle::class, 'ID_MESURES_ADDITIONNELLES', 'ID');
    }

    public function matriceRisque()
    {
        return $this->belongsTo(MatriceRisque::class, 'ID_MATRICE_RISQUE', 'ID');
    }

    public function responsableUser()
    {
        return $this->belongsTo(Utilisateur::class, 'RESPONSABLE', 'id');
    }

    public static function lister(Request $request)
    {
        try {
            $per_page = $request->input('per_page', 15);
            $page     = $request->input('page', 1);
            $search   = $request->input('search', '');

            $query = self::where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->with(['category', 'subcategory', 'mesureControle', 'mesureAdditionnelle', 'matriceRisque', 'responsableUser']);

            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('REFERENCE_RISK', 'like', '%' . $search . '%')
                      ->orWhere('INTITULE_RISK', 'like', '%' . $search . '%')
                      ->orWhere('CONSEQUENSE_ULTIME', 'like', '%' . $search . '%')
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
            Log::error('Risque::lister a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération des risques.'
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
                'REFERENCE_RISK'            => 'required|string|max:255|unique:TB_RISQUES,REFERENCE_RISK',
                'INTITULE_RISK'             => 'required|string|max:255',
                'ID_RISK_CATEGORY'          => 'nullable|integer|exists:TB_RISK_CATEGORY,ID',
                'ID_RISK_SUBCATEGORY'       => 'nullable|integer|exists:TB_RISK_SUBCATEGORY,ID',
                'CONSEQUENSE_ULTIME'        => 'required|string|max:255',
                'ID_MESURES_CONTROLE'       => 'nullable|integer|exists:TB_MESURES_CONTROLE,ID',
                'FREQUENCE_RISK_INITIAL'    => 'required|string|max:255',
                'GRAVITE_RISK_INITIAL'      => 'required|string|max:255',
                'ID_MATRICE_RISQUE'         => 'nullable|integer|exists:TB_MATRICE_RISQUE,ID',
                'ID_MESURES_ADDITIONNELLES' => 'nullable|integer|exists:TB_MESURES_ADDITIONNELLES,ID',
                'FREQUENCE_RISK_FINAL'      => 'required|string|max:255',
                'GRAVITE_RISK_FINAL'        => 'required|string|max:255',
                'NIVEAU_MAITRISE'           => 'required|in:ELEVE,MOYENNE,FAIBLE',
                'DATE_STATUT_RISK'          => 'required|date_format:Y-m-d',
                'STATUT_RISK'               => 'required|in:MAITRISE,PARTIELLEMENT_MAITRISE,NON_MAITRISE',
                'RESPONSABLE'               => 'nullable|integer|exists:utilisateurs,id',
                'DATE_CONTROLE'             => 'required|date_format:Y-m-d',
                'COMMENTAIRES'              => 'required|string',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $risque = new self($inputs);
            $risque->save();
            $risque->load(['category', 'subcategory', 'mesureControle', 'mesureAdditionnelle', 'matriceRisque', 'responsableUser']);

            return [
                'code_http' => 201,
                'code_message' => 201,
                'data' => $risque
            ];
        } catch (\Exception $e) {
            Log::error('Risque::ajouter a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la création du risque.'
            ];
        }
    }

    public static function recuperer($id)
    {
        try {
            $risque = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->with(['category', 'subcategory', 'mesureControle', 'mesureAdditionnelle', 'matriceRisque', 'responsableUser'])
                ->first();

            if (!$risque) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'Le risque n\'existe pas.'
                ];
            }

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $risque
            ];
        } catch (\Exception $e) {
            Log::error('Risque::recuperer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération du risque.'
            ];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $risque = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$risque) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'Le risque n\'existe pas.'
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
                'REFERENCE_RISK'            => 'nullable|string|max:255|unique:TB_RISQUES,REFERENCE_RISK,' . $id . ',ID',
                'INTITULE_RISK'             => 'nullable|string|max:255',
                'ID_RISK_CATEGORY'          => 'nullable|integer|exists:TB_RISK_CATEGORY,ID',
                'ID_RISK_SUBCATEGORY'       => 'nullable|integer|exists:TB_RISK_SUBCATEGORY,ID',
                'CONSEQUENSE_ULTIME'        => 'nullable|string|max:255',
                'ID_MESURES_CONTROLE'       => 'nullable|integer|exists:TB_MESURES_CONTROLE,ID',
                'FREQUENCE_RISK_INITIAL'    => 'nullable|string|max:255',
                'GRAVITE_RISK_INITIAL'      => 'nullable|string|max:255',
                'ID_MATRICE_RISQUE'         => 'nullable|integer|exists:TB_MATRICE_RISQUE,ID',
                'ID_MESURES_ADDITIONNELLES' => 'nullable|integer|exists:TB_MESURES_ADDITIONNELLES,ID',
                'FREQUENCE_RISK_FINAL'      => 'nullable|string|max:255',
                'GRAVITE_RISK_FINAL'        => 'nullable|string|max:255',
                'NIVEAU_MAITRISE'           => 'nullable|in:ELEVE,MOYENNE,FAIBLE',
                'DATE_STATUT_RISK'          => 'nullable|date_format:Y-m-d',
                'STATUT_RISK'               => 'nullable|in:MAITRISE,PARTIELLEMENT_MAITRISE,NON_MAITRISE',
                'RESPONSABLE'               => 'nullable|integer|exists:utilisateurs,id',
                'DATE_CONTROLE'             => 'nullable|date_format:Y-m-d',
                'COMMENTAIRES'              => 'nullable|string',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $risque->update($inputs);
            $risque->load(['category', 'subcategory', 'mesureControle', 'mesureAdditionnelle', 'matriceRisque', 'responsableUser']);

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $risque
            ];
        } catch (\Exception $e) {
            Log::error('Risque::modifier a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la modification du risque.'
            ];
        }
    }

    public static function supprimer($id)
    {
        try {
            $risque = self::find($id);

            if (!$risque) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'Le risque n\'existe pas.'
                ];
            }

            $risque->IS_DELETE = true;
            $risque->save();
            $risque->delete();

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $risque
            ];
        } catch (\Exception $e) {
            Log::error('Risque::supprimer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la suppression du risque.'
            ];
        }
    }
}
