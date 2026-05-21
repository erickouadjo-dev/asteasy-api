# API Documentation - Permissions

## Apercu

- Racine endpoints: `https://asteasy.deepinovia.com/api/api`
- Prefix API: `/v1`
- Ressource: `/permissions`
- Middleware de groupe: `cors`, `multi_authentication`
- Policy:
  - Lecture (`index`, `show`): utilisateur authentifie
  - Ecriture (`store`, `update`, `destroy`): utilisateur de type `ADMIN`

## Structure de la ressource Permission

```json
{
  "ID": 1,
  "LIBELLE": "LIRE_RAPPORT",
  "IS_DELETE": false,
  "created_at": "2026-05-08T10:00:00.000000Z",
  "updated_at": "2026-05-08T10:00:00.000000Z",
  "deleted_at": null
}
```

## Endpoints

### 1) Lister les permissions
- Methode: `GET`
- URL: `/v1/permissions`
- Autorisation: utilisateur authentifie

Parametres query optionnels:
- `per_page` (int, defaut: `15`)
- `page` (int, defaut: `1`)
- `search` (string, filtre sur `LIBELLE`)

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/permissions?per_page=10&page=1&search=lire" \
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
      "LIBELLE": "LIRE_RAPPORT",
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

### 2) Creer une permission
- Methode: `POST`
- URL: `/v1/permissions`
- Autorisation: `ADMIN`
- Content-Type: `application/json`

Body JSON:
- `LIBELLE` (string, optionnel, max 50, unique)

Exemple:
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/permissions" \
  -H "Authorization: Bearer <token_admin>" \
  -H "Content-Type: application/json" \
  -d '{
    "LIBELLE": "ECRIRE_RAPPORT"
  }'
```

Reponse 201:
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 2,
    "LIBELLE": "ECRIRE_RAPPORT",
    "IS_DELETE": false,
    "created_at": "2026-05-08T11:00:00.000000Z",
    "updated_at": "2026-05-08T11:00:00.000000Z",
    "deleted_at": null
  }
}
```

### 3) Recuperer une permission
- Methode: `GET`
- URL: `/v1/permissions/{id}`
- Autorisation: utilisateur authentifie

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/permissions/2" \
  -H "Authorization: Bearer <token>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "LIBELLE": "ECRIRE_RAPPORT",
    "IS_DELETE": false,
    "created_at": "2026-05-08T11:00:00.000000Z",
    "updated_at": "2026-05-08T11:00:00.000000Z",
    "deleted_at": null
  }
}
```

### 4) Mettre a jour une permission
- Methode: `PUT`
- URL: `/v1/permissions/{id}`
- Autorisation: `ADMIN`
- Content-Type: `application/json`

Body JSON:
- `LIBELLE` (string, optionnel, max 50, unique)

Exemple:
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/permissions/2" \
  -H "Authorization: Bearer <token_admin>" \
  -H "Content-Type: application/json" \
  -d '{
    "LIBELLE": "ECRIRE_RAPPORT_V2"
  }'
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "LIBELLE": "ECRIRE_RAPPORT_V2",
    "IS_DELETE": false,
    "created_at": "2026-05-08T11:00:00.000000Z",
    "updated_at": "2026-05-08T11:10:00.000000Z",
    "deleted_at": null
  }
}
```

### 5) Supprimer une permission
- Methode: `DELETE`
- URL: `/v1/permissions/{id}`
- Autorisation: `ADMIN`

Comportement:
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigne)

Exemple:
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/permissions/2" \
  -H "Authorization: Bearer <token_admin>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "LIBELLE": "ECRIRE_RAPPORT_V2",
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
    "The LIBELLE has already been taken."
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
  "erreurs": "La permission n'existe pas."
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
- Controller: `app/Http/Controllers/Api/V1/PermissionsController.php`
- Model: `app/Models/Permission.php`
- Policy: `app/Policies/PermissionsPolicy.php`


