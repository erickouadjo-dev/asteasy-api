# API Documentation - Bases

## Apercu

- Racine endpoints: `https://asteasy.deepinovia.com/api/api`
- Prefix API: `/v1`
- Ressource: `/bases`
- Middleware de groupe: `cors`, `multi_authentication`
- Policy:
  - Lecture (`index`, `show`): utilisateur authentifie
  - Ecriture (`store`, `update`, `destroy`): utilisateur de type `ADMIN` ou `POWER_USER`

## Structure de la ressource Base

```json
{
  "ID": 1,
  "INTITULE": "Base Cotonou",
  "ADRESSE_1": "Rue 123",
  "ADRESSE_2": null,
  "ADRESSE_3": null,
  "CODE_POSTAL": "BP 01",
  "VILLE": "Cotonou",
  "PAYS": "Benin",
  "TELEPHONE": "+22990000000",
  "COURRIEL": "base@example.com",
  "FICHIERS_IMAGES": null,
  "TYPE_BASE": "PRINCIPALE",
  "ACTIVITES": "Logistique",
  "ENTREPRISE_ID": 1,
  "IS_DELETE": false,
  "created_at": "2026-05-08T10:00:00.000000Z",
  "updated_at": "2026-05-08T10:00:00.000000Z",
  "deleted_at": null
}
```

## Endpoints

### 1) Lister les bases
- Methode: `GET`
- URL: `/v1/bases`
- Autorisation: utilisateur authentifie

Parametres query optionnels:
- `per_page` (int, defaut: `15`)
- `page` (int, defaut: `1`)
- `search` (string, filtre sur `INTITULE`, `VILLE`, `PAYS`, `TELEPHONE`, `COURRIEL`, `TYPE_BASE`)

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/bases?per_page=10&page=1&search=principale" \
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
      "INTITULE": "Base Cotonou",
      "VILLE": "Cotonou",
      "PAYS": "Benin",
      "TYPE_BASE": "PRINCIPALE",
      "IS_DELETE": false,
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

### 2) Creer une base
- Methode: `POST`
- URL: `/v1/bases`
- Autorisation: `ADMIN` ou `POWER_USER`
- Content-Type: `application/json`

Body JSON:
- `INTITULE` (required, string, max 255, unique)
- `ADRESSE_1` (optionnel, string, max 255)
- `ADRESSE_2` (optionnel, string, max 255)
- `ADRESSE_3` (optionnel, string, max 255)
- `CODE_POSTAL` (optionnel, string, max 255)
- `VILLE` (optionnel, string, max 255)
- `PAYS` (optionnel, string, max 255)
- `TELEPHONE` (optionnel, string, max 255)
- `COURRIEL` (optionnel, email, max 255)
- `FICHIERS_IMAGES` (optionnel, string)
- `TYPE_BASE` (optionnel, `PRINCIPALE` | `SCONDAIRE` | `SITE_EN_LIGNE`)
- `ACTIVITES` (optionnel, string)
- `ENTREPRISE_ID` (optionnel, integer, exists `TB_ENTREPRISE.ID`)
- `IS_DELETE` (optionnel, boolean)

Exemple:
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/bases" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "INTITULE": "Base Cotonou",
    "TYPE_BASE": "PRINCIPALE",
    "ENTREPRISE_ID": 1
  }'
```

Reponse 201:
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 2,
    "INTITULE": "Base Cotonou",
    "TYPE_BASE": "PRINCIPALE",
    "ENTREPRISE_ID": 1,
    "IS_DELETE": false,
    "created_at": "2026-05-08T11:00:00.000000Z",
    "updated_at": "2026-05-08T11:00:00.000000Z",
    "deleted_at": null
  }
}
```

### 3) Recuperer une base
- Methode: `GET`
- URL: `/v1/bases/{id}`
- Autorisation: utilisateur authentifie

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/bases/2" \
  -H "Authorization: Bearer <token>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "INTITULE": "Base Cotonou",
    "TYPE_BASE": "PRINCIPALE",
    "ENTREPRISE_ID": 1,
    "IS_DELETE": false,
    "deleted_at": null
  }
}
```

### 4) Mettre a jour une base
- Methode: `PUT`
- URL: `/v1/bases/{id}`
- Autorisation: `ADMIN` ou `POWER_USER`
- Content-Type: `application/json`

Body JSON:
- tous les champs sont optionnels
- `TYPE_BASE` doit etre `PRINCIPALE`, `SCONDAIRE` ou `SITE_EN_LIGNE` si fourni

Exemple:
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/bases/2" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "TYPE_BASE": "SCONDAIRE",
    "VILLE": "Porto-Novo"
  }'
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "TYPE_BASE": "SCONDAIRE",
    "VILLE": "Porto-Novo",
    "updated_at": "2026-05-08T11:10:00.000000Z"
  }
}
```

### 5) Supprimer une base
- Methode: `DELETE`
- URL: `/v1/bases/{id}`
- Autorisation: `ADMIN` ou `POWER_USER`

Comportement:
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigne)

Exemple:
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/bases/2" \
  -H "Authorization: Bearer <token_admin_ou_power_user>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "IS_DELETE": true,
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
    "The INTITULE field is required."
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
  "erreurs": "La base n'existe pas."
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
- Controller: `app/Http/Controllers/Api/V1/BasesController.php`
- Model: `app/Models/Base.php`
- Policy: `app/Policies/BasesPolicy.php`


