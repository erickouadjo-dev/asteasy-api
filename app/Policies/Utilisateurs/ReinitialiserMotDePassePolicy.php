<?php

namespace App\Policies\Utilisateurs;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class ReinitialiserMotDePassePolicy
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
     * Determine whether the user can update a model.
     *
     * @param  \App\Models\Utilisateur  utilisateur
     * @return mixed
     */
    public function create(?Utilisateur $utilisateur)
    {
        //si l'id de l'utilisateur est null cela veut qu'il n'est pas connecté
        return is_null(optional($utilisateur)->id) ? Response::allow() : Response::deny('Requête non autorisée.');
    }


}
