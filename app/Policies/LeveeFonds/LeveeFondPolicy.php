<?php
namespace App\Policies\LeveeFonds;

use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class LeveeFondPolicy
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
     * Determine whether the account can view any models.
     *
     * @param  \App\Models\Utilisateur  utilisateur
     * @return mixed
     */
    public function view(?Utilisateur $utilisateur)
    {
        return in_array($utilisateur->role, ['DT_FER', 'DAF_FER', 'DSI_FER', 'DCG_FER', 'DG_FER','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }

     /**
     * Determine whether the account can view any models.
     *
     * @param  \App\Models\Utilisateur  utilisateur
     * @return mixed
     */
    public function update(?Utilisateur $utilisateur)
    {
        return in_array($utilisateur->role, ['DT_FER', 'DAF_FER', 'DSI_FER', 'DCG_FER', 'DG_FER','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }


}