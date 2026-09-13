<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Traits\BelongsToTenant;

class EventType extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'TB_EVENT_TYPE';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'CODE',
        'LIBELLE',
        'DESCRIPTION',
        'ENTREPRISE_ID',
        'IS_DELETE',
    ];

    protected $casts = [
        'ENTREPRISE_ID' => 'integer',
        'IS_DELETE'     => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'ENTREPRISE_ID', 'ID');
    }

    public function declarations()
    {
        return $this->hasMany(EventDeclaration::class, 'ID_EVENT_TYPE', 'ID');
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
                $query->where(function ($q) use ($search) {
                    $q->where('CODE', 'like', '%' . $search . '%')
                      ->orWhere('LIBELLE', 'like', '%' . $search . '%')
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
                    'to'           => $paginated->lastItem(),
                ],
            ];
        } catch (\Exception $e) {
            Log::error('EventType::lister a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http'    => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs'      => 'Une erreur est survenue lors de la récupération des types d\'événements.',
            ];
        }
    }

    public static function ajouter(Request $request)
    {
        try {
            $inputs = json_decode($request->getContent(), true);

            if (!is_array($inputs)) {
                return [
                    'code_http'    => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs'      => 'Corps de la requête vide.',
                ];
            }

            $validator = Validator::make($inputs, [
                'LIBELLE'       => 'required|string|max:255',
                'CODE'          => 'nullable|string|max:50',
                'DESCRIPTION'   => 'nullable|string',
                'ENTREPRISE_ID' => 'nullable|integer|exists:TB_ENTREPRISE,ID',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http'    => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs'      => $validator->errors()->all(),
                ];
            }

            $eventType = new self($inputs);
            $eventType->save();

            return [
                'code_http'    => 201,
                'code_message' => 201,
                'data'         => $eventType,
            ];
        } catch (\Exception $e) {
            Log::error('EventType::ajouter a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http'    => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs'      => 'Une erreur est survenue lors de la création du type d\'événement.',
            ];
        }
    }

    public static function recuperer($id)
    {
        try {
            $eventType = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$eventType) {
                return [
                    'code_http'    => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs'      => 'Le type d\'événement n\'existe pas.',
                ];
            }

            return [
                'code_http'    => 200,
                'code_message' => 200,
                'data'         => $eventType,
            ];
        } catch (\Exception $e) {
            Log::error('EventType::recuperer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http'    => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs'      => 'Une erreur est survenue lors de la récupération du type d\'événement.',
            ];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $eventType = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$eventType) {
                return [
                    'code_http'    => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs'      => 'Le type d\'événement n\'existe pas.',
                ];
            }

            $inputs = json_decode($request->getContent(), true);

            if (!is_array($inputs)) {
                return [
                    'code_http'    => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs'      => 'Corps de la requête vide.',
                ];
            }

            $validator = Validator::make($inputs, [
                'LIBELLE'       => 'sometimes|required|string|max:255',
                'CODE'          => 'nullable|string|max:50',
                'DESCRIPTION'   => 'nullable|string',
                'ENTREPRISE_ID' => 'nullable|integer|exists:TB_ENTREPRISE,ID',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http'    => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs'      => $validator->errors()->all(),
                ];
            }

            $eventType->update($inputs);

            return [
                'code_http'    => 200,
                'code_message' => 200,
                'data'         => $eventType,
            ];
        } catch (\Exception $e) {
            Log::error('EventType::modifier a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http'    => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs'      => 'Une erreur est survenue lors de la modification du type d\'événement.',
            ];
        }
    }

    public static function supprimer($id)
    {
        try {
            $eventType = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$eventType) {
                return [
                    'code_http'    => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs'      => 'Le type d\'événement n\'existe pas.',
                ];
            }

            $eventType->IS_DELETE = true;
            $eventType->save();
            $eventType->delete();

            return [
                'code_http'    => 200,
                'code_message' => 200,
                'data'         => 'Le type d\'événement a été supprimé avec succès.',
            ];
        } catch (\Exception $e) {
            Log::error('EventType::supprimer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http'    => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs'      => 'Une erreur est survenue lors de la suppression du type d\'événement.',
            ];
        }
    }
}
