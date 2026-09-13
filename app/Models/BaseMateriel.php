<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Traits\BelongsToTenant;

class BaseMateriel extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'TB_BASE_MATERIEL';
    protected $primaryKey = 'ID';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'BASE_ID',
        'AERONEF_ID',
        'VEHICULE_ID',
        'EQUIPEMENT_ID',
        'ENTREPRISE_ID',
        'IS_DELETE',
    ];

    protected $casts = [
        'BASE_ID' => 'integer',
        'AERONEF_ID' => 'integer',
        'VEHICULE_ID' => 'integer',
        'EQUIPEMENT_ID' => 'integer',
        'ENTREPRISE_ID' => 'integer',
        'IS_DELETE' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    public function base()
    {
        return $this->belongsTo(Base::class, 'BASE_ID', 'ID');
    }

    public function aeronef()
    {
        return $this->belongsTo(Aeronef::class, 'AERONEF_ID', 'ID');
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class, 'VEHICULE_ID', 'ID');
    }

    public function equipement()
    {
        return $this->belongsTo(Equipement::class, 'EQUIPEMENT_ID', 'ID');
    }

    public static function lister(Request $request)
    {
        try {
            $per_page = $request->input('per_page', 15);
            $page     = $request->input('page', 1);
            $search   = $request->input('search', '');

            $query = self::where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->with(['base', 'aeronef', 'vehicule', 'equipement']);

            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('base', function($sq) use ($search) {
                        $sq->where('INTITULE', 'like', '%' . $search . '%');
                    })->orWhereHas('aeronef', function($sq) use ($search) {
                        $sq->where('MARQUE', 'like', '%' . $search . '%')
                           ->orWhere('TYPE_MODELE', 'like', '%' . $search . '%')
                           ->orWhere('IMMATRICULATION', 'like', '%' . $search . '%');
                    })->orWhereHas('vehicule', function($sq) use ($search) {
                        $sq->where('MARQUE', 'like', '%' . $search . '%')
                           ->orWhere('TYPE_MODELE', 'like', '%' . $search . '%')
                           ->orWhere('IMMATRICULATION', 'like', '%' . $search . '%');
                    })->orWhereHas('equipement', function($sq) use ($search) {
                        $sq->where('MARQUE', 'like', '%' . $search . '%')
                           ->orWhere('TYPE_MODELE', 'like', '%' . $search . '%')
                           ->orWhere('IMMATRICULATION', 'like', '%' . $search . '%');
                    });
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
            Log::error('BaseMateriel::lister a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération du matériel de base.'
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
                'BASE_ID'       => 'required|integer|exists:TB_BASE,ID',
                'AERONEF_ID'    => 'nullable|integer|exists:TB_AERONEFS,ID',
                'VEHICULE_ID'   => 'nullable|integer|exists:TB_VEHICULES,ID',
                'EQUIPEMENT_ID' => 'nullable|integer|exists:TB_EQUIPEMENTS,ID',
                'ENTREPRISE_ID' => 'nullable|integer|exists:TB_ENTREPRISE,ID',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $duplicateError = self::validerNonDuplication($inputs['BASE_ID'], $inputs);
            if ($duplicateError) {
                return [
                    'code_http' => 422,
                    'code_message' => 'ERR_DUPLICATE',
                    'erreurs' => $duplicateError
                ];
            }

            $baseMateriel = new self($inputs);
            $baseMateriel->save();
            $baseMateriel->load(['base', 'aeronef', 'vehicule', 'equipement']);

            return [
                'code_http' => 201,
                'code_message' => 201,
                'data' => $baseMateriel
            ];
        } catch (\Exception $e) {
            Log::error('BaseMateriel::ajouter a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la création du matériel de base.'
            ];
        }
    }

    public static function validerNonDuplication($baseId, $inputs, $ignoreId = null)
    {
        if (empty($baseId)) {
            return null;
        }

        $query = self::where('IS_DELETE', false)
            ->whereNull('deleted_at')
            ->where('BASE_ID', $baseId);

        if ($ignoreId) {
            $query->where('ID', '!=', $ignoreId);
        }

        if (!empty($inputs['AERONEF_ID'])) {
            $aeroId = $inputs['AERONEF_ID'];
            $existsId = (clone $query)->where('AERONEF_ID', $aeroId)->exists();
            if ($existsId) {
                return 'Cet aéronef est déjà affecté à cette base.';
            }

            $aero = Aeronef::find($aeroId);
            if ($aero && !empty($aero->IMMATRICULATION)) {
                $immat = trim($aero->IMMATRICULATION);
                $immatExists = (clone $query)->whereHas('aeronef', function($q) use ($immat) {
                    $q->where('IMMATRICULATION', $immat);
                })->exists();
                if ($immatExists) {
                    return "Un aéronef avec l'immatriculation '{$immat}' est déjà affecté à cette base.";
                }
            }
        }

        if (!empty($inputs['VEHICULE_ID'])) {
            $vehId = $inputs['VEHICULE_ID'];
            $existsId = (clone $query)->where('VEHICULE_ID', $vehId)->exists();
            if ($existsId) {
                return 'Ce véhicule est déjà affecté à cette base.';
            }

            $veh = Vehicule::find($vehId);
            if ($veh && !empty($veh->IMMATRICULATION)) {
                $immat = trim($veh->IMMATRICULATION);
                $immatExists = (clone $query)->whereHas('vehicule', function($q) use ($immat) {
                    $q->where('IMMATRICULATION', $immat);
                })->exists();
                if ($immatExists) {
                    return "Un véhicule avec l'immatriculation '{$immat}' est déjà affecté à cette base.";
                }
            }
        }

        if (!empty($inputs['EQUIPEMENT_ID'])) {
            $eqId = $inputs['EQUIPEMENT_ID'];
            $existsId = (clone $query)->where('EQUIPEMENT_ID', $eqId)->exists();
            if ($existsId) {
                return 'Cet équipement est déjà affecté à cette base.';
            }

            $eq = Equipement::find($eqId);
            if ($eq && !empty($eq->IMMATRICULATION)) {
                $immat = trim($eq->IMMATRICULATION);
                $immatExists = (clone $query)->whereHas('equipement', function($q) use ($immat) {
                    $q->where('IMMATRICULATION', $immat);
                })->exists();
                if ($immatExists) {
                    return "Un équipement avec l'immatriculation '{$immat}' est déjà affecté à cette base.";
                }
            }
        }

        return null;
    }

    public static function recuperer($id)
    {
        try {
            $baseMateriel = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->with(['base', 'aeronef', 'vehicule', 'equipement'])
                ->first();

            if (!$baseMateriel) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'Le matériel de base n\'existe pas.'
                ];
            }

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $baseMateriel
            ];
        } catch (\Exception $e) {
            Log::error('BaseMateriel::recuperer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la récupération du matériel de base.'
            ];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $baseMateriel = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$baseMateriel) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'Le matériel de base n\'existe pas.'
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
                'BASE_ID'       => 'nullable|integer|exists:TB_BASE,ID',
                'AERONEF_ID'    => 'nullable|integer|exists:TB_AERONEFS,ID',
                'VEHICULE_ID'   => 'nullable|integer|exists:TB_VEHICULES,ID',
                'EQUIPEMENT_ID' => 'nullable|integer|exists:TB_EQUIPEMENTS,ID',
                'ENTREPRISE_ID' => 'nullable|integer|exists:TB_ENTREPRISE,ID',
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $targetBaseId = $inputs['BASE_ID'] ?? $baseMateriel->BASE_ID;
            $duplicateError = self::validerNonDuplication($targetBaseId, $inputs, $id);
            if ($duplicateError) {
                return [
                    'code_http' => 422,
                    'code_message' => 'ERR_DUPLICATE',
                    'erreurs' => $duplicateError
                ];
            }

            $baseMateriel->update($inputs);
            $baseMateriel->load(['base', 'aeronef', 'vehicule', 'equipement']);

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $baseMateriel
            ];
        } catch (\Exception $e) {
            Log::error('BaseMateriel::modifier a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la modification du matériel de base.'
            ];
        }
    }

    public static function supprimer($id)
    {
        try {
            $baseMateriel = self::find($id);

            if (!$baseMateriel) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'Le matériel de base n\'existe pas.'
                ];
            }

            $baseMateriel->IS_DELETE = true;
            $baseMateriel->save();
            $baseMateriel->delete();

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $baseMateriel
            ];
        } catch (\Exception $e) {
            Log::error('BaseMateriel::supprimer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la suppression du matériel de base.'
            ];
        }
    }
}
