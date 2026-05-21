<?php
namespace App\Utility;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use App\Models\Utilisateur;

class BearerTokenResponse extends \League\OAuth2\Server\ResponseTypes\BearerTokenResponse{
    /**
     * Add custom fields to your Bearer Token response here, then override
     * AuthorizationServer::getResponseType() to pull in your version of
     * this class rather than the default.
     *
     * @param AccessTokenEntityInterface $accessToken
     *
     * @return array
     */
    protected function getExtraParams(AccessTokenEntityInterface $accessToken): array{
        $user = [];
        $entreprise = null;
        $doit_creer_entreprise = false;
        if(!is_null($this->accessToken->getUserIdentifier())){
            $utilisateur = Utilisateur::where('id', $this->accessToken->getUserIdentifier())->first();
            $user['mot_de_passe_defini'] = !is_null($utilisateur->mot_de_passe);
            $user['id'] = $utilisateur->id;
            $user['nom'] = $utilisateur->nom;
            $user['photo'] = $utilisateur->photo;
            $user['type_utilisateur'] = $utilisateur->type_utilisateur;

            // Récupérer l'entreprise via la relation employe
            if($utilisateur && $utilisateur->employe) {
                $employe = $utilisateur->employe;
                if($employe && $employe->entreprise) {
                    $entreprise_modele = $employe->entreprise;
                    $entreprise = [
                        'ID' => $entreprise_modele->ID,
                        'NON_SOCIETE' => $entreprise_modele->NON_SOCIETE,
                        'SITE_WEB' => $entreprise_modele->SITE_WEB,
                        'TELEPHONE' => $entreprise_modele->TELEPHONE,
                        'FICHIER_LOGO' => $entreprise_modele->FICHIER_LOGO,
                        'IS_DELETE' => $entreprise_modele->IS_DELETE,
                    ];
                } else {
                    $doit_creer_entreprise = true;
                }
            } else {
                $doit_creer_entreprise = true;
            }
        }

        return [
            'utilisateur' => empty($utilisateur) ? null : $utilisateur,
            'entreprise' => $entreprise,
            'doit_creer_entreprise' => $doit_creer_entreprise,
            'expires_at' => $this->accessToken->getExpiryDateTime()->getTimestamp()
        ];
    }
}
