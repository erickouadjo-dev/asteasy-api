<?php

namespace App\Policies;

use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class BilanComptablesPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Utilisateur  utilisateur
     * @return mixed
     */
    public function create(?Utilisateur $utilisateur)
    {
        return in_array($utilisateur->role, ['DT_FER','DAF_FER', 'DCG_FER','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }

    /**
     * Determine whether the account can view any models.
     *
     * @param  \App\Models\Utilisateur  utilisateur
     * @return mixed
     */
    public function viewAny(?Utilisateur $utilisateur)
    {
        return in_array($utilisateur->role, ['DT_FER', 'DAF_FER', 'DSI_FER', 'DCG_FER','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }
}
