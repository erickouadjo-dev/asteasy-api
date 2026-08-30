<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Traits\BelongsToTenant;

class TasksSafety extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'TB_TASKS_SAFETY';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'ID_SAFETY_ACTION',
        'ID_ORIGINE',
        'REF_ACTION',
        'TASKS_TYPE',
        'DATE_OUVERTURE',
        'DESCRIPTION_TASK',
        'ID_PUBLICATION',
        'ID_RECURRENCE',
        'ID_STATUT',
        'ID_AVANCEMENT',
        'DATE_BUTEE',
        'DATE_FERMETURE',
        'RAPPEL_DATE_BUTEE',
        'RESPONSABLE_PROPRIETAIRE',
        'COMMENTAIRES_OBSERVATIONS',
        'FICHIERS_IMAGES',
        'ID_TAG_ETIQUETTE',
        'ENTREPRISE_ID',
        'IS_DELETE',
    ];

    protected $casts = [
        'ID_SAFETY_ACTION' => 'integer',
        'ID_ORIGINE' => 'integer',
        'ID_PUBLICATION' => 'integer',
        'ID_RECURRENCE' => 'integer',
        'ID_STATUT' => 'integer',
        'ID_AVANCEMENT' => 'integer',
        'RESPONSABLE_PROPRIETAIRE' => 'integer',
        'ID_TAG_ETIQUETTE' => 'integer',
        'ENTREPRISE_ID' => 'integer',
        'IS_DELETE' => 'boolean',
    ];

    protected $dates = ['deleted_at', 'DATE_OUVERTURE', 'DATE_BUTEE', 'DATE_FERMETURE', 'RAPPEL_DATE_BUTEE'];

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'ENTREPRISE_ID', 'ID');
    }

    public function safetyAction()
    {
        return $this->belongsTo(SafetyAction::class, 'ID_SAFETY_ACTION', 'ID');
    }

    public function typeOrigineAction()
    {
        return $this->belongsTo(TypeOrigineAction::class, 'ID_ORIGINE', 'ID');
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

    public function responsable()
    {
        return $this->belongsTo(Utilisateur::class, 'RESPONSABLE_PROPRIETAIRE', 'id');
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
                    $q->where('REF_ACTION', 'like', '%' . $search . '%')
                      ->orWhere('DESCRIPTION_TASK', 'like', '%' . $search . '%');
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
            Log::error('TasksSafety::lister a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération des tâches.'
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
                'ID_SAFETY_ACTION'         => 'nullable|integer|exists:TB_SAFETY_ACTION,ID',
                'ID_ORIGINE'               => 'nullable|integer|exists:TB_TYPE_ORIGINE_ACTION,ID',
                'REF_ACTION'               => 'required|string|max:255',
                'TASKS_TYPE'               => 'required|string|in:CURRATIF,PREVENTIF,AUTRE',
                'DATE_OUVERTURE'           => 'required|date',
                'DESCRIPTION_TASK'         => 'required|string',
                'ID_PUBLICATION'           => 'nullable|integer',
                'ID_RECURRENCE'            => 'nullable|integer|exists:TB_RECURRENCE,ID',
                'ID_STATUT'                => 'nullable|integer|exists:TB_STATUT,ID',
                'ID_AVANCEMENT'            => 'nullable|integer|exists:TB_AVANCEMENT,ID',
                'DATE_BUTEE'               => 'required|date',
                'DATE_FERMETURE'           => 'nullable|date',
                'RAPPEL_DATE_BUTEE'        => 'nullable|date',
                'RESPONSABLE_PROPRIETAIRE' => 'nullable|integer|exists:utilisateurs,id',
                'COMMENTAIRES_OBSERVATIONS'=> 'nullable|string',
                'FICHIERS_IMAGES'          => 'nullable|string',
                'ID_TAG_ETIQUETTE'         => 'nullable|integer',
                'ENTREPRISE_ID'            => 'nullable|integer|exists:TB_ENTREPRISE,ID',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $tasksSafety = new self($inputs);
            $tasksSafety->save();

            return [
                'code_http' => 201,
                'code_message' => 201,
                'data' => $tasksSafety
            ];
        } catch (\Exception $e) {
            Log::error('TasksSafety::ajouter a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la création de la tâche.'
            ];
        }
    }

    public static function recuperer($id)
    {
        try {
            $tasksSafety = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$tasksSafety) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La tâche n\'existe pas.'
                ];
            }

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $tasksSafety
            ];
        } catch (\Exception $e) {
            Log::error('TasksSafety::recuperer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération de la tâche.'
            ];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $tasksSafety = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$tasksSafety) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La tâche n\'existe pas.'
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
                'ID_SAFETY_ACTION'         => 'nullable|integer|exists:TB_SAFETY_ACTION,ID',
                'ID_ORIGINE'               => 'nullable|integer|exists:TB_TYPE_ORIGINE_ACTION,ID',
                'REF_ACTION'               => 'nullable|string|max:255',
                'TASKS_TYPE'               => 'nullable|string|in:CURRATIF,PREVENTIF,AUTRE',
                'DATE_OUVERTURE'           => 'nullable|date',
                'DESCRIPTION_TASK'         => 'nullable|string',
                'ID_PUBLICATION'           => 'nullable|integer',
                'ID_RECURRENCE'            => 'nullable|integer|exists:TB_RECURRENCE,ID',
                'ID_STATUT'                => 'nullable|integer|exists:TB_STATUT,ID',
                'ID_AVANCEMENT'            => 'nullable|integer|exists:TB_AVANCEMENT,ID',
                'DATE_BUTEE'               => 'nullable|date',
                'DATE_FERMETURE'           => 'nullable|date',
                'RAPPEL_DATE_BUTEE'        => 'nullable|date',
                'RESPONSABLE_PROPRIETAIRE' => 'nullable|integer|exists:utilisateurs,id',
                'COMMENTAIRES_OBSERVATIONS'=> 'nullable|string',
                'FICHIERS_IMAGES'          => 'nullable|string',
                'ID_TAG_ETIQUETTE'         => 'nullable|integer',
                'ENTREPRISE_ID'            => 'nullable|integer|exists:TB_ENTREPRISE,ID',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $tasksSafety->update($inputs);

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $tasksSafety
            ];
        } catch (\Exception $e) {
            Log::error('TasksSafety::modifier a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la modification de la tâche.'
            ];
        }
    }

    public static function supprimer($id)
    {
        try {
            $tasksSafety = self::find($id);

            if (!$tasksSafety) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La tâche n\'existe pas.'
                ];
            }

            $tasksSafety->IS_DELETE = true;
            $tasksSafety->save();
            $tasksSafety->delete();

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $tasksSafety
            ];
        } catch (\Exception $e) {
            Log::error('TasksSafety::supprimer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la suppression de la tâche.'
            ];
        }
    }
}
