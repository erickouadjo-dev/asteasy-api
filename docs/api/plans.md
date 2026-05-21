# API Documentation - Plans

## Apercu
Cette documentation couvre les endpoints CRUD de la ressource `plans`.

- Racine endpoints: `https://asteasy.deepinovia.com/api/api`
- Prefix API: `/v1`
- Ressource: `/plans`
- Middleware de groupe: `cors`, `multi_authentication`
- Policy:
  - Lecture (`index`, `show`): utilisateur authentifie
  - Ecriture (`store`, `update`, `destroy`): utilisateur de type `ADMIN`

## Structure de la ressource Plan

```json
{
  "ID": 1,
  "LIBELLE": "Pro",
  "DESCRIPTION": "Plan professionnel pour PME",
  "PRIX": "49.99",
  "DUREE": "mensuel",
  "LIMITE_UTILISATEURS": 25,
  "IS_DELETE": false,
  "created_at": "2026-05-07T10:15:30.000000Z",
  "updated_at": "2026-05-07T10:15:30.000000Z",
  "deleted_at": null,
  "modules": [
    {
      "ID": 1,
      "PLAN_ID": 1,
      "MODULE_ID": 3,
      "IS_DELETE": false,
      "created_at": "2026-05-07T10:15:30.000000Z",
      "updated_at": "2026-05-07T10:15:30.000000Z",
      "deleted_at": null
    }
  ]
}
```

## Endpoints

### 1) Lister les plans
- Methode: `GET`
- URL: `/v1/plans`
- Autorisation: utilisateur authentifie

Parametres query optionnels:
- `per_page` (int, defaut: `15`)
- `page` (int, defaut: `1`)
- `search` (string, filtre sur `LIBELLE`, `DESCRIPTION` et `DUREE`)

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/plans?per_page=10&page=1&search=pro" \
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
      "LIBELLE": "Pro",
      "DESCRIPTION": "Plan professionnel pour PME",
      "PRIX": "49.99",
      "DUREE": "mensuel",
      "LIMITE_UTILISATEURS": 25,
      "IS_DELETE": false,
      "created_at": "2026-05-07T10:15:30.000000Z",
      "updated_at": "2026-05-07T10:15:30.000000Z",
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

### 2) Creer un plan
- Methode: `POST`
- URL: `/v1/plans`
- Autorisation: `ADMIN`
- Content-Type: `application/json`

Body JSON:
- `LIBELLE` (string, optionnel, max 50, unique)
- `DESCRIPTION` (string, optionnel)
- `PRIX` (numeric, requis, >= 0)
- `DUREE` (string, optionnel)
- `LIMITE_UTILISATEURS` (integer, requis, >= 1)
- `MODULE_IDS` (array d'integers, optionnel â€” IDs existants dans `TB_MODULE`)

Exemple:
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/plans" \
  -H "Authorization: Bearer <token_admin>" \
  -H "Content-Type: application/json" \
  -d '{
    "LIBELLE": "Premium",
    "DESCRIPTION": "Plan premium avec acces avance",
    "PRIX": 99.99,
    "DUREE": "annuel",
    "LIMITE_UTILISATEURS": 100,
    "MODULE_IDS": [1, 2, 3]
  }'
```

Reponse 201:
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 2,
    "LIBELLE": "Premium",
    "DESCRIPTION": "Plan premium avec acces avance",
    "PRIX": "99.99",
    "DUREE": "annuel",
    "LIMITE_UTILISATEURS": 100,
    "IS_DELETE": false,
    "created_at": "2026-05-07T11:00:00.000000Z",
    "updated_at": "2026-05-07T11:00:00.000000Z",
    "deleted_at": null,
    "modules": [
      { "ID": 1, "PLAN_ID": 2, "MODULE_ID": 1, "IS_DELETE": false, "created_at": "2026-05-07T11:00:00.000000Z", "updated_at": "2026-05-07T11:00:00.000000Z", "deleted_at": null },
      { "ID": 2, "PLAN_ID": 2, "MODULE_ID": 2, "IS_DELETE": false, "created_at": "2026-05-07T11:00:00.000000Z", "updated_at": "2026-05-07T11:00:00.000000Z", "deleted_at": null },
      { "ID": 3, "PLAN_ID": 2, "MODULE_ID": 3, "IS_DELETE": false, "created_at": "2026-05-07T11:00:00.000000Z", "updated_at": "2026-05-07T11:00:00.000000Z", "deleted_at": null }
    ]
  }
}
```

### 3) Recuperer un plan
- Methode: `GET`
- URL: `/v1/plans/{id}`
- Autorisation: utilisateur authentifie

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/plans/2" \
  -H "Authorization: Bearer <token>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "LIBELLE": "Premium",
    "DESCRIPTION": "Plan premium avec acces avance",
    "PRIX": "99.99",
    "DUREE": "annuel",
    "LIMITE_UTILISATEURS": 100,
    "IS_DELETE": false,
    "created_at": "2026-05-07T11:00:00.000000Z",
    "updated_at": "2026-05-07T11:00:00.000000Z",
    "deleted_at": null,
    "modules": [
      { "ID": 1, "PLAN_ID": 2, "MODULE_ID": 1, "IS_DELETE": false, "created_at": "2026-05-07T11:00:00.000000Z", "updated_at": "2026-05-07T11:00:00.000000Z", "deleted_at": null },
      { "ID": 2, "PLAN_ID": 2, "MODULE_ID": 2, "IS_DELETE": false, "created_at": "2026-05-07T11:00:00.000000Z", "updated_at": "2026-05-07T11:00:00.000000Z", "deleted_at": null }
    ]
  }
}
```

### 4) Mettre a jour un plan
- Methode: `PUT`
- URL: `/v1/plans/{id}`
- Autorisation: `ADMIN`
- Content-Type: `application/json`

Body JSON (tout optionnel, au moins 1 champ recommande):
- `LIBELLE` (string, optionnel, max 50, unique)
- `DESCRIPTION` (string, optionnel)
- `PRIX` (numeric, optionnel, >= 0)
- `DUREE` (string, optionnel)
- `LIMITE_UTILISATEURS` (integer, optionnel, >= 1)
- `MODULE_IDS` (array d'integers, optionnel â€” remplace entierement les modules existants du plan)

Exemple:
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/plans/2" \
  -H "Authorization: Bearer <token_admin>" \
  -H "Content-Type: application/json" \
  -d '{
    "DESCRIPTION": "Plan premium revise",
    "PRIX": 79.99,
    "LIMITE_UTILISATEURS": 80,
    "MODULE_IDS": [1, 4]
  }'
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "LIBELLE": "Premium",
    "DESCRIPTION": "Plan premium revise",
    "PRIX": "79.99",
    "DUREE": "annuel",
    "LIMITE_UTILISATEURS": 80,
    "IS_DELETE": false,
    "created_at": "2026-05-07T11:00:00.000000Z",
    "updated_at": "2026-05-07T11:10:00.000000Z",
    "deleted_at": null,
    "modules": [
      { "ID": 5, "PLAN_ID": 2, "MODULE_ID": 1, "IS_DELETE": false, "created_at": "2026-05-07T11:10:00.000000Z", "updated_at": "2026-05-07T11:10:00.000000Z", "deleted_at": null },
      { "ID": 6, "PLAN_ID": 2, "MODULE_ID": 4, "IS_DELETE": false, "created_at": "2026-05-07T11:10:00.000000Z", "updated_at": "2026-05-07T11:10:00.000000Z", "deleted_at": null }
    ]
  }
}
```

### 5) Supprimer un plan
- Methode: `DELETE`
- URL: `/v1/plans/{id}`
- Autorisation: `ADMIN`

Comportement:
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigne)

Exemple:
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/plans/2" \
  -H "Authorization: Bearer <token_admin>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "LIBELLE": "Premium",
    "DESCRIPTION": "Plan premium revise",
    "PRIX": "79.99",
    "DUREE": "annuel",
    "LIMITE_UTILISATEURS": 80,
    "IS_DELETE": true,
    "created_at": "2026-05-07T11:00:00.000000Z",
    "updated_at": "2026-05-07T11:15:00.000000Z",
    "deleted_at": "2026-05-07T11:15:00.000000Z"
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
    "The PRIX field is required.",
    "The LIMITE UTILISATEURS must be at least 1."
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
  "erreurs": "Le plan n'existe pas."
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
Cette doc est alignee avec l'implementation actuelle:
- Routes: `routes/api.php`
- Controller: `app/Http/Controllers/Api/V1/PlansController.php`
- Model: `app/Models/Plan.php`
- Policy: `app/Policies/PlansPolicy.php`
- Liaisons modules: voir [plans-modules.md](plans-modules.md)


