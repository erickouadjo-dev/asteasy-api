# API Documentation - Authentifier

## Apercu
Cette documentation couvre les endpoints lies a l authentification utilisateur.

- Racine endpoints: https://asteasy.deepinovia.com/api/api
- Prefix API: /v1
- Endpoint principal: /utilisateurs/authentifier
- Middleware de groupe: cors, multi_authentication
- Policy actuelle:
  - POST /v1/utilisateurs/authentifier autorise seulement si utilisateur non connecte

Parcours de finalisation de compte (nouvel utilisateur):
- Un lien email est envoye avec un token temporaire.
- Le mot de passe initial se definit via `POST /v1/utilisateurs/{id}/finaliser-mot-de-passe`.
- Ce flux est public (sans session active) et verifie que le token correspond bien a l utilisateur cible.

## 1) Authentifier un utilisateur
- Methode: POST
- URL complete: https://asteasy.deepinovia.com/api/api/v1/utilisateurs/authentifier
- Content-Type: application/json
- Authorization: non requis (doit etre non connecte)

Body JSON:
- email (string, requis, format email)
- mot_de_passe (string, requis)

Exemple:
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/utilisateurs/authentifier" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "mot_de_passe": "Secret123"
  }'
```

Reponse 200 (succes):
```json
{
  "code_http": 200,
  "code_message": 200,
  "oauth": {
    "token_type": "Bearer",
    "expires_in": 31536000,
    "access_token": "<access_token>",
    "refresh_token": "<refresh_token>",
    "utilisateur": {
      "id": 12,
      "nom": "KOUADJO",
      "prenom": "Eric",
      "identifiant": "kouadjo.eric",
      "telephone": "0700000000",
      "etat": "actif",
      "type_utilisateur": "POWER_USER"
    },
    "entreprise": {
      "ID": 5,
      "NON_SOCIETE": "ACME Sarl",
      "SITE_WEB": "https://acme.example.com",
      "TELEPHONE": "+22990000000",
      "FICHIER_LOGO": null,
      "IS_DELETE": false
    },
    "doit_creer_entreprise": false
  }
}
```

Reponse 200 (utilisateur sans entreprise):
```json
{
  "code_http": 200,
  "code_message": 200,
  "oauth": {
    "token_type": "Bearer",
    "expires_in": 31536000,
    "access_token": "<access_token>",
    "refresh_token": "<refresh_token>",
    "utilisateur": {
      "id": 12,
      "nom": "KOUADJO",
      "prenom": "Eric",
      "identifiant": "kouadjo.eric",
      "telephone": "0700000000",
      "etat": "actif",
      "type_utilisateur": "POWER_USER"
    },
    "entreprise": null,
    "doit_creer_entreprise": true
  }
}
```

Note:
- Si l utilisateur n est rattache a aucune entreprise, `entreprise` vaut `null` et `doit_creer_entreprise` vaut `true`.
- Le frontend peut alors rediriger l utilisateur vers le flux de creation d entreprise.

Reponse 400 (validation):
```json
{
  "code_http": 400,
  "code_message": "ERR_VALIDATION",
  "erreurs": [
    "The email field is required.",
    "The mot de passe field is required."
  ]
}
```

Reponse 400 (oauth):
```json
{
  "code_http": 400,
  "code_message": "ERR_OAUTH"
}
```

Reponse 400 (compte desactive):
```json
{
  "code_http": 400,
  "code_message": "ERR_OAUTH",
  "erreurs": "Compte desactive."
}
```

Reponse 403 (deja connecte):
```json
{
  "http_code": 403,
  "code": 403,
  "code_message": "Requete non autorisee."
}
```

## 2) Finaliser le mot de passe via lien email
- Methode: POST
- URL complete: https://asteasy.deepinovia.com/api/api/v1/utilisateurs/{id}/finaliser-mot-de-passe
- Content-Type: application/json
- Authorization: non requis (doit etre non connecte)

Body JSON:
- token (string, requis)
- mot_de_passe (string, requis, min 8, confirme)
- mot_de_passe_confirmation (string, requis)
- photo (string, optionnel)

Exemple:
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/utilisateurs/12/finaliser-mot-de-passe" \
  -H "Content-Type: application/json" \
  -d '{
    "token": "<token_recu_par_email>",
    "mot_de_passe": "NouveauPass123",
    "mot_de_passe_confirmation": "NouveauPass123"
  }'
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200
}
```

Reponse 401 (token invalide/expire):
```json
{
  "code_http": 401,
  "code_message": "ERR_UNAUTHORIZED",
  "erreurs": "Token invalide ou expire."
}
```

Reponse 403 (token ne correspond pas a l id):
```json
{
  "http_code": 403,
  "code": 403,
  "code_message": "Requete non autorisee."
}
```

## 3) Endpoint OAuth utilise en interne
Le endpoint POST /v1/utilisateurs/authentifier construit ensuite une requete interne vers /oauth/token avec grant_type=password.

URL OAuth:
- https://asteasy.deepinovia.com/api/api/oauth/token

Payload interne (conceptuel):
- grant_type: password
- client_id: valeur de config app.app_oauth_client_id
- client_secret: valeur de config app.app_oauth_client_secret
- username: email
- password: mot_de_passe

Exemple direct (debug uniquement):
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/oauth/token" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "grant_type=password" \
  -d "client_id=<client_id>" \
  -d "client_secret=<client_secret>" \
  -d "username=user@example.com" \
  -d "password=Secret123"
```

Important:
- Ne pas versionner les secrets OAuth dans la documentation ou le code source.
- Utiliser les variables de configuration de l application.

## Sources techniques
Cette doc est alignee avec l implementation actuelle:
- Routes: routes/api.php
- Controller: app/Http/Controllers/Api/V1/Utilisateurs/AuthentifierController.php
- Controller (finalisation): app/Http/Controllers/Api/V1/Utilisateurs/Utilisateur/MotDePasseController.php
- Model: app/Models/Utilisateur.php (methode authentifier)
- Policy: app/Policies/Utilisateurs/AuthentifierPolicy.php


