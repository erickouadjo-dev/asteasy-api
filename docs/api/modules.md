# API Documentation - Modules

## Apercu

- Racine endpoints: `https://asteasy.deepinovia.com/api/api`
- Prefix API: `/v1`
- Ressource: `/modules`
- Middleware de groupe: `cors`, `multi_authentication`
- Policy:
  - Lecture (`index`, `show`): utilisateur authentifie
  - Ecriture (`store`, `update`, `destroy`): utilisateur de type `ADMIN`

## Structure de la ressource Module

```json
{
  "ID": 1,
  "LIBELLE": "Comptabilite",
  "DESCRIPTION": "Module de gestion comptable",
  "LIEN": "https://exemple.com/modules/comptabilite",
  "IS_DELETE": false,
  "created_at": "2026-05-09T10:00:00.000000Z",
  "updated_at": "2026-05-09T10:00:00.000000Z",
  "deleted_at": null
}
```

## Endpoints

### 1) Lister les modules
- Methode: `GET`
- URL: `/v1/modules`
- Autorisation: utilisateur authentifie

Parametres query optionnels:
- `per_page` (int, defaut: `15`)
- `page` (int, defaut: `1`)
- `search` (string, filtre sur `LIBELLE` et `DESCRIPTION`)

> La recherche porte sur `LIBELLE` et `DESCRIPTION`.

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/modules?per_page=10&page=1&search=compta" \
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
      "LIBELLE": "Comptabilite",
      "DESCRIPTION": "Module de gestion comptable",
      "LIEN": "https://exemple.com/modules/comptabilite",
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

### 2) Creer un module
- Methode: `POST`
- URL: `/v1/modules`
- Autorisation: `ADMIN`
- Content-Type: `application/json`

Body JSON:
- `LIBELLE` (string, optionnel, max 50, unique)
- `DESCRIPTION` (string, optionnel)
- `LIEN` (string, optionnel)

Exemple:
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/modules" \
  -H "Authorization: Bearer <token_admin>" \
  -H "Content-Type: application/json" \
  -d '{
    "LIBELLE": "Ressources Humaines",
    "DESCRIPTION": "Module de suivi RH",
    "LIEN": "https://exemple.com/modules/rh"
  }'
```

Reponse 201:
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 2,
    "LIBELLE": "Ressources Humaines",
    "DESCRIPTION": "Module de suivi RH",
    "LIEN": "https://exemple.com/modules/rh",
    "IS_DELETE": false,
    "created_at": "2026-05-09T11:00:00.000000Z",
    "updated_at": "2026-05-09T11:00:00.000000Z",
    "deleted_at": null
  }
}
```

### 3) Recuperer un module
- Methode: `GET`
- URL: `/v1/modules/{id}`
- Autorisation: utilisateur authentifie

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/modules/2" \
  -H "Authorization: Bearer <token>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "LIBELLE": "Ressources Humaines",
    "DESCRIPTION": "Module de suivi RH",
    "LIEN": "https://exemple.com/modules/rh",
    "IS_DELETE": false,
    "created_at": "2026-05-09T11:00:00.000000Z",
    "updated_at": "2026-05-09T11:00:00.000000Z",
    "deleted_at": null
  }
}
```

### 4) Mettre a jour un module
- Methode: `PUT`
- URL: `/v1/modules/{id}`
- Autorisation: `ADMIN`
- Content-Type: `application/json`

Body JSON:
- `LIBELLE` (string, optionnel, max 50, unique)
- `DESCRIPTION` (string, optionnel)
- `LIEN` (string, optionnel)

Exemple:
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/modules/2" \
  -H "Authorization: Bearer <token_admin>" \
  -H "Content-Type: application/json" \
  -d '{
    "DESCRIPTION": "Module RH et gestion des equipes"
  }'
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "LIBELLE": "Ressources Humaines",
    "DESCRIPTION": "Module RH et gestion des equipes",
    "LIEN": "https://exemple.com/modules/rh",
    "IS_DELETE": false,
    "created_at": "2026-05-09T11:00:00.000000Z",
    "updated_at": "2026-05-09T11:10:00.000000Z",
    "deleted_at": null
  }
}
```

### 5) Supprimer un module
- Methode: `DELETE`
- URL: `/v1/modules/{id}`
- Autorisation: `ADMIN`

Comportement:
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigne)

Exemple:
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/modules/2" \
  -H "Authorization: Bearer <token_admin>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "LIBELLE": "Ressources Humaines",
    "DESCRIPTION": "Module RH et gestion des equipes",
    "LIEN": "https://exemple.com/modules/rh",
    "IS_DELETE": true,
    "created_at": "2026-05-09T11:00:00.000000Z",
    "updated_at": "2026-05-09T11:15:00.000000Z",
    "deleted_at": "2026-05-09T11:15:00.000000Z"
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
  "erreurs": "Le module n'existe pas."
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
- Controller: `app/Http/Controllers/Api/V1/ModulesController.php`
- Model: `app/Models/Module.php`
- Policy: `app/Policies/ModulesPolicy.php`


