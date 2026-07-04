<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Traits\BelongsToTenant;

class Employe extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'TB_EMPLOYE';
    protected $primaryKey = 'ID';
    protected $guarded = ['MODIFICATION'];
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'IS_DELETE',
        'USER_ID',
        'PHONE_WHATSAPP',
        'PHONE2',
        'WHATSAPP',
        'E-MAIL',
        'ADRESSE1',
        'ADRESSE2',
        'ADRESSE3',
        'CODE POSTAL',
        'VILLE',
        'PAYS',
        'DATE_EMBAUCHE',
        'DATE_FIN_CONTRAT',
        'STATUT',
        'NATIONALITE1',
        'FICHIER_PHOTO_PASSEPORT1',
        'NATIONALITE2',
        'FICHIER_PHOTO_NATIONALITE2',
        'GROUPE_SANGUIN',
        'PROFIL_EMPLOYE_ID',
        'URG_NOM',
        'URG_PRENOM',
        'URG_LIEN_PARENTE',
        'URG_TEL1',
        'URG_TEL2',
        'URG_EMAIL',
        'MODIFICATION',
        'ENTREPRISE_ID'
    ];

    protected $casts = [
        'IS_DELETE' => 'boolean',
        'USER_ID' => 'integer',
        'PROFIL_EMPLOYE_ID' => 'integer',
        'ENTREPRISE_ID' => 'integer',
        'DATE_EMBAUCHE' => 'datetime',
        'DATE_FIN_CONTRAT' => 'datetime',
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
                $query->where('PHONE_WHATSAPP', 'like', '%' . $search . '%')
                    ->orWhere('VILLE', 'like', '%' . $search . '%')
                    ->orWhere('PAYS', 'like', '%' . $search . '%')
                    ->orWhere('NATIONALITE1', 'like', '%' . $search . '%')
                    ->orWhere('NATIONALITE2', 'like', '%' . $search . '%');
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
            Log::error('Employe::lister a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la recuperation des employes.'
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
                'IS_DELETE' => 'nullable|boolean',
                'PHONE_WHATSAPP' => 'nullable|string|max:255',
                'PHONE2' => 'nullable|string|max:255',
                'WHATSAPP' => 'nullable|string|max:255',
                'E-MAIL' => 'nullable|email|max:255',
                'ADRESSE1' => 'nullable|string|max:255',
                'ADRESSE2' => 'nullable|string|max:255',
                'ADRESSE3' => 'nullable|string|max:255',
                'CODE POSTAL' => 'nullable|string|max:255',
                'VILLE' => 'nullable|string|max:255',
                'PAYS' => 'nullable|string|max:255',
                'DATE_EMBAUCHE' => 'nullable|date',
                'DATE_FIN_CONTRAT' => 'nullable|date',
                'STATUT' => 'required|in:ACTIF,NON_ACTIF',
                'NATIONALITE1' => 'required|string|max:255',
                'FICHIER_PHOTO_PASSEPORT1' => 'nullable|string',
                'NATIONALITE2' => 'required|string|max:255',
                'FICHIER_PHOTO_NATIONALITE2' => 'nullable|string',
                'GROUPE_SANGUIN' => 'nullable|string|max:255',
                'URG_NOM' => 'nullable|string|max:255',
                'URG_PRENOM' => 'nullable|string|max:255',
                'URG_LIEN_PARENTE' => 'nullable|string|max:255',
                'URG_TEL1' => 'nullable|string|max:255',
                'URG_TEL2' => 'nullable|string|max:255',
                'URG_EMAIL' => 'nullable|email|max:255',
                'MODIFICATION' => 'nullable|string|max:255',
                'USER_ID' => 'nullable|integer|exists:utilisateurs,id',
                'PROFIL_EMPLOYE_ID' => 'nullable|integer|exists:TB_PROFIL_EMPLOYE,ID',
                'ENTREPRISE_ID' => 'nullable|integer|exists:TB_ENTREPRISE,ID'
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $employe = new self($inputs);
            $employe->save();

            return [
                'code_http' => 201,
                'code_message' => 201,
                'data' => $employe
            ];
        } catch (\Exception $e) {
            Log::error('Employe::ajouter a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la creation de l\'employe.'
            ];
        }
    }

    public static function recuperer($id)
    {
        try {
            $employe = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$employe) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'employe n\'existe pas.'
                ];
            }

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $employe
            ];
        } catch (\Exception $e) {
            Log::error('Employe::recuperer a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la recuperation de l\'employe.'
            ];
        }
    }

    public static function modifier(Request $request, $id)
    {
        try {
            $employe = self::where('ID', $id)
                ->where('IS_DELETE', false)
                ->whereNull('deleted_at')
                ->first();

            if (!$employe) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'employe n\'existe pas.'
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
                'IS_DELETE' => 'nullable|boolean',
                'USER_ID' => 'nullable|integer|exists:utilisateurs,id',
                'PHONE_WHATSAPP' => 'nullable|string|max:255',
                'PHONE2' => 'nullable|string|max:255',
                'WHATSAPP' => 'nullable|string|max:255',
                'E-MAIL' => 'nullable|email|max:255',
                'ADRESSE1' => 'nullable|string|max:255',
                'ADRESSE2' => 'nullable|string|max:255',
                'ADRESSE3' => 'nullable|string|max:255',
                'CODE POSTAL' => 'nullable|string|max:255',
                'VILLE' => 'nullable|string|max:255',
                'PAYS' => 'nullable|string|max:255',
                'DATE_EMBAUCHE' => 'nullable|date',
                'DATE_FIN_CONTRAT' => 'nullable|date',
                'STATUT' => 'nullable|in:ACTIF,NON_ACTIF',
                'NATIONALITE1' => 'nullable|string|max:255',
                'FICHIER_PHOTO_PASSEPORT1' => 'nullable|string',
                'NATIONALITE2' => 'nullable|string|max:255',
                'FICHIER_PHOTO_NATIONALITE2' => 'nullable|string',
                'GROUPE_SANGUIN' => 'nullable|string|max:255',
                'PROFIL_EMPLOYE_ID' => 'nullable|integer|exists:TB_PROFIL_EMPLOYE,ID',
                'URG_NOM' => 'nullable|string|max:255',
                'URG_PRENOM' => 'nullable|string|max:255',
                'URG_LIEN_PARENTE' => 'nullable|string|max:255',
                'URG_TEL1' => 'nullable|string|max:255',
                'URG_TEL2' => 'nullable|string|max:255',
                'URG_EMAIL' => 'nullable|email|max:255',
                'MODIFICATION' => 'nullable|string|max:255',
                'ENTREPRISE_ID' => 'nullable|integer|exists:TB_ENTREPRISE,ID'
            ]);

            if (!$validator->passes()) {
                return [
                    'code_http' => 400,
                    'code_message' => 'ERR_VALIDATION',
                    'erreurs' => $validator->errors()->all()
                ];
            }

            $employe->update($inputs);

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $employe
            ];
        } catch (\Exception $e) {
            Log::error('Employe::modifier a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la modification de l\'employe.'
            ];
        }
    }

    public static function supprimer($id)
    {
        try {
            $employe = self::find($id);

            if (!$employe) {
                return [
                    'code_http' => 404,
                    'code_message' => 'ERR_NOT_FOUND',
                    'erreurs' => 'L\'employe n\'existe pas.'
                ];
            }

            $employe->IS_DELETE = true;
            $employe->save();
            $employe->delete();

            return [
                'code_http' => 200,
                'code_message' => 200,
                'data' => $employe
            ];
        } catch (\Exception $e) {
            Log::error('Employe::supprimer a echoue avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la suppression de l\'employe.'
            ];
        }
    }

    /**
     * Relation avec l'utilisateur
     */
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'USER_ID', 'id');
    }

    /**
     * Relation avec l'entreprise
     */
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'ENTREPRISE_ID', 'ID');
    }

    /**
     * Relation avec le profil employé
     */
    public function profilEmploye()
    {
        return $this->belongsTo(ProfilEmploye::class, 'PROFIL_EMPLOYE_ID', 'ID');
    }
}
