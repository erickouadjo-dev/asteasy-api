<?php

namespace App\Policies\Banques;

use App\Models\Banque;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class BanquePolicy
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
     * Determine whether the user can update models.
     *
     * @param  \App\Models\Utilisateur  $utilisateur
     * @param  \App\Models\Banque  $banque
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(?Utilisateur $utilisateur, Banque $banque)
    {
        return in_array($utilisateur->role, ['ADMIN_FER','DAF_FER','DCG_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }

    /**
     * Determine whether the user can update models.
     *
     * @param  \App\Models\Utilisateur  $utilisateur
     * @param  \App\Models\Banque  $banque
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(?Utilisateur $utilisateur, Banque $banque)
    {
        return in_array($utilisateur->role, ['ADMIN_FER','DAF_FER','DCG_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }
}
