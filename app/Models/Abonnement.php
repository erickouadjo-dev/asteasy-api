<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Traits\BelongsToTenant;

class Abonnement extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'TB_ABONNEMENT';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'ENTREPRISE_ID',
        'PLAN_ID',
        'DATE_DEBUT',
        'DATE_FIN',
        'STATUT',
        'IS_DELETE',
    ];

    protected $casts = [
        'ENTREPRISE_ID' => 'integer',
        'PLAN_ID' => 'integer',
        'DATE_DEBUT' => 'date',
        'DATE_FIN' => 'date',
        'IS_DELETE' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'ENTREPRISE_ID', 'ID');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'PLAN_ID', 'ID');
    }

    public static function lister(Request $request)
    {
        try {
            $per_page = $request->input('per_page', 15);
            $page = $request->input('page', 1);
            $search = $request->input('search', '');

            $query = self::where('IS_DELETE', false)
                ->whereNull('deleted_at');

            if (!empty($search)) {
                $query->whereHas('entreprise', function ($q) use ($search) {
                    $q->where('NON_SOCIETE', 'like', '%' . $search . '%');
                })->orWhere('STATUT', 'like', '%' . $search . '%');
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
            Log::error('Abonnement::lister a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return ['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue lors de la récupération des abonnements.'];
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
                'ENTREPRISE_ID' => 'nullable|integer|exists:TB_ENTREPRISE,ID',
                'PLAN_ID' => 'nullable|integer|exists:TB_PLAN,ID',
                'DATE_DEBUT' => 'nullable|date',
                'DATE_FIN' => 'nullable|date',
                'STATUT' => 'nullable|string|max:50',
            ]);

            if (!$validator->passes()) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => $validator->errors()->all()];
            }

            $abonnement = new self($inputs);
            $abonnement->save();

            return ['code_http' => 201, 'code_message' => 201, 'data' => $abonnement];
        } catch (\Exception $e) {
            Log::error('Abonnement::ajouter a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return ['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue lors de la création de l\'abonnement.'];
        }
    }

    public static function recuperer($id)
    {
        try {
            $abonnement = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$abonnement) {
                return ['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'L\'abonnement n\'existe pas.'];
            }

            return ['code_http' => 200, 'code_message' => 200, 'data' => $abonnement];
        } catch (\Exception $e) {
            Log::error('Abonnement::recuperer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return ['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue lors de la récupération de l\'abonnement.'];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $abonnement = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$abonnement) {
                return ['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'L\'abonnement n\'existe pas.'];
            }

            $inputs = json_decode($request->getContent(), true);

            if (!is_array($inputs)) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => 'Corps de la requête vide.'];
            }

            $validator = Validator::make($inputs, [
                'ENTREPRISE_ID' => 'nullable|integer|exists:TB_ENTREPRISE,ID',
                'PLAN_ID' => 'nullable|integer|exists:TB_PLAN,ID',
                'DATE_DEBUT' => 'nullable|date',
                'DATE_FIN' => 'nullable|date',
                'STATUT' => 'nullable|string|max:50',
            ]);

            if (!$validator->passes()) {
                return ['code_http' => 400, 'code_message' => 'ERR_VALIDATION', 'erreurs' => $validator->errors()->all()];
            }

            $abonnement->update($inputs);

            return ['code_http' => 200, 'code_message' => 200, 'data' => $abonnement];
        } catch (\Exception $e) {
            Log::error('Abonnement::modifier a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return ['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue lors de la modification de l\'abonnement.'];
        }
    }

    public static function supprimer($id)
    {
        try {
            $abonnement = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$abonnement) {
                return ['code_http' => 404, 'code_message' => 'ERR_NOT_FOUND', 'erreurs' => 'L\'abonnement n\'existe pas.'];
            }

            $abonnement->IS_DELETE = true;
            $abonnement->save();
            $abonnement->delete();

            return ['code_http' => 200, 'code_message' => 200, 'data' => $abonnement];
        } catch (\Exception $e) {
            Log::error('Abonnement::supprimer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return ['code_http' => 500, 'code_message' => 'ERR_SERVER', 'erreurs' => 'Une erreur est survenue lors de la suppression de l\'abonnement.'];
        }
    }
}
