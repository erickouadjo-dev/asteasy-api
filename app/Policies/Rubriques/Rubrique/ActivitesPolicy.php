<?php

namespace App\Policies\Rubriques\Rubrique;

use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class ActivitesPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine whether the account can view any models.
     *
     * @param  \App\Models\Utilisateur  utilisateur
     * @return mixed
     */
    public function viewAny(?Utilisateur $utilisateur)
    {
        return in_array($utilisateur->role, ['DT_FER','DCG_FER', 'DAF_FER', 'DSI_FER','AUDIT_FER','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Utilisateur  utilisateur
     * @return mixed
     */
    public function create(?Utilisateur $utilisateur)
    {
        return in_array($utilisateur->role, ['DT_FER', 'DAF_FER', 'DSI_FER','AUDIT_FER','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }


}
