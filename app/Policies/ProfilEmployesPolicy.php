<?php

namespace App\Policies;

use App\Models\ProfilEmploye;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ProfilEmployesPolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }

    /**
     * Determine whether the user can view any profils.
     */
    public function viewAny(?Utilisateur $user)
    {
        if (is_null($user)) {
            return Response::deny('Requête non autorisée.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can view the profil.
     */
    public function view(?Utilisateur $user, ProfilEmploye $profilEmploye)
    {
        if (is_null($user)) {
            return Response::deny('Requête non autorisée.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can create profils.
     */
    public function create(?Utilisateur $user)
    {
        return !is_null($user) && $user->type_utilisateur === Utilisateur::TYPE_UTILISATEUR_ADMIN
            ? Response::allow()
            : Response::deny('Requête non autorisée.');
    }

    /**
     * Determine whether the user can update the profil.
     */
    public function update(?Utilisateur $user, ProfilEmploye $profilEmploye)
    {
        return !is_null($user) && $user->type_utilisateur === Utilisateur::TYPE_UTILISATEUR_ADMIN
            ? Response::allow()
            : Response::deny('Requête non autorisée.');
    }

    /**
     * Determine whether the user can delete the profil.
     */
    public function delete(?Utilisateur $user, ProfilEmploye $profilEmploye)
    {
        return !is_null($user) && $user->type_utilisateur === Utilisateur::TYPE_UTILISATEUR_ADMIN
            ? Response::allow()
            : Response::deny('Requête non autorisée.');
    }
}
