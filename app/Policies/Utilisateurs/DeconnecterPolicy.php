<?php

namespace App\Policies\Utilisateurs;

use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class DeconnecterPolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }

    public function create(?Utilisateur $utilisateur)
    {
        // L'utilisateur doit etre authentifie pour se deconnecter.
        return !is_null(optional($utilisateur)->id)
            ? Response::allow()
            : Response::deny('Requête non autorisée.');
    }
}
