<?php

namespace App\Policies;

use App\Models\BaseMateriel;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class BaseMaterielsPolicy
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

    public function view(?Utilisateur $user, BaseMateriel $baseMateriel)
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

    public function update(?Utilisateur $user, BaseMateriel $baseMateriel)
    {
        return !is_null($user) && in_array($user->type_utilisateur, [Utilisateur::TYPE_UTILISATEUR_ADMIN, Utilisateur::TYPE_UTILISATEUR_POWER_USER])
            ? Response::allow()
            : Response::deny('Requete non autorisee.');
    }

    public function delete(?Utilisateur $user, BaseMateriel $baseMateriel)
    {
        return !is_null($user) && in_array($user->type_utilisateur, [Utilisateur::TYPE_UTILISATEUR_ADMIN, Utilisateur::TYPE_UTILISATEUR_POWER_USER])
            ? Response::allow()
            : Response::deny('Requete non autorisee.');
    }
}
