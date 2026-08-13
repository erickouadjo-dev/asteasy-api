<?php

namespace App\Policies;

use App\Models\TargEtiquette;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TargEtiquettesPolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }

    public function viewAny(?Utilisateur $user)
    {
        if (is_null($user)) {
            return Response::deny('Requête non autorisée.');
        }

        return Response::allow();
    }

    public function view(?Utilisateur $user, TargEtiquette $targEtiquette)
    {
        if (is_null($user)) {
            return Response::deny('Requête non autorisée.');
        }

        return Response::allow();
    }

    public function create(?Utilisateur $user)
    {
        return !is_null($user) && in_array($user->type_utilisateur, [Utilisateur::TYPE_UTILISATEUR_ADMIN, Utilisateur::TYPE_UTILISATEUR_POWER_USER])
            ? Response::allow()
            : Response::deny('Requête non autorisée.');
    }

    public function update(?Utilisateur $user, TargEtiquette $targEtiquette)
    {
        return !is_null($user) && in_array($user->type_utilisateur, [Utilisateur::TYPE_UTILISATEUR_ADMIN, Utilisateur::TYPE_UTILISATEUR_POWER_USER])
            ? Response::allow()
            : Response::deny('Requête non autorisée.');
    }

    public function delete(?Utilisateur $user, TargEtiquette $targEtiquette)
    {
        return !is_null($user) && in_array($user->type_utilisateur, [Utilisateur::TYPE_UTILISATEUR_ADMIN, Utilisateur::TYPE_UTILISATEUR_POWER_USER])
            ? Response::allow()
            : Response::deny('Requête non autorisée.');
    }
}
