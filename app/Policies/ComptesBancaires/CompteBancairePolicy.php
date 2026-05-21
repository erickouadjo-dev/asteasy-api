<?php

namespace App\Policies\ComptesBancaires;

use App\Models\CompteBancaire;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CompteBancairePolicy
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
     * @param  \App\Models\CompteBancaire  $compteBancaire
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(?Utilisateur $utilisateur, CompteBancaire $compteBancaire)
    {
        return in_array($utilisateur->role, ['ADMIN_FER','DAF_FER','DCG_FER','DT_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }

    /**
     * Determine whether the user can update models.
     *
     * @param  \App\Models\Utilisateur  $utilisateur
     * @param  \App\Models\CompteBancaire  $compteBancaire
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(?Utilisateur $utilisateur, CompteBancaire $compteBancaire)
    {
        return in_array($utilisateur->role, ['ADMIN_FER','DAF_FER','DCG_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }

    /**
     * Determine whether the user can update models.
     *
     * @param  \App\Models\Utilisateur  $utilisateur
     * @param  \App\Models\CompteBancaire  $compteBancaire
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(?Utilisateur $utilisateur, CompteBancaire $compteBancaire)
    {
        return in_array($utilisateur->role, ['ADMIN_FER','DAF_FER','DCG_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }
}
