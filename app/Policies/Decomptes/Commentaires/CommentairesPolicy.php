<?php

namespace App\Policies\Decomptes\Commentaires;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\Response;

class CommentairesPolicy
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
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Utilisateur
     * @return mixed
     */
    public function create(?Utilisateur $utilisateur)
    {

       return in_array($utilisateur->role, ['DT_FER','DCG_FER', 'DAF_FER', 'DSI_FER','DMC_AGEROUTE', 'DGA_AGEROUTE', 'DAFP_AGEROUTE', 'COMPTABILITE_DGIR','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');

    }
    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Utilisateur
     * @return mixed
     */
    public function viewAny(?Utilisateur $utilisateur)
    {

       return in_array($utilisateur->role, ['DT_FER','DCG_FER', 'DAF_FER', 'DSI_FER','DMC_AGEROUTE', 'DGA_AGEROUTE', 'DAFP_AGEROUTE', 'COMPTABILITE_DGIR','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');

    }
}
