<?php

namespace App\Policies\MissionControls;

use App\Models\Utilisateur;
use App\Models\MissionControle;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\HandlesAuthorization;

class MissionControlPolicy
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
     * Determine whether the account can view a model.
     *
     * @param  \App\Models\Utilisateur  utilisateur
     * @param  \App\Models\MissionControle  $missionControle
     * @return mixed
     */
    public function view(?Utilisateur $utilisateur)
    {
        return in_array($utilisateur->role, ['DT_FER', 'DCG_FER', 'DAF_FER', 'DG_FER', 'DMC_AGEROUTE', 'DGA_AGEROUTE','COMPTABILITE_DGIR','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }

    /**
     * Determine whether the account can update any models.
     *
     * @param  \App\Models\Utilisateur  utilisateur
     * @param  \App\Models\MissionControle  $missionControle
     * @return mixed
     */
    public function update(?Utilisateur $utilisateur)
    {
        return in_array($utilisateur->role, ['DT_FER', 'DCG_FER', 'DAF_FER', 'DG_FER', 'DMC_AGEROUTE', 'DGA_AGEROUTE','COMPTABILITE_DGIR','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }

    /**
     * Determine whether the account can update any models.
     *
     * @param  \App\Models\Utilisateur  utilisateur
     * @param  \App\Models\MissionControle  $missionControle
     * @return mixed
     */
    public function delete(?Utilisateur $utilisateur)
    {
        return in_array($utilisateur->role, ['DT_FER', 'DCG_FER', 'DAF_FER', 'DG_FER', 'DMC_AGEROUTE', 'DGA_AGEROUTE','COMPTABILITE_DGIR','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }
}
