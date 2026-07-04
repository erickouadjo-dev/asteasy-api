<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\HasApiTokens;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Hash;
use Config;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use App\Models\Permission;
use App\Traits\BelongsToTenant;
/*use Illuminate\Support\Facades\Mail;
use App\Mail\CreationUtilisateurMail;
use App\Mail\ReinitialiserMotDePasseMail;*/


class Utilisateur extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable, SoftDeletes, BelongsToTenant;

    protected $table = 'utilisateurs';
    protected $primaryKey = 'id';
    protected $guarded = ['updated_at'];
    protected $hidden = ['mot_de_passe'];
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'identifiant',
        'mot_de_passe',
        'telephone',
        'photo',
        'etat',
        'type_utilisateur',
        'USER_TYPE_ID',
        'ENTREPRISE_ID',
    ];
    protected $casts = [
        'USER_TYPE_ID' => 'integer',
        'ENTREPRISE_ID' => 'integer',
    ];
    protected $dates = ['deleted_at'];
    public $timestamps = true;
    public $incrementing = true;

    const ETAT_UTILISATEUR_ACTIF = 'actif';
    const ETAT_UTILISATEUR_INACTIF = 'inactif';
 
    const TYPE_UTILISATEUR_SIMPLE_USER = 'SIMPLE_USER';
    const TYPE_UTILISATEUR_POWER_USER = 'POWER_USER';
    const TYPE_UTILISATEUR_ADMIN = 'ADMIN';
    const TYPE_UTILISATEUR_AUTRE = 'AUTRE';


    public function findForPassport($username) {
        return $this->where('email', $username)->first();
    }

    public function username(){
        return $this->email;
    }

    public function getAuthPassword(){
        return $this->mot_de_passe;
    }

    //obtenir les traces d'activité d'un utilisateur
    public function traces_activites(){
        try{
            return $this->hasMany(TraceActivite::class, 'utilisateur', 'id');
        }catch(\Exception $e){
            Log::error('Utilisateur::traces_activites a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }

    /**
     * Relation avec l'employé
     */
    public function employe()
    {
        return $this->hasOne(Employe::class, 'USER_ID', 'id');
    }

    /**
     * Roles relation
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'TB_UTILISATEUR_ROLE', 'UTILISATEUR_ID', 'ROLE_ID');
    }

    /**
     * Check if utilisateur has a role
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles()->where('LIBELLE', $role)->exists();
        }
        $roleId = is_object($role) ? ($role->ID ?? null) : $role;
        return $this->roles()->where('ID', $roleId)->exists();
    }

    /**
     * Check permission via roles
     */
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            return $this->roles()->whereHas('permissions', function($q) use ($permission) {
                $q->where('LIBELLE', $permission);
            })->exists();
        }
        $permissionId = is_object($permission) ? ($permission->ID ?? null) : $permission;
        return $this->roles()->whereHas('permissions', function($q) use ($permissionId) {
            $q->where('ID', $permissionId);
        })->exists();
    }

    /**
     * Assign role to user
     */
    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('LIBELLE', $role)->first();
        }
        if ($role) {
            $this->roles()->syncWithoutDetaching([$role->ID]);
        }
    }

    public static function reinitialiserMotDePasse(Request $request)
    {
            if (!($request instanceof Request))
            {
                throw new \Exception('Instance de Illuminate\Http\Request attendue en paramètre, ' . (is_object($request) ? get_class($request) : gettype($request)) . ' trouvé.');
            }

            $result = [
                'code_http' => 200,
                'code_message' => 200
            ];

            $inputs = json_decode($request->getContent(), true);

            if(!is_array($inputs)){
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_VALIDATION';
                $result['erreurs'] = 'Corps de la requête vide.';
                return $result;
            }
             //validation des inputs
            $rules = [
                'email' => 'required|email',
            ];

            $validator = Validator::make($inputs, $rules);

            if(!$validator->passes()){
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_VALIDATION';
                $result['erreurs'] = $validator->errors()->all();
                return $result;
            }

            $utilisateur = Utilisateur::where('email', $inputs['email'])->first();

            if ($utilisateur)
            {
                // code...
                $token = $utilisateur->createToken('token')->accessToken;

                // Envoyé un mail
                /*$detail=[
                          'title'=>'Réinitialisation de mot de passe',
                          'body'=>$token];

                Mail::to($inputs['email'])->send(new ReinitialiserMotDePasseMail($detail));*/
                Mail::insertOrIgnore([
                    'classe_mailable' => 'App\Mail\ReinitialiserMotDePasseMail',
                    'parametres_mailable' => json_encode([
                        'title'=>'Réinitialisation de mot de passe',
                        'body'=>config('app.app_link').'/resetPassword/'.$token.'/'.$utilisateur->id
                    ]),
                    'destinataire' => $inputs['email'],
                    'priorite' => Mail::PRIORITE_IMMEDIATE,
                    'utilisateur' => $utilisateur->id
                ]);

                //création de trace d'activité
                TraceActivite::insertOrIgnore([
                [
                    'created_at' => now(),
                    'operation' => TraceActivite::OPERATION_MODIFICATION,
                    'description' => 'Réinitialisation de mot de passe d\'un  utilisateur',
                    'donnees' => json_encode([
                        'entrees' => ['email'=>$inputs['email']],
                        'sorties' => []
                    ]),
                    'table_cible' => 'utilisateurs',
                    'utilisateur' => optional($request->user())->id
                ]
                ]);

                //création de log
                Log::info('Réinitialisation reussie',['email'=>$inputs['email']]);

            }else{
            //error
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_EMAIL_INVALIDE';

                Log::error('Réinitialisation échoué',['email'=>$inputs['email']]);
            }
            return $result;

    }

    public static function authentifier (Request $request){
        try {

                if (!($request instanceof Request))
                {
                    throw new \Exception('Instance de Illuminate\Http\Request attendue en paramètre, ' . (is_object($request) ? get_class($request) : gettype($request)) . ' trouvé.');
                }

                $result = [
                    'code_http' => 200,
                    'code_message' => 200
                ];

                $inputs = json_decode($request->getContent(), true);

                if(!is_array($inputs)){
                    $result['code_http'] = 400;
                    $result['code_message'] = 'ERR_VALIDATION';
                    $result['erreurs'] = 'Corps de la requête vide.';
                    return $result;
                }

                //validation des inputs
                $rules = [
                    'email' => 'required|email',
                    'mot_de_passe' => 'required|string',
                ];

                $validator = Validator::make($inputs, $rules);

                if(!$validator->passes()){
                    $result['code_http'] = 400;
                    $result['code_message'] = 'ERR_VALIDATION';
                    $result['erreurs'] = $validator->errors()->all();
                    return $result;
                }
             
                $oauth_request_inputs = [
                    'grant_type'=> 'password',
                    'client_id'=> config('app.app_oauth_client_id'),
                    'client_secret'=> config('app.app_oauth_client_secret'),
                    'username'=> $inputs['email'],
                    'password'=> $inputs['mot_de_passe']
                ];

                $oauth_request = Request::create('/oauth/token', 'POST', $oauth_request_inputs);
                $oauth_request_handle = app()->handle($oauth_request);
                
                if($oauth_request_handle->status()==200) {

                    $utilisateur = Utilisateur::where('email',$inputs['email'])->first();
                
                    if($utilisateur->etat !== self::ETAT_UTILISATEUR_INACTIF){
                        //succes
                        $result['oauth'] = json_decode(app()->handle($oauth_request)->getContent(), true);

                        unset($result['oauth']['utilisateur']['email']);
                        unset($result['oauth']['utilisateur']['USER_TYPE_ID']);
                        unset($result['oauth']['utilisateur']['photo']);
                        unset($result['oauth']['utilisateur']['deleted_at']);
                        unset($result['oauth']['utilisateur']['updated_at']);
                        unset($result['oauth']['utilisateur']['created_at']);
                    
                        $id_utilisateur = 0;
                        $utilisateurs = Utilisateur::where('email',$inputs['email'])->get();
                        foreach ($utilisateurs as $utilisateur) {
                            $id_utilisateur = $utilisateur->id;
                        }

                        TraceActivite::insertOrIgnore([
                        [
                            'created_at' => now(),
                            'operation' => TraceActivite::OPERATION_AUTRE,
                            'description' => 'Authentification d\'un  utilisateur',
                            'donnees' => json_encode([
                                'entrees' => ['email'=>$inputs['email']],
                                'sorties' => []
                            ]),
                            'table_cible' => 'utilisateurs',
                            'utilisateur' => $id_utilisateur
                        ]
                        ]);

                        Log::info('Authentification reussie',['email'=>$inputs['email']]);
                    }else {
                        $result['code_http'] = 400;
                        $result['code_message'] = 'ERR_OAUTH';
                        $result['erreurs'] = 'Compte désactivé.';
                    }

                }else{
                //error
                    $result['code_http'] = 400;
                    $result['code_message'] = 'ERR_OAUTH';

                    Log::error('Authentification échouée',['email'=>$inputs['email']]);
                }
                
                return $result;

            } catch (Exception $e) {
                Log::error('Utilisateur::connexion a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);

            }
    }

    public static function deconnecter(Request $request)
    {
        try {
            if (!($request instanceof Request)) {
                throw new \Exception('Instance de Illuminate\\Http\\Request attendue en paramètre, ' . (is_object($request) ? get_class($request) : gettype($request)) . ' trouvé.');
            }

            $result = [
                'code_http' => 200,
                'code_message' => 200,
            ];

            $utilisateur = $request->user();
            if (is_null($utilisateur)) {
                return [
                    'code_http' => 401,
                    'code_message' => 'ERR_UNAUTHORIZED',
                    'erreurs' => 'Utilisateur non authentifié.'
                ];
            }

            $token = $utilisateur->token();
            if (!is_null($token)) {
                $token->revoke();
            }

            TraceActivite::insertOrIgnore([
                [
                    'created_at' => now(),
                    'operation' => TraceActivite::OPERATION_AUTRE,
                    'description' => 'Déconnexion d\'un utilisateur',
                    'donnees' => json_encode([
                        'entrees' => ['id' => $utilisateur->id],
                        'sorties' => []
                    ]),
                    'table_cible' => 'utilisateurs',
                    'utilisateur' => $utilisateur->id
                ]
            ]);

            Log::info('Déconnexion reussie', ['id' => $utilisateur->id]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Utilisateur::deconnecter a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'code_http' => 500,
                'code_message' => 'ERR_SERVER',
                'erreurs' => 'Une erreur est survenue lors de la déconnexion.'
            ];
        }
    }

    //modifier mot de passe
    public function modifierMotDePasse(Request $request)
    {
        try {

            if (!($request instanceof Request))
            {
                throw new \Exception('Instance de Illuminate\Http\Request attendue en paramètre, ' . (is_object($request) ? get_class($request) : gettype($request)) . ' trouvé.');
            }
            /*Le serveur prépare une réponse de succès avec un code HTTP 200 */
            $result = [
                'code_http' => 200,
                'code_message' => 200
            ];

            $inputs = $request->all();

            if (!is_array($inputs) || empty($inputs)) {
                $inputs = json_decode($request->getContent(), true);
            }

            if (!is_array($inputs)) {
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_VALIDATION';
                $result['erreurs'] = 'Corps de la requête vide.';
                return $result;
            }

            if (!array_key_exists('mot_de_passe', $inputs) && array_key_exists('password', $inputs)) {
                $inputs['mot_de_passe'] = $inputs['password'];
            }

            if (!array_key_exists('mot_de_passe_confirmation', $inputs) && array_key_exists('password_confirmation', $inputs)) {
                $inputs['mot_de_passe_confirmation'] = $inputs['password_confirmation'];
            }

             //validat inputs

            /*2-Le serveur valide les entrées */
            $rules = [
                'mot_de_passe' => 'required|string|min:8|confirmed',
                'photo' => 'string',
            ];

            $validator = Validator::make($inputs, $rules);
            if(!$validator->passes()){
                Log::warning('Utilisateur::modifierMotDePasse - validation echouee.', ['erreurs' => $validator->errors()->all()]);
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_VALIDATION';
                $result['erreurs'] = $validator->errors()->all();
                return $result;
            }

            if(array_key_exists('nouveau_mot_de_passe',$inputs))
            {
                /**** ****/
            }
            else
            {

            /* Le serveur hache le mot_de_passe envoyé */

                $this->mot_de_passe =  Hash::make($inputs['mot_de_passe']);

                if (array_key_exists('photo',$inputs)) {
                    // code...
                    $this->photo=$inputs['photo'];
                }

            /*Le serveur enregistre la valeur hachée du mot_de_passe  dans le compte utilisateur associée à la requête */
                $this->save();

                // Associe automatiquement un employe a l'utilisateur si absent.
                $employe = Employe::withTrashed()->where('USER_ID', $this->id)->first();
                Log::info('Vérification de l\'employé associé à l\'utilisateur #' . $this->id, ['employe' => $employe]);
                if (is_null($employe)) {
                    Employe::create([
                        'USER_ID' => $this->id,
                        'STATUT' => 'ACTIF',
                        'NATIONALITE1' => 'NON_RENSEIGNE',
                        'NATIONALITE2' => 'NON_RENSEIGNE',
                        'IS_DELETE' => false,
                    ]);
                    Log::info('Employé associé créé pour l\'utilisateur #' . $this->id);
                } else {
                    if (!is_null($employe->deleted_at)) {
                        $employe->restore();
                    }

                    if ((bool) $employe->IS_DELETE) {
                        $employe->IS_DELETE = false;
                        $employe->save();
                    }
                }
                Log::info('Modification de mot de passe réussie pour l\'utilisateur #' . $this->id);
            /*Le serveur enregistre la trace d’activité de la modification du compte (sans la valeur du mot de passe envoyé)*/
                TraceActivite::insertOrIgnore([
                [
                    'created_at' => now(),
                    'operation' => TraceActivite::OPERATION_MODIFICATION,
                    'description' => 'Modification d\'un mot de passe',
                    'donnees' => json_encode([
                    'entrees' => [],
                    'sorties' => []
                    ]),
                    'table_cible' => 'utilisateurs',
                    'utilisateur' => optional($request->user())->id ?? $this->id
                        ]
                ]);
            /*Le serveur enregistre dans les fichiers logs un message de succès de l’opération*/
                Log::info('Modification de mot de passe.', ['id'=>$this->id]);
            }

            /*Le serveur envoie la réponse*/
            return $result;

        } catch (\Exception $e) {
            Log::error('la modification a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);

        }
    }
    //ajoute un nouveau compte
    public static function ajouter(Request $request)
    {
        try{
            if (!($request instanceof Request)) {
                throw new \Exception('Instance de Illuminate\Http\Request attendue en paramètre, ' . (is_object($request) ? get_class($request) : gettype($request)) . ' trouvé.');
            }

            $result = [
                'code_http' => 201,
                'code_message' => 201
            ];

            $inputs = json_decode($request->getContent(), true);

            if(!is_array($inputs)){
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_VALIDATION';
                $result['erreurs'] = 'Corps de la requête vide.';
                return $result;
            }

            //validat inputs
            $rules = [
                'nom' => 'required|string',
                'prenom' => 'required|string',
                'email' => 'required|string|email',
                'type_utilisateur' => 'required|string|in:' . implode(',', [self::TYPE_UTILISATEUR_ADMIN,self::TYPE_UTILISATEUR_POWER_USER,self::TYPE_UTILISATEUR_SIMPLE_USER,self::TYPE_UTILISATEUR_AUTRE]),
            ];

            $validator = Validator::make($inputs, $rules);
            if(!$validator->passes()){
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_VALIDATION';
                $result['erreurs'] = $validator->errors()->all();
                return $result;
            }

            $utilisateur = Utilisateur::where('email', $inputs['email'])->first();
            if(is_null($utilisateur)){
                $nouvel_utilisateur = new Utilisateur();
                $nouvel_utilisateur->created_at = now();
                $nouvel_utilisateur->nom = $inputs['nom'];
                $nouvel_utilisateur->prenom = $inputs['prenom'];
                $nouvel_utilisateur->email = $inputs['email'];
                $nouvel_utilisateur->identifiant = $inputs['identifiant'] ?? $inputs['nom'].'.'.$inputs['prenom'];
                $nouvel_utilisateur->telephone = $inputs['telephone'] ?? null;
                $nouvel_utilisateur->type_utilisateur = $inputs['type_utilisateur'];
                $nouvel_utilisateur->USER_TYPE_ID = 1;
                $nouvel_utilisateur->save();

                $result['id'] = $nouvel_utilisateur->id;

                TraceActivite::insertOrIgnore([
                    [
                        'created_at' => now(),
                        'operation' => TraceActivite::OPERATION_AJOUT,
                        'description' => 'Création d\'un nouvel utilisateur',
                        'donnees' => json_encode([
                            'entrees' => $inputs,
                            'sorties' => ['id'=>$nouvel_utilisateur->id]
                        ]),
                        'table_cible' => 'utilisateurs',
                        'utilisateur' => optional($request->user())->id
                    ]
                ]);

                Log::info('Nouvel utilisateur crée.', ['id'=>$nouvel_utilisateur->id]);

                // Génération du token d'activation et envoi du mail de finalisation
                // isolé pour ne pas bloquer la création si Passport n'est pas configuré
                try {
                    $token = $nouvel_utilisateur->createToken('token')->accessToken;
                    Log::info('Token de création de compte généré.', ['token'=>$token]);

                    //générer le mail d'activation
                    /*$detail=[
                              'title'=>'Création de compte',
                              'body'=>$token];

                    Mail::to($inputs['email'])->send(new CreationUtilisateurMail($detail));*/
                    Mail::insertOrIgnore([
                        'classe_mailable' => 'App\Mail\CreationUtilisateurMail',
                        'parametres_mailable' => json_encode([
                            'title'=>'Création de compte',
                            'body'=>config('app.app_link').'/resetPassword/'.$token.'/'.$nouvel_utilisateur->id
                        ]),
                        'destinataire' => $inputs['email'],
                        'priorite' => Mail::PRIORITE_IMMEDIATE,
                        'utilisateur' => optional($request->user())->id
                    ]);
                } catch (\Exception $e) {
                    Log::error('Utilisateur::ajouter - génération du token/mail d\'activation échouée pour l\'utilisateur ' . $nouvel_utilisateur->id . ' : ' . $e->getMessage());
                }
            }else{
                Log::info('Erreur d\'ajout d\'un utilisateur avec email utilisé.' , ['entrees' => $inputs]);
                $result['code_http'] = 400;
                $result['code_message'] = 'ERR_EMAIL_UTILISE';
            }

            return $result;
        }catch(\Exception $e){
            Log::error('Utilisateur::ajouter a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }

    public static function lister(Request $request)
    {
      try{
          if (!($request instanceof Request)) {
              throw new \Exception('Instance de Illuminate\Http\Request attendue en paramètre, ' . (is_object($request) ? get_class($request) : gettype($request)) . ' trouvé.');
          }

          $result = [
              'code_http' => 200,
              'code_message' => 200,
              'donnees' => [],
              'url' => ""
          ];

          $limite = $request->query('limite', 50);
          $avant = $request->query('avant', null);
          $apres = $request->query('apres', null);
          $filtres = json_decode($request->query('filtres', null));

          // Elément de tri et filtre
          $libelle = $request->query('libelle', null);
          $tri = $request->query('tri', 'utilisateurs.id');
          $order= $request->query('order', null);
          $spec= explode('.',$tri);

          $selection = Utilisateur::select(
                                            'utilisateurs.id AS id_user', 
                                            'utilisateurs.created_at AS date_user', 
                                            'utilisateurs.nom AS nom_user', 
                                            'utilisateurs.prenom AS prenom_user', 
                                            'utilisateurs.email AS email_user',
                                            'utilisateurs.identifiant AS identifiant_user',
                                            'utilisateurs.telephone AS telephone_user',
                                            'utilisateurs.photo AS photo_user',
                                            'utilisateurs.type_utilisateur AS type_user', 
                                            'utilisateurs.etat AS etat_user');

        if (is_null($avant) && is_null($apres)) {
            $selection->orderBy('utilisateurs.id', 'desc');
        }
        else
        {
            if(!is_null($avant)){
                $selection->where('utilisateurs.id', '>', base64_decode($avant))->orderBy('utilisateurs.id', 'asc');
            }
            else{
                $selection->where('utilisateurs.id', '<', base64_decode($apres))->orderBy('utilisateurs.id', 'desc');
            }

        }

        if (!is_null($filtres)) {
            foreach ($filtres as $filtre) {
                if ($filtre->type == "caractere") {
                    Filtre::req_caractere($selection,$filtre);
                }elseif ($filtre->type == "numeric") {
                    Filtre::req_numeric($selection,$filtre);
                }elseif ($filtre->type == "date") {
                    Filtre::req_date($selection,$filtre);
                }
            }
        }

        $utilisateurs = $selection->get();

        //création du csv

        if(count($utilisateurs)){
            //données
            foreach($utilisateurs as $utilisateur){
                $result['donnees'][] = [
                    'id' => $utilisateur->id_user,
                    'date' => substr($utilisateur->date_user, 0, 10),
                    'nom' => $utilisateur->nom_user,
                    'prenom' => $utilisateur->prenom_user,
                    'email' => $utilisateur->email_user,
                    'identifiant' => $utilisateur->identifiant_user,
                    'telephone' => $utilisateur->telephone_user,
                    'photo' => $utilisateur->photo_user,
                    'type' => $utilisateur->type_user,
                    'etat' => $utilisateur->etat_user,
                ];
            }

            //pagination
            $result['pagination'] = [];

            if(count($result['donnees']) > $limite) {
                if(!is_null($avant) && !is_null($apres)){
                    $result['donnees'] = array_slice($result['donnees'], 0, $limite);
                    $apres = $result['donnees'][count($result['donnees']) - 1]['id'];
                    $result['pagination']['curseurs'] = [
                        'apres' => base64_encode($apres)
                    ];
                }else {
                    if (!is_null($avant)) {
                        $result['donnees'] = array_slice($result['donnees'], 0, $limite);
                        $result['donnees'] = array_reverse($result['donnees']);
                        $avant = $result['donnees'][0]['id'];
                        $apres = $result['donnees'][count($result['donnees']) - 1]['id'];
                        $result['pagination']['curseurs'] = [
                            'apres' => base64_encode($apres),
                            'avant' => base64_encode($avant)
                        ];
                    } else {
                        if (!is_null($apres)) {
                            $result['donnees'] = array_slice($result['donnees'], 0, $limite);
                            $avant = $result['donnees'][0]['id'];
                            $apres = $result['donnees'][count($result['donnees']) - 1]['id'];
                            $result['pagination']['curseurs'] = [
                                'apres' => base64_encode($apres),
                                'avant' => base64_encode($avant)
                            ];
                        }else{
                            $result['donnees'] = array_slice($result['donnees'], 0, $limite);
                            $apres = $result['donnees'][count($result['donnees']) - 1]['id'];
                            $result['pagination']['curseurs'] = [
                                'apres' => base64_encode($apres)
                            ];
                        }
                    }
                }
            }else{
                if(!is_null($apres)){
                    $avant = $result['donnees'][0]['id'];
                    $result['pagination']['curseurs'] = [
                        'avant' => base64_encode($avant)
                    ];
                }elseif(!is_null($avant)){
                    $result['donnees'] = array_reverse($result['donnees']);
                    $apres = $result['donnees'][count($result['donnees']) - 1]['id'];
                    $result['pagination']['curseurs'] = [
                        'apres' => base64_encode($apres)
                    ];
                }
            }

            $parametres_url_suivante = [
                'limite' => $limite
            ];
            $parametres_url_precedente = [
                'limite' => $limite
            ];

            if(isset($result['pagination']['curseurs']['apres'])) {
                $parametres_url_suivante['apres'] = $result['pagination']['curseurs']['apres'];
                $result['pagination']['suivant'] = sprintf('/utilisateurs?%s', http_build_query($parametres_url_suivante));
            }
            if(isset($result['pagination']['curseurs']['avant'])) {
                $parametres_url_precedente['avant'] = $result['pagination']['curseurs']['avant'];
                $result['pagination']['precedent'] = sprintf('/utilisateurs?%s', http_build_query($parametres_url_precedente));
            }

            if(empty($result['pagination'])){
                unset($result['pagination']);
            }
        }

        TraceActivite::insertOrIgnore([
            [
                'created_at' => now(),
                'operation' => TraceActivite::OPERATION_LECTURE,
                'description' => 'Lecture des utilisateurs',
                'donnees' => json_encode([
                    'entrees' => [],
                    'sorties' => ['donnees'=>$result['donnees']]
                ]),
                'table_cible' => 'utilisateurs',
                'utilisateur' => $request->user()->id
            ]
        ]);

          return $result;
      }catch(\Exception $e){
          Log::error('util$utilisateur::lister a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
      }
    }
  
   //lire un marché
   public function lire(Request $request)
   {
    try{
        if (!($request instanceof Request)) {
            throw new \Exception('Instance de Illuminate\Http\Request attendue en paramètre, ' . (is_object($request) ? get_class($request) : gettype($request)) . ' trouvé.');
        }

        $result = [
            'code_http' => 200,
            'code_message' => 200,
            'utilisateur' => []
        ];

        $selection = Utilisateur::select('utilisateurs.id AS id_user',
                                            'utilisateurs.created_at AS date_user', 
                                            'utilisateurs.nom AS nom_user', 
                                            'utilisateurs.email AS email_user',
                                            'utilisateurs.type_utilisateur AS type_user', 
                                            'utilisateurs.etat')->where('utilisateurs.id', $this->id);

        $utilisateurs = $selection->get();

        if(count($utilisateurs)){
            foreach($utilisateurs as $utilisateur){
              $result['utilisateur'][] = [
                'id' => $utilisateur->id_user,
                'nom' => $utilisateur->nom_user,
                'email' => $utilisateur->email_user,
                'type' => strval($utilisateur->type_user),
                'date' => $utilisateur->date_user,
                'etat' => $utilisateur->etat,
              ];
            }
        }

        if(count($result['utilisateur'])==1){
            $result['utilisateur'] = $result['utilisateur'][0];

            TraceActivite::insertOrIgnore([
                [
                    'created_at' => now(),
                    'operation' => TraceActivite::OPERATION_LECTURE,
                    'description' => 'Lecture d\'un utilisateur',
                    'donnees' => json_encode([
                        'entrees' => ['id' => $this->id],
                        'sorties' => ['utilisateur'=>$result['utilisateur']]
                    ]),
                    'table_cible' => 'utilisateurs',
                    'utilisateur' => $request->user()->id
                ]
            ]);
        }else{
            Log::info('Erreur de lecture d\'un utilisateur avec un identifiant invalide.' , ['id' => $this->id]);
            $result['code_http'] = 400;
            $result['code_message'] = 'ERR_UTILISATEUR_NON_TROUVE';
        }

        return $result;
    }catch(\Exception $e){
        Log::error('Utilisateur::lire a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
    }
   }

  //modifier un utilisateur
  public function modifier(Request $request)
  {
    try {
        if (!($request instanceof Request)){
            throw new \Exception('Instance de Illuminate\Http\Request attendue en paramètre, ' . (is_object($request) ? get_class($request) : gettype($request)) . ' trouvé.');
        }

        $result = [
            'code_http' => 200,
            'code_message' => 200
        ];

        $inputs = json_decode($request->getContent(), true);
        if(!is_array($inputs)){
            $result['code_http'] = 400;
            $result['code_message'] = 'ERR_VALIDATION';
            $result['erreurs'] = 'Corps de la requête vide.';
            return $result;
        }

        if(empty($inputs)){
            $result['code_http'] = 400;
            $result['code_message'] = 'ERR_VALIDATION';
            $result['erreurs'] = 'Corps de la requête vide.';
            return $result;
        }

        $rules = [
            'nom' => 'string|max:100',
            'email' => 'string|max:100',
            'type' => 'string|max:50',
        ];

        $validator = Validator::make($inputs, $rules);
        if(!$validator->passes()){
            $result['code_http'] = 400;
            $result['code_message'] = 'ERR_VALIDATION';
            $result['erreurs'] = $validator->errors()->all();
            return $result;
        }

        $utilisateur = Utilisateur::where('email', $inputs['email'])
            ->where('id', '<>', $this->id)
            ->first();

        if(is_null($utilisateur)){
            if (array_key_exists('nom', $inputs)) {
                $this->nom = $inputs['nom'];
            }
            if (array_key_exists('email', $inputs)) {
                $this->email = $inputs['email'];
            }
            if (array_key_exists('type_utilisateur', $inputs)) {
                $this->type_utilisateur = $inputs['type_utilisateur'];
            }
            $this->save();

            TraceActivite::insertOrIgnore([
                [
                    'created_at' => now(),
                    'operation' => TraceActivite::OPERATION_MODIFICATION,
                    'description' => 'Modification d\'un utilisateur',
                    'donnees' => json_encode([
                        'entrees' => $inputs,
                        'sorties' => ['id' => $this->id]
                    ]),
                    'table_cible' => 'marches',
                    'utilisateur' => $request->user()->id
                ]
            ]);

            Log::info('Utilisateur modifié.', ['id' => $this->id]);
        }else{
            Log::info('Erreur de modification d\'un utilisateur avec un email déjà utilisé.' , ['entrees' => $inputs]);
            $result['code_http'] = 400;
            $result['code_message'] = 'ERR_EMAIL_UTILISE';
        }

        return $result;
    } catch (\Exception $e) {
        Log::error('Utilisateur::modifier a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
    }
    }

}
