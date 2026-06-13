<?php

namespace App\Policies;

use App\Models\Formation;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class FormationsPolicy
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

        return $this->can($user, 'viewAny')
            ? Response::allow()
            : Response::deny('Requête non autorisée.');
    }

    public function view(?Utilisateur $user, Formation $formation)
    {
        if (is_null($user)) {
            return Response::deny('Requête non autorisée.');
        }

        return $this->can($user, 'view')
            ? Response::allow()
            : Response::deny('Requête non autorisée.');
    }

    public function create(?Utilisateur $user)
    {
        if (is_null($user)) {
            return Response::deny('Requête non autorisée.');
        }

        return $this->can($user, 'create')
            ? Response::allow()
            : Response::deny('Requête non autorisée.');
    }

    public function update(?Utilisateur $user, Formation $formation)
    {
        if (is_null($user)) {
            return Response::deny('Requête non autorisée.');
        }

        return $this->can($user, 'update')
            ? Response::allow()
            : Response::deny('Requête non autorisée.');
    }

    public function delete(?Utilisateur $user, Formation $formation)
    {
        if (is_null($user)) {
            return Response::deny('Requête non autorisée.');
        }

        return $this->can($user, 'delete')
            ? Response::allow()
            : Response::deny('Requête non autorisée.');
    }

    private function can(Utilisateur $user, string $ability): bool
    {
        if ($user->hasRole('ADMIN')) {
            return true;
        }

        $permissionLabels = [
            "formation.{$ability}",
            "formation_resource.{$ability}",
            "formations.{$ability}",
            "formations_resource.{$ability}",
        ];

        foreach ($permissionLabels as $permissionLabel) {
            if ($user->hasPermission($permissionLabel)) {
                return true;
            }
        }

        return false;
    }
}
