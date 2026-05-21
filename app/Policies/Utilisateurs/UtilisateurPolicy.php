<?php

namespace App\Policies\Utilisateurs;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class UtilisateurPolicy
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
     * @param  \App\Models\Utilisateur  utilisateur
     * @return mixed
     */
    public function update(?Utilisateur $utilisateur, ?Utilisateur $cible = null)
    {
        if (is_null($utilisateur)) {
            return Response::deny('Requête non autorisée.');
        }

        // Autorise un utilisateur a modifier son propre mot de passe.
        if (!is_null($cible) && (int) $utilisateur->id === (int) $cible->id) {
            return Response::allow();
        }

        return in_array($utilisateur->type_utilisateur, [
            Utilisateur::TYPE_UTILISATEUR_ADMIN,
            Utilisateur::TYPE_UTILISATEUR_POWER_USER,
        ])
            ? Response::allow()
            : Response::deny('Requête non autorisée.');
    }
    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Utilisateur  $utilisateur
     * @return mixed
     */
    public function view(?Utilisateur $utilisateur)
    {
      return !is_null($utilisateur)
          && in_array($utilisateur->type_utilisateur, [
              Utilisateur::TYPE_UTILISATEUR_ADMIN,
              Utilisateur::TYPE_UTILISATEUR_POWER_USER,
          ])
          ? Response::allow()
          : Response::deny('Requête non autorisée.');
    }

}
