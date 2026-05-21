# API Documentation - Abonnements

## Apercu
https://asteasy.deepinovia.com/api/api
- Racine endpoints: `https://asteasy.deepinovia.com/api/api`
- Prefix API: `/v1`
- Ressource: `/abonnements`
- Middleware de groupe: `cors`, `multi_authentication`
- Policy:
  - Lecture (`index`, `show`): utilisateur authentifie
  - Ecriture (`store`, `update`, `destroy`): utilisateur de type `ADMIN`

## Structure de la ressource Abonnement

```json
{
  "ID": 1,
  "ENTREPRISE_ID": 1,
  "PLAN_ID": 1,
  "DATE_DEBUT": "2026-01-01",
  "DATE_FIN": "2026-12-31",
  "STATUT": "actif",
  "IS_DELETE": false,
  "created_at": "2026-05-08T10:00:00.000000Z",
  "updated_at": "2026-05-08T10:00:00.000000Z",
  "deleted_at": null
}
```

## Endpoints

### 1) Lister les abonnements
- Methode: `GET`
- URL: `/v1/abonnements`
- Autorisation: utilisateur authentifie

Parametres query optionnels:
- `per_page` (int, defaut: `15`)
- `page` (int, defaut: `1`)
- `search` (string, filtre sur entreprise `NON_SOCIETE` ou `STATUT`)

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/abonnements?per_page=10&page=1&search=actif" \
  -H "Authorization: Bearer <token>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": [
    {
      "ID": 1,
      "ENTREPRISE_ID": 1,
      "PLAN_ID": 1,
      "DATE_DEBUT": "2026-01-01",
      "DATE_FIN": "2026-12-31",
      "STATUT": "actif",
      "IS_DELETE": false,
      "created_at": "2026-05-08T10:00:00.000000Z",
      "updated_at": "2026-05-08T10:00:00.000000Z",
      "deleted_at": null
    }
  ],
  "pagination": {
    "total": 1,
    "per_page": 10,
    "current_page": 1,
    "last_page": 1,
    "from": 1,
    "to": 1
  }
}
```

### 2) Creer un abonnement
- Methode: `POST`
- URL: `/v1/abonnements`
- Autorisation: `ADMIN`
- Content-Type: `application/json`

Body JSON:
- `ENTREPRISE_ID` (integer, optionnel, must exist in TB_ENTREPRISE)
- `PLAN_ID` (integer, optionnel, must exist in TB_PLAN)
- `DATE_DEBUT` (date, optionnel, format: YYYY-MM-DD)
- `DATE_FIN` (date, optionnel, format: YYYY-MM-DD)
- `STATUT` (string, optionnel, max 50) - ex: "actif", "inactif", "suspendu"

Exemple:
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/abonnements" \
  -H "Authorization: Bearer <token_admin>" \
  -H "Content-Type: application/json" \
  -d '{
    "ENTREPRISE_ID": 1,
    "PLAN_ID": 1,
    "DATE_DEBUT": "2026-01-01",
    "DATE_FIN": "2026-12-31",
    "STATUT": "actif"
  }'
```

Reponse 201:
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 2,
    "ENTREPRISE_ID": 1,
    "PLAN_ID": 1,
    "DATE_DEBUT": "2026-01-01",
    "DATE_FIN": "2026-12-31",
    "STATUT": "actif",
    "IS_DELETE": false,
    "created_at": "2026-05-08T11:00:00.000000Z",
    "updated_at": "2026-05-08T11:00:00.000000Z",
    "deleted_at": null
  }
}
```

### 3) Recuperer un abonnement
- Methode: `GET`
- URL: `/v1/abonnements/{id}`
- Autorisation: utilisateur authentifie

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/abonnements/2" \
  -H "Authorization: Bearer <token>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "ENTREPRISE_ID": 1,
    "PLAN_ID": 1,
    "DATE_DEBUT": "2026-01-01",
    "DATE_FIN": "2026-12-31",
    "STATUT": "actif",
    "IS_DELETE": false,
    "created_at": "2026-05-08T11:00:00.000000Z",
    "updated_at": "2026-05-08T11:00:00.000000Z",
    "deleted_at": null
  }
}
```

### 4) Mettre a jour un abonnement
- Methode: `PUT`
- URL: `/v1/abonnements/{id}`
- Autorisation: `ADMIN`
- Content-Type: `application/json`

Body JSON:
- `ENTREPRISE_ID` (integer, optionnel, must exist in TB_ENTREPRISE)
- `PLAN_ID` (integer, optionnel, must exist in TB_PLAN)
- `DATE_DEBUT` (date, optionnel, format: YYYY-MM-DD)
- `DATE_FIN` (date, optionnel, format: YYYY-MM-DD)
- `STATUT` (string, optionnel, max 50)

Exemple:
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/abonnements/2" \
  -H "Authorization: Bearer <token_admin>" \
  -H "Content-Type: application/json" \
  -d '{
    "STATUT": "inactif"
  }'
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "ENTREPRISE_ID": 1,
    "PLAN_ID": 1,
    "DATE_DEBUT": "2026-01-01",
    "DATE_FIN": "2026-12-31",
    "STATUT": "inactif",
    "IS_DELETE": false,
    "created_at": "2026-05-08T11:00:00.000000Z",
    "updated_at": "2026-05-08T11:10:00.000000Z",
    "deleted_at": null
  }
}
```

### 5) Supprimer un abonnement
- Methode: `DELETE`
- URL: `/v1/abonnements/{id}`
- Autorisation: `ADMIN`

Comportement:
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigne)

Exemple:
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/abonnements/2" \
  -H "Authorization: Bearer <token_admin>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "ENTREPRISE_ID": 1,
    "PLAN_ID": 1,
    "DATE_DEBUT": "2026-01-01",
    "DATE_FIN": "2026-12-31",
    "STATUT": "inactif",
    "IS_DELETE": true,
    "created_at": "2026-05-08T11:00:00.000000Z",
    "updated_at": "2026-05-08T11:15:00.000000Z",
    "deleted_at": "2026-05-08T11:15:00.000000Z"
  }
}
```

## Erreurs communes

### 400 - Validation
```json
{
  "code_http": 400,
  "code_message": "ERR_VALIDATION",
  "erreurs": [
    "The ENTREPRISE_ID field is invalid."
  ]
}
```

Ou si body invalide/vide:
```json
{
  "code_http": 400,
  "code_message": "ERR_VALIDATION",
  "erreurs": "Corps de la requete vide."
}
```

### 403 - Non autorise
```json
{
  "http_code": 403,
  "code": 403,
  "code_message": "Requete non autorisee."
}
```

### 404 - Non trouve
```json
{
  "code_http": 404,
  "code_message": "ERR_NOT_FOUND",
  "erreurs": "L'abonnement n'existe pas."
}
```

### 500 - Erreur serveur
```json
{
  "code_http": 500,
  "code_message": "ERR_SERVER",
  "erreurs": "Une erreur est survenue."
}
```

## Sources techniques
- Routes: `routes/api.php`
- Controller: `app/Http/Controllers/Api/V1/AbonnementsController.php`
- Model: `app/Models/Abonnement.php`
- Policy: `app/Policies/AbonnementsPolicy.php`


