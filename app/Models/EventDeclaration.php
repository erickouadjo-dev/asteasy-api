<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EventDeclaration extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'TB_EVENT_DECLARATION';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'REF_EVENT',
        'TYPE_EVENT',
        'CONFIDENTIEL',
        'RAPORTEUR',
        'BASE_OPERATEUR',
        'DATE_EVENT',
        'HEURE_EVENT',
        'CLIENT_MISSION',
        'ID_BASE_MATERIEL',
        'EVENT_LOCALISATION',
        'GPS_POSITION',
        'EVENT_DESCRIPTION',
        'FICHIERS_IMAGES',
        'IS_DELETE',
    ];

    protected $casts = [
        'RAPORTEUR' => 'integer',
        'ID_BASE_MATERIEL' => 'integer',
        'IS_DELETE' => 'boolean',
    ];

    protected $dates = ['deleted_at', 'DATE_EVENT'];

    public function raporteurUser()
    {
        return $this->belongsTo(Utilisateur::class, 'RAPORTEUR', 'id');
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
                    $q->where('REF_EVENT', 'like', '%' . $search . '%')
                      ->orWhere('EVENT_DESCRIPTION', 'like', '%' . $search . '%');
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
            Log::error('EventDeclaration::lister a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération des déclarations d\'événements.'
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
                'REF_EVENT'          => 'required|string|max:255',
                'TYPE_EVENT'         => 'required|string|max:255',
                'CONFIDENTIEL'       => 'required|string|in:OUI,NON',
                'RAPORTEUR'          => 'nullable|integer|exists:utilisateurs,id',
                'BASE_OPERATEUR'     => 'nullable|string|max:255',
                'DATE_EVENT'         => 'required|date',
                'HEURE_EVENT'        => 'required',
                'CLIENT_MISSION'     => 'nullable|string|max:255',
                'ID_BASE_MATERIEL'   => 'nullable|integer',
                'EVENT_LOCALISATION' => 'required|string|max:255',
                'GPS_POSITION'       => 'nullable|string|max:255',
                'EVENT_DESCRIPTION'  => 'required|string',
                'FICHIERS_IMAGES'    => 'nullable|string',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $eventDeclaration = new self($inputs);
            $eventDeclaration->save();

            return [
                'code_http' => 201,
                'code_message' => 201,
                'data' => $eventDeclaration
            ];
        } catch (\Exception $e) {
            Log::error('EventDeclaration::ajouter a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la création de la déclaration d\'événement.'
            ];
        }
    }

    public static function recuperer($id)
    {
        try {
            $eventDeclaration = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$eventDeclaration) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La déclaration d\'événement n\'existe pas.'
                ];
            }

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $eventDeclaration
            ];
        } catch (\Exception $e) {
            Log::error('EventDeclaration::recuperer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération de la déclaration d\'événement.'
            ];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $eventDeclaration = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$eventDeclaration) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La déclaration d\'événement n\'existe pas.'
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
                'REF_EVENT'          => 'nullable|string|max:255',
                'TYPE_EVENT'         => 'nullable|string|max:255',
                'CONFIDENTIEL'       => 'nullable|string|in:OUI,NON',
                'RAPORTEUR'          => 'nullable|integer|exists:utilisateurs,id',
                'BASE_OPERATEUR'     => 'nullable|string|max:255',
                'DATE_EVENT'         => 'nullable|date',
                'HEURE_EVENT'        => 'nullable',
                'CLIENT_MISSION'     => 'nullable|string|max:255',
                'ID_BASE_MATERIEL'   => 'nullable|integer',
                'EVENT_LOCALISATION' => 'nullable|string|max:255',
                'GPS_POSITION'       => 'nullable|string|max:255',
                'EVENT_DESCRIPTION'  => 'nullable|string',
                'FICHIERS_IMAGES'    => 'nullable|string',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $eventDeclaration->update($inputs);

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $eventDeclaration
            ];
        } catch (\Exception $e) {
            Log::error('EventDeclaration::modifier a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la modification de la déclaration d\'événement.'
            ];
        }
    }

    public static function supprimer($id)
    {
        try {
            $eventDeclaration = self::find($id);

            if (!$eventDeclaration) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'La déclaration d\'événement n\'existe pas.'
                ];
            }

            $eventDeclaration->IS_DELETE = true;
            $eventDeclaration->save();
            $eventDeclaration->delete();

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $eventDeclaration
            ];
        } catch (\Exception $e) {
            Log::error('EventDeclaration::supprimer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la suppression de la déclaration d\'événement.'
            ];
        }
    }
}
