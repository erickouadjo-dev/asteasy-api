# API Documentation - Plans Modules

## Apercu
Cette documentation couvre les endpoints CRUD de la ressource `plans-modules` (table `TB_PLAN_MODULE`).

- Racine endpoints: `https://asteasy.deepinovia.com/api/api`
- Prefix API: `/v1`
- Ressource: `/plans-modules`
- Middleware de groupe: `cors`, `multi_authentication`
- Policy:
  - Lecture (`index`, `show`): utilisateur authentifie
  - Ecriture (`store`, `update`, `destroy`): utilisateur de type `ADMIN`

## Statut d'implementation
Les routes `plans-modules` sont disponibles dans [routes/api.php](routes/api.php).

## Structure de la ressource PlanModule

```json
{
  "ID": 1,
  "PLAN_ID": 2,
  "MODULE_ID": 5,
  "IS_DELETE": false,
  "created_at": "2026-05-11T10:00:00.000000Z",
  "updated_at": "2026-05-11T10:00:00.000000Z",
  "deleted_at": null
}
```

## Endpoints

### 1) Lister les liaisons plan-module
- Methode: `GET`
- URL: `/v1/plans-modules`
- Autorisation: utilisateur authentifie

Parametres query optionnels:
- `per_page` (int, defaut: `15`)
- `page` (int, defaut: `1`)
- `search` (string, filtre sur `PLAN_ID` et `MODULE_ID`)

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/plans-modules?per_page=10&page=1&search=2" \
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
      "PLAN_ID": 2,
      "MODULE_ID": 5,
      "IS_DELETE": false,
      "created_at": "2026-05-11T10:00:00.000000Z",
      "updated_at": "2026-05-11T10:00:00.000000Z",
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

### 2) Creer une liaison plan-module
- Methode: `POST`
- URL: `/v1/plans-modules/{id}`
- Autorisation: `ADMIN`
- Content-Type: `application/json`

Body JSON:
- `PLAN_ID` (integer, requis, doit exister dans `TB_PLAN.ID`)
- `MODULE_ID` (integer, requis, doit exister dans `TB_MODULE.ID`)

Exemple:
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/plans-modules" \
  -H "Authorization: Bearer <token_admin>" \
  -H "Content-Type: application/json" \
  -d '{
    "PLAN_ID": 2,
    "MODULE_ID": 5
  }'
```

Reponse 201:
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 2,
    "PLAN_ID": 2,
    "MODULE_ID": 5,
    "IS_DELETE": false,
    "created_at": "2026-05-11T10:10:00.000000Z",
    "updated_at": "2026-05-11T10:10:00.000000Z",
    "deleted_at": null
  }
}
```

### 3) Recuperer une liaison plan-module
- Methode: `GET`
- URL: `/v1/plans-modules/{id}`
- Autorisation: utilisateur authentifie

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/plans-modules/2" \
  -H "Authorization: Bearer <token>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "PLAN_ID": 2,
    "MODULE_ID": 5,
    "IS_DELETE": false,
    "created_at": "2026-05-11T10:10:00.000000Z",
    "updated_at": "2026-05-11T10:10:00.000000Z",
    "deleted_at": null
  }
}
```

### 4) Mettre a jour une liaison plan-module
- Methode: `PUT`
- URL: `/v1/plans-modules/{id}`
- Autorisation: `ADMIN`
- Content-Type: `application/json`

Body JSON (optionnel):
- `PLAN_ID` (integer, optionnel, doit exister dans `TB_PLAN.ID`)
- `MODULE_ID` (integer, optionnel, doit exister dans `TB_MODULE.ID`)

Exemple:
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/plans-modules/2" \
  -H "Authorization: Bearer <token_admin>" \
  -H "Content-Type: application/json" \
  -d '{
    "MODULE_ID": 6
  }'
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "PLAN_ID": 2,
    "MODULE_ID": 6,
    "IS_DELETE": false,
    "created_at": "2026-05-11T10:10:00.000000Z",
    "updated_at": "2026-05-11T10:15:00.000000Z",
    "deleted_at": null
  }
}
```

### 5) Supprimer une liaison plan-module
- Methode: `DELETE`
- URL: `/v1/plans-modules/{id}`
- Autorisation: `ADMIN`

Comportement:
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigne)

Exemple:
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/plans-modules/2" \
  -H "Authorization: Bearer <token_admin>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "PLAN_ID": 2,
    "MODULE_ID": 6,
    "IS_DELETE": true,
    "created_at": "2026-05-11T10:10:00.000000Z",
    "updated_at": "2026-05-11T10:20:00.000000Z",
    "deleted_at": "2026-05-11T10:20:00.000000Z"
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
    "The selected PLAN ID is invalid.",
    "The selected MODULE ID is invalid."
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
  "erreurs": "La liaison plan-module n'existe pas."
}
```

### 500 - Erreur serveur
```json
{
  "code_http": 500,
  "code_message": "ERR_SERVER",
  "erreurs": "Une erreur est survenue lors du traitement des liaisons plan-module."
}
```


