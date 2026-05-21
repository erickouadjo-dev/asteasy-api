<?php

namespace App\Policies;

use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UtilisateursPolicy
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
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Utilisateur  utilisateur
     * @return mixed
     */
    public function create(?Utilisateur $utilisateur)
    {
        if (
            is_null($utilisateur)
            || in_array($utilisateur->type_utilisateur, [
                Utilisateur::TYPE_UTILISATEUR_ADMIN,
                Utilisateur::TYPE_UTILISATEUR_POWER_USER,
            ])
        ) {
            return Response::allow();
        }
 
        return Response::deny('Requête non autorisée.');
    }

    public function viewAny(?Utilisateur $utilisateur)
    {
        return !is_null($utilisateur)
            && in_array($utilisateur->type_utilisateur, [
                Utilisateur::TYPE_UTILISATEUR_ADMIN,
                Utilisateur::TYPE_UTILISATEUR_POWER_USER,
            ])
            ? Response::allow()
            : Response::deny('Requête non autorisée.');
    }

}
