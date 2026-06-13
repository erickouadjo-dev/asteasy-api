# API Documentation - Roles Permissions

## Apercu
Cette documentation couvre les endpoints CRUD de la ressource `roles-permissions`.

- Racine endpoints: `https://asteasy.deepinovia.com/api/api`
- Prefix API: `/v1`
- Ressource: `/roles-permissions`
- Middleware de groupe: `cors`, `multi_authentication`
- Policy:
  - Lecture (`index`, `show`): utilisateur authentifie
  - Ecriture (`store`, `update`, `destroy`): utilisateur de type `ADMIN`

## Structure de la ressource RolePermission

```json
{
  "ID": 1,
  "ROLE_ID": 2,
  "PERMISSION_ID": 5,
  "FONCTIONNALITE_ID": 3,
  "IS_DELETE": false,
  "created_at": "2026-05-09T12:00:00.000000Z",
  "updated_at": "2026-05-09T12:00:00.000000Z",
  "deleted_at": null
}
```

## Endpoints

### 1) Lister les liaisons roles-permissions
- Methode: `GET`
- URL: `/v1/roles-permissions`
- Autorisation: utilisateur authentifie

Parametres query optionnels:
- `per_page` (int, defaut: `15`)
- `page` (int, defaut: `1`)
- `search` (string, filtre sur `ROLE_ID`, `PERMISSION_ID`, `FONCTIONNALITE_ID`)

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/roles-permissions?per_page=10&page=1&search=2" \
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
      "ROLE_ID": 2,
      "PERMISSION_ID": 5,
      "FONCTIONNALITE_ID": 3,
      "IS_DELETE": false,
      "created_at": "2026-05-09T12:00:00.000000Z",
      "updated_at": "2026-05-09T12:00:00.000000Z",
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

### 2) Creer une liaison role-permission
- Methode: `POST`
- URL: `/v1/roles-permissions/{id}`
- Autorisation: `ADMIN`
- Content-Type: `application/json`

Body JSON:
- `ROLE_ID` (integer, requis, doit exister dans `TB_ROLE.ID`)
- `PERMISSION_ID` (integer, requis, doit exister dans `TB_PERMISSION.ID`)
- `FONCTIONNALITE_ID` (integer, optionnel, doit exister dans `TB_FONCTIONNALITE.ID`)

Exemple:
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/roles-permissions" \
  -H "Authorization: Bearer <token_admin>" \
  -H "Content-Type: application/json" \
  -d '{
    "ROLE_ID": 2,
    "PERMISSION_ID": 5,
    "FONCTIONNALITE_ID": 3
  }'
```

Reponse 201:
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 2,
    "ROLE_ID": 2,
    "PERMISSION_ID": 5,
    "FONCTIONNALITE_ID": 3,
    "IS_DELETE": false,
    "created_at": "2026-05-09T12:10:00.000000Z",
    "updated_at": "2026-05-09T12:10:00.000000Z",
    "deleted_at": null
  }
}
```

### 3) Recuperer une liaison role-permission
- Methode: `GET`
- URL: `/v1/roles-permissions/{id}`
- Autorisation: utilisateur authentifie

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/roles-permissions/2" \
  -H "Authorization: Bearer <token>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "ROLE_ID": 2,
    "PERMISSION_ID": 5,
    "FONCTIONNALITE_ID": 3,
    "IS_DELETE": false,
    "created_at": "2026-05-09T12:10:00.000000Z",
    "updated_at": "2026-05-09T12:10:00.000000Z",
    "deleted_at": null
  }
}
```

### 4) Mettre a jour une liaison role-permission
- Methode: `PUT`
- URL: `/v1/roles-permissions/{id}`
- Autorisation: `ADMIN`
- Content-Type: `application/json`

Body JSON (optionnel):
- `ROLE_ID` (integer, optionnel, doit exister dans `TB_ROLE.ID`)
- `PERMISSION_ID` (integer, optionnel, doit exister dans `TB_PERMISSION.ID`)
- `FONCTIONNALITE_ID` (integer, optionnel, doit exister dans `TB_FONCTIONNALITE.ID`)

Exemple:
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/roles-permissions/2" \
  -H "Authorization: Bearer <token_admin>" \
  -H "Content-Type: application/json" \
  -d '{
    "PERMISSION_ID": 7,
    "FONCTIONNALITE_ID": 4
  }'
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "ROLE_ID": 2,
    "PERMISSION_ID": 7,
    "FONCTIONNALITE_ID": 4,
    "IS_DELETE": false,
    "created_at": "2026-05-09T12:10:00.000000Z",
    "updated_at": "2026-05-09T12:15:00.000000Z",
    "deleted_at": null
  }
}
```

### 5) Supprimer une liaison role-permission
- Methode: `DELETE`
- URL: `/v1/roles-permissions/{id}`
- Autorisation: `ADMIN`

Comportement:
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigne)

Exemple:
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/roles-permissions/2" \
  -H "Authorization: Bearer <token_admin>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "ROLE_ID": 2,
    "PERMISSION_ID": 7,
    "FONCTIONNALITE_ID": 4,
    "IS_DELETE": true,
    "created_at": "2026-05-09T12:10:00.000000Z",
    "updated_at": "2026-05-09T12:20:00.000000Z",
    "deleted_at": "2026-05-09T12:20:00.000000Z"
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
    "The ROLE ID field is required.",
    "The selected PERMISSION ID is invalid."
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
  "erreurs": "La liaison role-permission n'existe pas."
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
- Controller: `app/Http/Controllers/Api/V1/RolesPermissionsController.php`
- Model: `app/Models/RolePermission.php`
- Policy: `app/Policies/RolesPermissionsPolicy.php`


