<?php

namespace App\Policies\PlanComptes;

use App\Models\Utilisateur;
use App\Models\PlanCompte;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class PlanComptePolicy
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
     * @param  \App\Models\Utilisateur  $utilisateur
     * @param  \App\Models\PlanCompte  $planCompte
     * @return mixed
     */
    public function update(?Utilisateur $utilisateur)
    {
        return in_array($utilisateur->role, [ 'DCG_FER', 'DAF_FER', 'ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Utilisateur  $utilisateur
     * @param  \App\Models\PlanCompte  $planCompte
     * @return mixed
     */
    public function view(?Utilisateur $utilisateur)
    {
      return in_array($utilisateur->role,
      ['DT_FER', 'DAF_FER', 'DSI_FER','DG_FER','DCG_FER','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Utilisateur  $utilisateur
     * @param  \App\Models\PlanCompte  $planCompte
     * @return mixed
     */
    public function delete(?Utilisateur $utilisateur)
    {
      return in_array($utilisateur->role,['DAF_FER', 'DCG_FER','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }
}
