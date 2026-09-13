<?php

namespace App\Policies;

use App\Models\EventType;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class EventTypesPolicy
{
    use HandlesAuthorization;

    public function viewAny(?Utilisateur $user)
    {
        if (is_null($user)) {
            return Response::deny('Requête non autorisée.');
        }

        return Response::allow();
    }

    public function view(?Utilisateur $user, EventType $eventType)
    {
        if (is_null($user)) {
            return Response::deny('Requête non autorisée.');
        }

        return Response::allow();
    }

    public function create(?Utilisateur $user)
    {
        if (is_null($user)) {
            return Response::deny('Requête non autorisée.');
        }

        return in_array($user->type_utilisateur, [
            Utilisateur::TYPE_UTILISATEUR_ADMIN,
            Utilisateur::TYPE_UTILISATEUR_POWER_USER
        ])
            ? Response::allow()
            : Response::deny('Requête non autorisée.');
    }

    public function update(?Utilisateur $user, EventType $eventType)
    {
        if (is_null($user)) {
            return Response::deny('Requête non autorisée.');
        }

        return in_array($user->type_utilisateur, [
            Utilisateur::TYPE_UTILISATEUR_ADMIN,
            Utilisateur::TYPE_UTILISATEUR_POWER_USER
        ])
            ? Response::allow()
            : Response::deny('Requête non autorisée.');
    }

    public function delete(?Utilisateur $user, EventType $eventType)
    {
        if (is_null($user)) {
            return Response::deny('Requête non autorisée.');
        }

        return in_array($user->type_utilisateur, [
            Utilisateur::TYPE_UTILISATEUR_ADMIN,
            Utilisateur::TYPE_UTILISATEUR_POWER_USER
        ])
            ? Response::allow()
            : Response::deny('Requête non autorisée.');
    }
}
