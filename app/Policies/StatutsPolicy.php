<?php

namespace App\Policies;

use App\Models\Statut;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class StatutsPolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }

    public function viewAny(?Utilisateur $user)
    {
        if (is_null($user)) {
            return Response::deny('Requete non autorisee.');
        }

        return Response::allow();
    }

    public function view(?Utilisateur $user, Statut $statut)
    {
        if (is_null($user)) {
            return Response::deny('Requete non autorisee.');
        }

        return Response::allow();
    }

    public function create(?Utilisateur $user)
    {
        return !is_null($user) && in_array($user->type_utilisateur, [Utilisateur::TYPE_UTILISATEUR_ADMIN, Utilisateur::TYPE_UTILISATEUR_POWER_USER])
            ? Response::allow()
            : Response::deny('Requete non autorisee.');
    }

    public function update(?Utilisateur $user, Statut $statut)
    {
        return !is_null($user) && in_array($user->type_utilisateur, [Utilisateur::TYPE_UTILISATEUR_ADMIN, Utilisateur::TYPE_UTILISATEUR_POWER_USER])
            ? Response::allow()
            : Response::deny('Requete non autorisee.');
    }

    public function delete(?Utilisateur $user, Statut $statut)
    {
        return !is_null($user) && in_array($user->type_utilisateur, [Utilisateur::TYPE_UTILISATEUR_ADMIN, Utilisateur::TYPE_UTILISATEUR_POWER_USER])
            ? Response::allow()
            : Response::deny('Requete non autorisee.');
    }
}
