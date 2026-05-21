<?php

namespace App\Policies\Transactions;

use App\Models\Transaction;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class TypesPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the account can view any models.
     *
     * @param  \App\Models\Utilisateur  utilisateur
     * @return mixed
     */
    public function viewAny(?Utilisateur $utilisateur)
    {
        return in_array($utilisateur->role, ['DT_FER', 'DCG_FER', 'DSI_FER', 'DAF_FER', 'DG_FER','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }
}
