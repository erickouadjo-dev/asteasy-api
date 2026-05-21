<?php

namespace App\Policies\DecomptesPartiels;

use App\Models\Utilisateur;
use App\Models\DecomptePartiel;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class DecomptePartielPolicy
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
     public function update(?Utilisateur $utilisateur, DecomptePartiel $decomptePartiel)
    {
        return in_array($utilisateur->role, ['DMC_AGEROUTE', 'DGA_AGEROUTE', 'DAFP_AGEROUTE', 'COMPTABILITE_DGIR','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Utilisateur  $utilisateur
     * @return mixed
     */
    public function view(?Utilisateur $utilisateur, DecomptePartiel $decomptePartiel)
    {
        return in_array($utilisateur->role, ['DMC_AGEROUTE', 'DGA_AGEROUTE', 'DAFP_AGEROUTE', 'COMPTABILITE_DGIR','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }
}
