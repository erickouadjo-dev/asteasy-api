<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Traits\BelongsToTenant;

class SafetyAction extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'TB_SAFETY_ACTION';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'INTITULE',
        'DESCRIPTION',
        'DATE_OUVERTURE',
        'ID_EVENT_ANALYSE',
        'ID_TYPE_ORIGINE_ACTION',
        'ID_TASK_SAFETY',
        'FICHIERS_IMAGES',
        'ID_RECURRENCE',
        'ID_STATUT',
        'ID_AVANCEMENT',
        'DATE_CLOTURE',
        'RESPONSABLE',
        'ID_TAG',
        'ACTION_LIEE_RISQUE',
        'ID_RISQUE',
        'ACTION_FREQUENCE_RISQUE',
        'ACTION_GRAVITE_RISQUE',
        'ENTREPRISE_ID',
        'IS_DELETE',
    ];

    protected $casts = [
        'ID_EVENT_ANALYSE' => 'integer',
        'ID_TYPE_ORIGINE_ACTION' => 'integer',
        'ID_TASK_SAFETY' => 'integer',
        'ID_RECURRENCE' => 'integer',
        'ID_STATUT' => 'integer',
        'ID_AVANCEMENT' => 'integer',
        'RESPONSABLE' => 'integer',
        'ID_TAG' => 'integer',
        'ID_RISQUE' => 'integer',
        'ENTREPRISE_ID' => 'integer',
        'IS_DELETE' => 'boolean',
    ];

    protected $dates = ['deleted_at', 'DATE_OUVERTURE', 'DATE_CLOTURE'];

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'ENTREPRISE_ID', 'ID');
    }

    public function eventAnalyse()
    {
        return $this->belongsTo(EventAnalyse::class, 'ID_EVENT_ANALYSE', 'ID');
    }

    public function tasks()
    {
        return $this->hasMany(TasksSafety::class, 'ID_SAFETY_ACTION', 'ID');
    }

    public function typeOrigineAction()
    {
        return $this->belongsTo(TypeOrigineAction::class, 'ID_TYPE_ORIGINE_ACTION', 'ID');
    }

    public function taskSafety()
    {
        return $this->belongsTo(TasksSafety::class, 'ID_TASK_SAFETY', 'ID');
    }

    public function recurrence()
    {
        return $this->belongsTo(Recurrence::class, 'ID_RECURRENCE', 'ID');
    }

    public function statut()
    {
        return $this->belongsTo(Statut::class, 'ID_STATUT', 'ID');
    }

    public function avancement()
    {
        return $this->belongsTo(Avancement::class, 'ID_AVANCEMENT', 'ID');
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
                ->whereNull('deleted_at');

            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('INTITULE', 'like', '%' . $search . '%')
                      ->orWhere('DESCRIPTION', 'like', '%' . $search . '%');
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
            Log::error('SafetyAction::lister a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération des actions de sécurité.'
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
                'INTITULE'                => 'required|string|max:255',
                'DESCRIPTION'             => 'required|string',
                'DATE_OUVERTURE'          => 'required|date',
                'ID_EVENT_ANALYSE'        => 'nullable|integer|exists:TB_EVENT_ANALYSE,ID',
                'ID_TYPE_ORIGINE_ACTION'  => 'nullable|integer|exists:TB_TYPE_ORIGINE_ACTION,ID',
                'ID_TASK_SAFETY'          => 'nullable|integer|exists:TB_TASKS_SAFETY,ID',
                'FICHIERS_IMAGES'         => 'nullable|string',
                'ID_RECURRENCE'           => 'nullable|integer|exists:TB_RECURRENCE,ID',
                'ID_STATUT'               => 'nullable|integer|exists:TB_STATUT,ID',
                'ID_AVANCEMENT'           => 'nullable|integer|exists:TB_AVANCEMENT,ID',
                'DATE_CLOTURE'            => 'nullable|date',
                'RESPONSABLE'             => 'nullable|integer|exists:utilisateurs,id',
                'ID_TAG'                  => 'nullable|integer',
                'ACTION_LIEE_RISQUE'      => 'required|string|in:OUI,NON,NON_APPLICABLE',
                'ID_RISQUE'               => 'nullable|integer',
                'ACTION_FREQUENCE_RISQUE' => 'required|string|in:OUI,NON,NON_APPLICABLE',
                'ACTION_GRAVITE_RISQUE'   => 'required|string|in:OUI,NON,NON_APPLICABLE',
                'ENTREPRISE_ID'           => 'nullable|integer|exists:TB_ENTREPRISE,ID',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $safetyAction = new self($inputs);
            $safetyAction->save();

            return [
                'code_http' => 201,
                'code_message' => 201,
                'data' => $safetyAction
            ];
        } catch (\Exception $e) {
            Log::error('SafetyAction::ajouter a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la création de l\'action de sécurité.'
            ];
        }
    }

    public static function recuperer($id)
    {
        try {
            $safetyAction = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$safetyAction) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'action de sécurité n\'existe pas.'
                ];
            }

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $safetyAction
            ];
        } catch (\Exception $e) {
            Log::error('SafetyAction::recuperer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération de l\'action de sécurité.'
            ];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $safetyAction = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$safetyAction) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'action de sécurité n\'existe pas.'
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
                'INTITULE'                => 'nullable|string|max:255',
                'DESCRIPTION'             => 'nullable|string',
                'DATE_OUVERTURE'          => 'nullable|date',
                'ID_EVENT_ANALYSE'        => 'nullable|integer|exists:TB_EVENT_ANALYSE,ID',
                'ID_TYPE_ORIGINE_ACTION'  => 'nullable|integer|exists:TB_TYPE_ORIGINE_ACTION,ID',
                'ID_TASK_SAFETY'          => 'nullable|integer|exists:TB_TASKS_SAFETY,ID',
                'FICHIERS_IMAGES'         => 'nullable|string',
                'ID_RECURRENCE'           => 'nullable|integer|exists:TB_RECURRENCE,ID',
                'ID_STATUT'               => 'nullable|integer|exists:TB_STATUT,ID',
                'ID_AVANCEMENT'           => 'nullable|integer|exists:TB_AVANCEMENT,ID',
                'DATE_CLOTURE'            => 'nullable|date',
                'RESPONSABLE'             => 'nullable|integer|exists:utilisateurs,id',
                'ID_TAG'                  => 'nullable|integer',
                'ACTION_LIEE_RISQUE'      => 'nullable|string|in:OUI,NON,NON_APPLICABLE',
                'ID_RISQUE'               => 'nullable|integer',
                'ACTION_FREQUENCE_RISQUE' => 'nullable|string|in:OUI,NON,NON_APPLICABLE',
                'ACTION_GRAVITE_RISQUE'   => 'nullable|string|in:OUI,NON,NON_APPLICABLE',
                'ENTREPRISE_ID'           => 'nullable|integer|exists:TB_ENTREPRISE,ID',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $safetyAction->update($inputs);

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $safetyAction
            ];
        } catch (\Exception $e) {
            Log::error('SafetyAction::modifier a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la modification de l\'action de sécurité.'
            ];
        }
    }

    public static function supprimer($id)
    {
        try {
            $safetyAction = self::find($id);

            if (!$safetyAction) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'action de sécurité n\'existe pas.'
                ];
            }

            $safetyAction->IS_DELETE = true;
            $safetyAction->save();
            $safetyAction->delete();

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $safetyAction
            ];
        } catch (\Exception $e) {
            Log::error('SafetyAction::supprimer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la suppression de l\'action de sécurité.'
            ];
        }
    }
}
