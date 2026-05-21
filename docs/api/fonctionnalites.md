# API Documentation - Fonctionnalites

## Apercu

- Racine endpoints: `https://asteasy.deepinovia.com/api`
- Prefix API: `/v1`
- Ressource: `/fonctionnalites`
- Middleware de groupe: `cors`, `multi_authentication`
- Policy:
  - Lecture (`index`, `show`): utilisateur authentifie
  - Ecriture (`store`, `update`, `destroy`): utilisateur de type `ADMIN`

## Structure de la ressource Fonctionnalite

```json
{
  "ID": 1,
  "LIBELLE": "Gestion des factures",
  "DESCRIPTION": "Permet de creer et gerer les factures",
  "LIEN": "https://exemple.com/fonctionnalites/factures",
  "MODULE_ID": 1,
  "IS_DELETE": false,
  "created_at": "2026-05-09T10:00:00.000000Z",
  "updated_at": "2026-05-09T10:00:00.000000Z",
  "deleted_at": null
}
```

## Endpoints

### 1) Lister les fonctionnalites
- Methode: `GET`
- URL: `/v1/fonctionnalites`
- Autorisation: utilisateur authentifie

Parametres query optionnels:
- `per_page` (int, defaut: `15`)
- `page` (int, defaut: `1`)
- `search` (string, filtre sur `LIBELLE`, `DESCRIPTION` et `MODULE_ID`)

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/fonctionnalites?per_page=10&page=1&search=facture" \
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
      "LIBELLE": "Gestion des factures",
      "DESCRIPTION": "Permet de creer et gerer les factures",
      "LIEN": "https://exemple.com/fonctionnalites/factures",
      "MODULE_ID": 1,
      "IS_DELETE": false,
      "created_at": "2026-05-09T10:00:00.000000Z",
      "updated_at": "2026-05-09T10:00:00.000000Z",
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

### 2) Creer une fonctionnalite
- Methode: `POST`
- URL: `/v1/fonctionnalites`
- Autorisation: `ADMIN`
- Content-Type: `application/json`

Body JSON:
- `LIBELLE` (string, optionnel, max 50, unique)
- `DESCRIPTION` (string, optionnel, max 255)
- `LIEN` (string, optionnel)
- `MODULE_ID` (integer, optionnel, doit exister dans `TB_MODULE`)

Exemple:
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/fonctionnalites" \
  -H "Authorization: Bearer <token_admin>" \
  -H "Content-Type: application/json" \
  -d '{
    "LIBELLE": "Gestion des factures",
    "DESCRIPTION": "Permet de creer et gerer les factures",
    "LIEN": "https://exemple.com/fonctionnalites/factures",
    "MODULE_ID": 1
  }'
```

Reponse 201:
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 1,
    "LIBELLE": "Gestion des factures",
    "DESCRIPTION": "Permet de creer et gerer les factures",
    "LIEN": "https://exemple.com/fonctionnalites/factures",
    "MODULE_ID": 1,
    "IS_DELETE": false,
    "created_at": "2026-05-09T10:00:00.000000Z",
    "updated_at": "2026-05-09T10:00:00.000000Z",
    "deleted_at": null
  }
}
```

### 3) Recuperer une fonctionnalite
- Methode: `GET`
- URL: `/v1/fonctionnalites/{id}`
- Autorisation: utilisateur authentifie

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/fonctionnalites/1" \
  -H "Authorization: Bearer <token>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "LIBELLE": "Gestion des factures",
    "DESCRIPTION": "Permet de creer et gerer les factures",
    "LIEN": "https://exemple.com/fonctionnalites/factures",
    "MODULE_ID": 1,
    "IS_DELETE": false,
    "created_at": "2026-05-09T10:00:00.000000Z",
    "updated_at": "2026-05-09T10:00:00.000000Z",
    "deleted_at": null
  }
}
```

### 4) Mettre a jour une fonctionnalite
- Methode: `PUT`
- URL: `/v1/fonctionnalites/{id}`
- Autorisation: `ADMIN`
- Content-Type: `application/json`

Body JSON:
- `LIBELLE` (string, optionnel, max 50, unique)
- `DESCRIPTION` (string, optionnel, max 255)
- `LIEN` (string, optionnel)
- `MODULE_ID` (integer, optionnel, doit exister dans `TB_MODULE`)

Exemple:
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/fonctionnalites/1" \
  -H "Authorization: Bearer <token_admin>" \
  -H "Content-Type: application/json" \
  -d '{
    "DESCRIPTION": "Permet de creer, modifier et archiver les factures",
    "LIEN": "https://exemple.com/fonctionnalites/factures-v2"
  }'
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "LIBELLE": "Gestion des factures",
    "DESCRIPTION": "Permet de creer, modifier et archiver les factures",
    "LIEN": "https://exemple.com/fonctionnalites/factures-v2",
    "MODULE_ID": 1,
    "IS_DELETE": false,
    "created_at": "2026-05-09T10:00:00.000000Z",
    "updated_at": "2026-05-09T10:30:00.000000Z",
    "deleted_at": null
  }
}
```

### 5) Supprimer une fonctionnalite
- Methode: `DELETE`
- URL: `/v1/fonctionnalites/{id}`
- Autorisation: `ADMIN`

Comportement:
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigne)

Exemple:
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/fonctionnalites/1" \
  -H "Authorization: Bearer <token_admin>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "LIBELLE": "Gestion des factures",
    "DESCRIPTION": "Permet de creer, modifier et archiver les factures",
    "LIEN": "https://exemple.com/fonctionnalites/factures-v2",
    "MODULE_ID": 1,
    "IS_DELETE": true,
    "created_at": "2026-05-09T10:00:00.000000Z",
    "updated_at": "2026-05-09T10:45:00.000000Z",
    "deleted_at": "2026-05-09T10:45:00.000000Z"
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
  "erreurs": "La fonctionnalite n'existe pas."
}
```

### 500 - Erreur serveur
```json
{
  "code_http": 500,
  "code_message": "ERR_SERVER",
  "erreurs": "Une erreur est survenue lors de la recuperation des fonctionnalites."
}
```


