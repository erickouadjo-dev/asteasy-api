<?php

namespace App\Policies\Programmes;

use App\Models\Programme;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class ProgrammePolicy
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
     * @return mixed
     */
     public function update(?Utilisateur $utilisateur)
    {
        return in_array($utilisateur->role, ['DT_FER', 'DAF_FER', 'DSI_FER', 'DCG_FER','AUTRE_DGIR','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Utilisateur  $utilisateur
     * @param  \App\Models\Programme  $programme
     * @return mixed
     */
    public function view(?Utilisateur $utilisateur, Programme $programme)
    {
        return in_array($utilisateur->role, ['DT_FER', 'DAF_FER', 'DSI_FER', 'DCG_FER', 'DMC_AGEROUTE', 'DGA_AGEROUTE','COMPTABILITE_DGIR','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }
    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Utilisateur  $utilisateur
     * @param  \App\Models\Programme  $programme
     * @return mixed
     */
    public function delete(?Utilisateur $utilisateur)
    {
        return in_array($utilisateur->role, ['DT_FER','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }
}
