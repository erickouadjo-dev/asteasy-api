# API Documentation - Utilisateurs

## Apercu
Cette documentation couvre les endpoints exposes sous la ressource `utilisateurs`.

- Racine endpoints: `https://asteasy.deepinovia.com/api`
- Prefix API: `/v1`
- Ressource principale: `/utilisateurs`
- Middleware de groupe principal: `cors`, `multi_authentication`
- Exception: `POST /v1/utilisateurs/{id}/finaliser-mot-de-passe` est expose sous `cors` uniquement (sans session active)

## Endpoints couverts

1. `POST /utilisateurs`
2. `GET /utilisateurs`
3. `GET /utilisateurs/{id}`
4. `PUT /utilisateurs/{id}`
5. `POST /utilisateurs/authentifier`
6. `POST /utilisateurs/deconnecter`
7. `PUT /utilisateurs/{id}/mot-de-passe`
8. `POST /utilisateurs/reinitialiser-mot-de-passe`
9. `POST /utilisateurs/{id}/finaliser-mot-de-passe`

## Structure utilisateur (exemple)

```json
{
  "id": 12,
  "nom": "KOUADJO",
  "prenom": "Eric",
  "email": "user@domaine.com",
  "identifiant": "KOUADJO.Eric",
  "telephone": "0700000000",
  "photo": null,
  "etat": "actif",
  "type_utilisateur": "POWER_USER",
  "USER_TYPE_ID": 1,
  "created_at": "2026-05-07T10:15:30.000000Z",
  "updated_at": "2026-05-07T10:15:30.000000Z",
  "deleted_at": null
}
```

## 1) Creer un utilisateur
- Methode: `POST`
- URL: `/v1/utilisateurs`
- Autorisation (policy actuelle): autorise si non authentifie OU si type utilisateur `ADMIN`/`POWER_USER`
- Content-Type: `application/json`

Body JSON:
- `nom` (string, requis)
- `prenom` (string, requis)
- `email` (string email, requis)
- `type_utilisateur` (string, requis): `ADMIN|POWER_USER|SIMPLE_USER|AUTRE`
- `identifiant` (string, optionnel; defaut: `nom.prenom`)
- `telephone` (string, optionnel)

Comportement complementaire:
- Le serveur force `USER_TYPE_ID` a `1` a la creation.
- Un mail de finalisation de compte est ajoute dans la file des mails (`App\\Mail\\CreationUtilisateurMail`) avec un lien de type `APP_LINK/resetPassword/{token}/{id}`.
- L envoi effectif depend du traitement de la file de mails (commande `cron:envoyer_mails`).

Exemple:
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/utilisateurs" \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "nom": "KOUADJO",
    "prenom": "Eric",
    "email": "eric@example.com",
    "identifiant": "kouadjo.eric",
    "telephone": "0700000000",
    "type_utilisateur": "POWER_USER"
  }'
```

Reponse 201 (succes):
```json
{
  "code_http": 201,
  "code_message": 201,
  "id": 12
}
```

Reponse 400 (email deja utilise):
```json
{
  "code_http": 400,
  "code_message": "ERR_EMAIL_UTILISE"
}
```

## 2) Lister les utilisateurs
- Methode: `GET`
- URL: `/v1/utilisateurs`
- Autorisation (policy actuelle): `ADMIN` ou `POWER_USER`

Query params principaux:
- `limite` (int, defaut `50`)
- `avant` (cursor base64)
- `apres` (cursor base64)
- `filtres` (json encode)
- `libelle`, `tri`, `order` (exposes par le code, tri/filtres)

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/utilisateurs?limite=20" \
  -H "Authorization: Bearer <token>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "donnees": [
    {
      "id": 12,
      "date": "2026-05-07",
      "nom": "KOUADJO",
      "prenom": "Eric",
      "email": "eric@example.com",
      "identifiant": "kouadjo.eric",
      "telephone": "0700000000",
      "photo": null,
      "type": "POWER_USER",
      "etat": "actif"
    }
  ],
  "pagination": {
    "curseurs": {
      "apres": "MTI="
    },
    "suivant": "/utilisateurs?limite=20&apres=MTI="
  }
}
```

## 3) Recuperer un utilisateur
- Methode: `GET`
- URL: `/v1/utilisateurs/{id}`
- Autorisation (policy actuelle): `ADMIN` ou `POWER_USER` (`UtilisateurPolicy@view`)

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/utilisateurs/12" \
  -H "Authorization: Bearer <token>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "utilisateur": {
    "id": 12,
    "nom": "KOUADJO",
    "email": "eric@example.com",
    "type": "POWER_USER",
    "date": "2026-05-07T10:15:30.000000Z",
    "etat": "actif"
  }
}
```

Note comportement actuel:
- Le controller utilise `findOrFail($id)` puis capture une exception generique sans reponse JSON explicite.
- En cas d identifiant inexistant, la reponse peut ne pas etre standardisee.

## 4) Modifier un utilisateur
- Methode: `PUT`
- URL: `/v1/utilisateurs/{id}`
- Autorisation (policy actuelle): `ADMIN` ou `POWER_USER` (`UtilisateurPolicy@update`)
- Content-Type: `application/json`

Body JSON (comportement recommande):
- `email` (string, recommande/requis en pratique, max 100)
- `nom` (string, optionnel, max 100)
- `type_utilisateur` (string, optionnel)

Notes comportement actuel (implementation):
- Le code verifie l unicite avec `inputs['email']` avant `array_key_exists`, donc omettre `email` peut provoquer une erreur serveur selon la configuration PHP.
- La validation declare `type` (et non `type_utilisateur`), alors que la mise a jour applique `type_utilisateur`.

Exemple:
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/utilisateurs/12" \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "nom": "KOUADJO Modifie",
    "email": "eric.modifie@example.com",
    "type_utilisateur": "ADMIN"
  }'
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200
}
```

Reponse 400 (email deja utilise):
```json
{
  "code_http": 400,
  "code_message": "ERR_EMAIL_UTILISE"
}
```

## 5) Authentifier un utilisateur
- Methode: `POST`
- URL: `/v1/utilisateurs/authentifier`
- Autorisation (policy actuelle): autorise seulement si non authentifie
- Content-Type: `application/json`

Body JSON:
- `email` (email, requis)
- `mot_de_passe` (string, requis)

Exemple:
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/utilisateurs/authentifier" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "eric@example.com",
    "mot_de_passe": "secret123"
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
    "access_token": "<token>",
    "refresh_token": "<refresh_token>",
    "utilisateur": {
      "id": 12,
      "nom": "KOUADJO",
      "prenom": "Eric",
      "identifiant": "kouadjo.eric",
      "telephone": "0700000000",
      "type_utilisateur": "POWER_USER",
      "etat": "actif"
    }
  }
}
```

Reponse 400 (echec oauth):
```json
{
  "code_http": 400,
  "code_message": "ERR_OAUTH"
}
```

## 6) Deconnecter un utilisateur
- Methode: `POST`
- URL: `/v1/utilisateurs/deconnecter`
- Autorisation (policy actuelle): utilisateur authentifie

Headers requis:
- `Authorization: Bearer <access_token>`
- `Accept: application/json`

Exemple:
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/utilisateurs/deconnecter" \
  -H "Authorization: Bearer <access_token>" \
  -H "Accept: application/json"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200
}
```

Reponse 401 (non authentifie):
```json
{
  "code_http": 401,
  "code_message": "ERR_UNAUTHORIZED",
  "erreurs": "Utilisateur non authentifiÃ©."
}
```

Reponse 500 (erreur interne):
```json
{
  "code_http": 500,
  "code_message": "ERR_SERVER",
  "erreurs": "Une erreur est survenue lors de la dÃ©connexion."
}
```

## 7) Creer le mot de passe
- Methode: `PUT`
- URL: `/v1/utilisateurs/{id}/mot-de-passe`
- Autorisation: utilisateur authentifie (`auth:api`) + `update` via `UtilisateurPolicy`
- Regle d acces actuelle: l utilisateur peut modifier son propre mot de passe; `ADMIN`/`POWER_USER` peuvent modifier celui d un autre utilisateur
- Content-Type: `application/json`

Body JSON:
- `mot_de_passe` (string, requis, min 8, doit etre confirme)
- `mot_de_passe_confirmation` (string, requis pour `confirmed`)
- `photo` (string, optionnel)

Exemple:
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/utilisateurs/12/mot-de-passe" \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
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

## 8) Reinitialiser le mot de passe
- Methode: `POST`
- URL: `/v1/utilisateurs/reinitialiser-mot-de-passe`
- Autorisation (policy actuelle): autorise seulement si non authentifie
- Content-Type: `application/json`

Body JSON:
- `email` (email, requis)

Exemple:
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/utilisateurs/reinitialiser-mot-de-passe" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "eric@example.com"
  }'
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200
}
```

Reponse 400 (email invalide/inexistant selon cas):
```json
{
  "code_http": 400,
  "code_message": "ERR_EMAIL_INVALIDE"
}
```

## 9) Finaliser le mot de passe via lien email
- Methode: `POST`
- URL: `/v1/utilisateurs/{id}/finaliser-mot-de-passe`
- Autorisation (policy actuelle): endpoint public, autorise seulement si non authentifie
- Middleware route: `cors` uniquement
- Content-Type: `application/json`

Body JSON:
- `token` (string, requis) - token recu dans le lien email
- `mot_de_passe` (string, requis, min 8, confirme) ou `password`
- `mot_de_passe_confirmation` (string, requis) ou `password_confirmation`
- `photo` (string, optionnel)

Le token peut etre fourni de 3 manieres:
- Dans le body JSON via `token`
- En query string via `?token=...`
- Dans le header `Authorization: Bearer <token>`

Exemple 1 (token dans le body):
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/utilisateurs/12/finaliser-mot-de-passe" \
  -H "Content-Type: application/json" \
  -d '{
    "token": "<token_recu_par_email>",
    "password": "NouveauPass123",
    "password_confirmation": "NouveauPass123"
  }'
```

Exemple 2 (token en query string):
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/utilisateurs/12/finaliser-mot-de-passe?token=<token_recu_par_email>" \
  -H "Content-Type: application/json" \
  -d '{
    "mot_de_passe": "NouveauPass123",
    "mot_de_passe_confirmation": "NouveauPass123"
  }'
```

Exemple 3 (token en header Bearer):
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/utilisateurs/12/finaliser-mot-de-passe" \
  -H "Authorization: Bearer <token_recu_par_email>" \
  -H "Content-Type: application/json" \
  -d '{
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
  "code_message": "RequÃªte non autorisÃ©e."
}
```

## Erreurs communes

### 400 - Validation
```json
{
  "code_http": 400,
  "code_message": "ERR_VALIDATION",
  "erreurs": [
    "The email field is required."
  ]
}
```

### 401 - Non authentifie
```json
{
  "code_http": 401,
  "code_message": "ERR_UNAUTHORIZED",
  "erreurs": "Utilisateur non authentifiÃ©."
}
```

### 403 - Non autorise
```json
{
  "http_code": 403,
  "code": 403,
  "code_message": "RequÃªte non autorisÃ©e."
}
```

### 500 - Erreur serveur
```json
{
  "code_http": 500,
  "code_message": "ERR_SERVER",
  "erreurs": "Une erreur est survenue lors de la dÃ©connexion."
}
```

## Sources techniques
Cette doc est alignee avec l implementation actuelle:
- Routes: `routes/api.php`
- Controllers:
  - `app/Http/Controllers/Api/V1/UtilisateursController.php`
  - `app/Http/Controllers/Api/V1/Utilisateurs/Utilisateur/UtilisateursController.php`
  - `app/Http/Controllers/Api/V1/Utilisateurs/AuthentifierController.php`
  - `app/Http/Controllers/Api/V1/Utilisateurs/DeconnecterController.php`
  - `app/Http/Controllers/Api/V1/Utilisateurs/Utilisateur/MotDePasseController.php`
  - `app/Http/Controllers/Api/V1/Utilisateurs/ReinitialiserMotDePasseController.php`
- Model: `app/Models/Utilisateur.php`
- Policies:
  - `app/Policies/UtilisateursPolicy.php`
  - `app/Policies/Utilisateurs/UtilisateurPolicy.php`
  - `app/Policies/Utilisateurs/AuthentifierPolicy.php`
  - `app/Policies/Utilisateurs/DeconnecterPolicy.php`
  - `app/Policies/Utilisateurs/ReinitialiserMotDePassePolicy.php`


