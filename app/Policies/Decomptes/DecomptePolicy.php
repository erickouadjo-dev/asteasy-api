<?php

namespace App\Policies\Decomptes;

use App\Models\Decompte;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class DecomptePolicy
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
     * @param  \App\Models\Decompte  $decompte
     * @return mixed
     */
     public function update(?Utilisateur $utilisateur, Decompte $decompte)
    {
        return in_array($utilisateur->role, ['DT_FER', 'DAF_FER', 'DSI_FER', 'DCG_FER', 'DAF_FER', 'DG_FER','DMC_AGEROUTE', 'DGA_AGEROUTE', 'DAFP_AGEROUTE', 'COMPTABILITE_DGIR','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Utilisateur  $utilisateur
     * @param  \App\Models\Decompte  $decompte
     * @return mixed
     */
    public function view(?Utilisateur $utilisateur, Decompte $decompte)
    {
        return in_array($utilisateur->role, ['DT_FER', 'DAF_FER', 'DSI_FER', 'DCG_FER', 'DAF_FER', 'DG_FER', 'DMC_AGEROUTE', 'DGA_AGEROUTE', 'DAFP_AGEROUTE', 'COMPTABILITE_DGIR','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Utilisateur  $utilisateur
     * @param  \App\Models\Decompte  $decompte
     * @return mixed
     */
    public function delete(?Utilisateur $utilisateur, Decompte $decompte)
    {
        return in_array($utilisateur->role, ['DMC_AGEROUTE', 'DGA_AGEROUTE', 'DAFP_AGEROUTE', 'COMPTABILITE_DGIR','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }
}
