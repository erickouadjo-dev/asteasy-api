<?php

namespace App\Policies\Decaissements;

use App\Models\Decaissement;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class DecaissementPolicy
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
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Utilisateur  $utilisateur
     * @param  \App\Models\Decaissement  $decaissement
     * @return mixed
     */
    public function view(?Utilisateur $utilisateur, Decaissement $decaissement)
    {
        return in_array($utilisateur->role, ['DT_FER', 'DCG_FER', 'DAF_FER', 'DG_FER','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }
}
