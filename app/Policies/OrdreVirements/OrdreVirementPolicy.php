<?php

namespace App\Policies\OrdreVirements;

use App\Models\OrdreVirement;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class OrdreVirementPolicy
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
     * @param  \App\Models\OrdreVirement  $virement
     * @return mixed
     */
    public function view(?Utilisateur $utilisateur, OrdreVirement $virement)
    {
        return in_array($utilisateur->role, ['DT_FER', 'DCG_FER', 'DAF_FER', 'DG_FER','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }
}
