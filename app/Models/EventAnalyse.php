<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Traits\BelongsToTenant;

class EventAnalyse extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'TB_EVENT_ANALYSE';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'ID_EVENT_DECLARATION',
        'ID_STATUT_EVENEMENT',
        'DATE_ANALYSE',
        'TITRE_EVENT',
        'EVENT_DESCRIPTION_ANALYSE',
        'EVENT_LOCATION_ANALYSE',
        'EVENT_TYPE',
        'ROOTCAUSE',
        'FACTEURS_CONTRIBUTIFS',
        'ID_RISQUE',
        'ID_MATRICE_RISQUE',
        'ID_SAFETY_ACTION',
        'RISKLEVEL_FINAL_ACCEPTANCE',
        'ANALYSE_PAR',
        'INFO_AUTORITE',
        'DATE_INFO_AUTORITE',
        'INFO_CLIENT',
        'DATE_INFO_CLIENT',
        'COMMENTAIRE',
        'RISQUE_SUBSIDIAIRE',
        'ID_STATUT',
        'PUBLIE',
        'DATE_PUBLIE',
        'ID_TAG_ETIQUETTE',
        'DATE_CLOTURE',
        'ENTREPRISE_ID',
        'IS_DELETE',
    ];

    protected $casts = [
        'ID_EVENT_DECLARATION' => 'integer',
        'ID_STATUT_EVENEMENT' => 'integer',
        'EVENT_LOCATION_ANALYSE' => 'integer',
        'ID_RISQUE' => 'integer',
        'ID_MATRICE_RISQUE' => 'integer',
        'ID_SAFETY_ACTION' => 'integer',
        'ANALYSE_PAR' => 'integer',
        'ID_STATUT' => 'integer',
        'ID_TAG_ETIQUETTE' => 'integer',
        'ENTREPRISE_ID' => 'integer',
        'IS_DELETE' => 'boolean',
    ];

    protected $dates = ['deleted_at', 'DATE_ANALYSE', 'DATE_INFO_AUTORITE', 'DATE_INFO_CLIENT', 'DATE_PUBLIE', 'DATE_CLOTURE'];

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'ENTREPRISE_ID', 'ID');
    }

    public function eventDeclaration()
    {
        return $this->belongsTo(EventDeclaration::class, 'ID_EVENT_DECLARATION', 'ID');
    }

    public function safetyAction()
    {
        return $this->belongsTo(SafetyAction::class, 'ID_SAFETY_ACTION', 'ID');
    }

    public function safetyActions()
    {
        return $this->hasMany(SafetyAction::class, 'ID_EVENT_ANALYSE', 'ID');
    }

    public function analyste()
    {
        return $this->belongsTo(Utilisateur::class, 'ANALYSE_PAR', 'id');
    }

    public function statut()
    {
        return $this->belongsTo(Statut::class, 'ID_STATUT', 'ID');
    }

    public static function lister(Request $request)
    {
        try {
            $per_page = $request->input('per_page', 15);
            $page     = $request->input('page', 1);
            $search   = $request->input('search', '');

            $query = self::where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->with(['eventDeclaration', 'safetyAction', 'analyste', 'statut']);

            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('TITRE_EVENT', 'like', '%' . $search . '%')
                      ->orWhere('EVENT_DESCRIPTION_ANALYSE', 'like', '%' . $search . '%');
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
            Log::error('EventAnalyse::lister a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération des analyses d\'événements.'
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
                'ID_EVENT_DECLARATION'       => 'nullable|integer',
                'ID_STATUT_EVENEMENT'        => 'nullable|integer',
                'DATE_ANALYSE'               => 'required|date',
                'TITRE_EVENT'                => 'required|string',
                'EVENT_DESCRIPTION_ANALYSE'  => 'required|string',
                'EVENT_LOCATION_ANALYSE'     => 'required|integer',
                'EVENT_TYPE'                 => 'required|string|in:ACCIDENT,INCIDENT MAJEUR,INCIDENT MINEUR,DANGER',
                'ROOTCAUSE'                  => 'nullable|string',
                'FACTEURS_CONTRIBUTIFS'      => 'nullable|string',
                'ID_RISQUE'                  => 'nullable|integer',
                'ID_MATRICE_RISQUE'          => 'nullable|integer',
                'ID_SAFETY_ACTION'           => 'nullable|integer|exists:TB_SAFETY_ACTION,ID',
                'RISKLEVEL_FINAL_ACCEPTANCE' => 'nullable|string',
                'ANALYSE_PAR'                => 'nullable|integer|exists:utilisateurs,id',
                'INFO_AUTORITE'              => 'nullable|string|in:OUI,NON',
                'DATE_INFO_AUTORITE'         => 'nullable|date',
                'INFO_CLIENT'                => 'nullable|string|in:OUI,NON',
                'DATE_INFO_CLIENT'           => 'nullable|date',
                'COMMENTAIRE'                => 'nullable|string',
                'RISQUE_SUBSIDIAIRE'         => 'nullable|string|in:OUI,NON',
                'ID_STATUT'                  => 'nullable|integer|exists:TB_STATUT,ID',
                'PUBLIE'                     => 'nullable|string|in:OUI,NON',
                'DATE_PUBLIE'                => 'nullable|date',
                'ID_TAG_ETIQUETTE'           => 'nullable|integer',
                'DATE_CLOTURE'               => 'nullable|date',
                'ENTREPRISE_ID'              => 'nullable|integer|exists:TB_ENTREPRISE,ID',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $eventAnalyse = new self($inputs);
            $eventAnalyse->save();

            return [
                'code_http' => 201,
                'code_message' => 201,
                'data' => $eventAnalyse
            ];
        } catch (\Exception $e) {
            Log::error('EventAnalyse::ajouter a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la création de l\'analyse d\'événement.'
            ];
        }
    }

    public static function recuperer($id)
    {
        try {
            $eventAnalyse = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$eventAnalyse) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'analyse d\'événement n\'existe pas.'
                ];
            }

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $eventAnalyse
            ];
        } catch (\Exception $e) {
            Log::error('EventAnalyse::recuperer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération de l\'analyse d\'événement.'
            ];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $eventAnalyse = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$eventAnalyse) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'analyse d\'événement n\'existe pas.'
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
                'ID_EVENT_DECLARATION'       => 'nullable|integer',
                'ID_STATUT_EVENEMENT'        => 'nullable|integer',
                'DATE_ANALYSE'               => 'nullable|date',
                'TITRE_EVENT'                => 'nullable|string',
                'EVENT_DESCRIPTION_ANALYSE'  => 'nullable|string',
                'EVENT_LOCATION_ANALYSE'     => 'nullable|integer',
                'EVENT_TYPE'                 => 'nullable|string|in:ACCIDENT,INCIDENT MAJEUR,INCIDENT MINEUR,DANGER',
                'ROOTCAUSE'                  => 'nullable|string',
                'FACTEURS_CONTRIBUTIFS'      => 'nullable|string',
                'ID_RISQUE'                  => 'nullable|integer',
                'ID_MATRICE_RISQUE'          => 'nullable|integer',
                'ID_SAFETY_ACTION'           => 'nullable|integer|exists:TB_SAFETY_ACTION,ID',
                'RISKLEVEL_FINAL_ACCEPTANCE' => 'nullable|string',
                'ANALYSE_PAR'                => 'nullable|integer|exists:utilisateurs,id',
                'INFO_AUTORITE'              => 'nullable|string|in:OUI,NON',
                'DATE_INFO_AUTORITE'         => 'nullable|date',
                'INFO_CLIENT'                => 'nullable|string|in:OUI,NON',
                'DATE_INFO_CLIENT'           => 'nullable|date',
                'COMMENTAIRE'                => 'nullable|string',
                'RISQUE_SUBSIDIAIRE'         => 'nullable|string|in:OUI,NON',
                'ID_STATUT'                  => 'nullable|integer|exists:TB_STATUT,ID',
                'PUBLIE'                     => 'nullable|string|in:OUI,NON',
                'DATE_PUBLIE'                => 'nullable|date',
                'ID_TAG_ETIQUETTE'           => 'nullable|integer',
                'DATE_CLOTURE'               => 'nullable|date',
                'ENTREPRISE_ID'              => 'nullable|integer|exists:TB_ENTREPRISE,ID',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $eventAnalyse->update($inputs);

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $eventAnalyse
            ];
        } catch (\Exception $e) {
            Log::error('EventAnalyse::modifier a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la modification de l\'analyse d\'événement.'
            ];
        }
    }

    public static function supprimer($id)
    {
        try {
            $eventAnalyse = self::find($id);

            if (!$eventAnalyse) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'analyse d\'événement n\'existe pas.'
                ];
            }

            $eventAnalyse->IS_DELETE = true;
            $eventAnalyse->save();
            $eventAnalyse->delete();

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $eventAnalyse
            ];
        } catch (\Exception $e) {
            Log::error('EventAnalyse::supprimer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la suppression de l\'analyse d\'événement.'
            ];
        }
    }
}
