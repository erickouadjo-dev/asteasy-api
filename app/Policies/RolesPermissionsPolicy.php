<?php

namespace App\Policies;

use App\Models\RolePermission;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class RolesPermissionsPolicy
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

    public function view(?Utilisateur $user, RolePermission $rolePermission)
    {
        if (is_null($user)) {
            return Response::deny('Requête non autorisée.');
        }

        return Response::allow();
    }

    public function create(?Utilisateur $user)
    {
        return !is_null($user) && $user->type_utilisateur === Utilisateur::TYPE_UTILISATEUR_ADMIN
            ? Response::allow()
            : Response::deny('Requête non autorisée.');
    }

    public function update(?Utilisateur $user, RolePermission $rolePermission)
    {
        return !is_null($user) && $user->type_utilisateur === Utilisateur::TYPE_UTILISATEUR_ADMIN
            ? Response::allow()
            : Response::deny('Requête non autorisée.');
    }

    public function delete(?Utilisateur $user, RolePermission $rolePermission)
    {
        return !is_null($user) && $user->type_utilisateur === Utilisateur::TYPE_UTILISATEUR_ADMIN
            ? Response::allow()
            : Response::deny('Requête non autorisée.');
    }
}