<?php

namespace App\Policies\Marches\AccordFinancement;

use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;
use App\Models\AccordFinancement;

class AccordFinancementPolicy
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
     * @param  \App\Models\Marche  $marche
     * @return mixed
     */
     public function update(?Utilisateur $utilisateur)
    {
        return in_array($utilisateur->role, ['DT_FER', 'DCG_FER', 'DAF_FER', 'DG_FER','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Utilisateur  $utilisateur
     * @param  \App\Models\Marche  $marche
     * @return mixed
     */
    public function view(?Utilisateur $utilisateur)
    {
      return in_array($utilisateur->role,
      [
      'DT_FER', 'DAF_FER', 'DSI_FER','DG_FER','DCG_FER','COMPTABILITE_DGIR','DMC_AGEROUTE','DGA_AGEROUTE','DAFP_DGIR','ADMIN_FER'
      ]) ? Response::allow() : Response::deny('Requête non autorisée.');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Utilisateur  $utilisateur
     * @param  \App\Models\Marche  $marche
     * @return mixed
     */
    public function delete(?Utilisateur $utilisateur)
    {
      return in_array($utilisateur->role,['DT_FER','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }
}
