<?php

namespace App\Policies\Transactions;

use App\Models\Transaction;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionPolicy
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
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(?Utilisateur $utilisateur, Transaction $transaction)
    {
        return in_array($utilisateur->role, ['DT_FER', 'DCG_FER', 'DAF_FER', 'DG_FER', 'DSI_FER','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }
    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Utilisateur  $utilisateur
     * @param  \App\Models\Decompte  $decompte
     * @return mixed
     */
    public function view(?Utilisateur $utilisateur, Transaction $transaction)
    {
        return in_array($utilisateur->role, ['DT_FER', 'DCG_FER', 'DAF_FER', 'DG_FER', 'DSI_FER','ADMIN_FER']) ? Response::allow() : Response::deny('Requête non autorisée.');
    }
}
