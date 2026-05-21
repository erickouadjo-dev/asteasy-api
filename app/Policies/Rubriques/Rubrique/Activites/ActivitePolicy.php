<?php

namespace App\Policies\Rubriques\Rubrique\Activites;

use App\Models\Activite;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActivitePolicy
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
     * @param  \App\Models\Activite  $activite
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(?Utilisateur $utilisateur, Activite $activite)
    {
        return in_array($utilisateur->role, ['ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }
     
    /**
     * Determine whether the user can update models.
     *
     * @param  \App\Models\Utilisateur  $utilisateur
     * @param  \App\Models\Activite  $activite
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(?Utilisateur $utilisateur, Activite $activite)
    {
        return in_array($utilisateur->role, ['ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }
}
