# API Documentation - Agrements

## Apercu

- Racine endpoints: `https://asteasy.deepinovia.com/api/api`
- Prefix API: `/v1`
- Ressource: `/agrements`
- Middleware de groupe: `cors`, `multi_authentication`
- **Isolation Multi-Tenant** : Cloisonnement automatique par entreprise. Chaque entreprise ne voit et ne gère que ses propres agréments (`ENTREPRISE_ID`). Les super-administrateurs système ont un accès global.
- Policy:
  - Lecture (`index`, `show`): utilisateur authentifie
  - Ecriture (`store`, `update`, `destroy`): utilisateur de type `ADMIN` ou `POWER_USER`

## Structure de la ressource Agrement

```json
{
  "ID": 1,
  "INTITULE": "Agrement ANTT",
  "DESCRIPTION": "Autorisation d'exploitation",
  "DATE_OBTENTION": "2026-01-15",
  "DATE_VALIDITE": "2027-01-15",
  "DELAI_RENOUVELLEMENT": 30,
  "DOCUMENTATION_ID": 12,
  "FICHIERS_IMAGES": "[\"/uploads/agrement_1.pdf\"]",
  "ENTREPRISE_ID": 2,
  "IS_DELETE": false,
  "created_at": "2026-05-12T10:00:00.000000Z",
  "updated_at": "2026-05-12T10:00:00.000000Z",
  "deleted_at": null
}
```

## Endpoints

### 1) Lister les agrements
- Methode: `GET`
- URL: `/v1/agrements`
- Autorisation: utilisateur authentifie

Parametres query optionnels:
- `per_page` (int, defaut: `15`)
- `page` (int, defaut: `1`)
- `search` (string, filtre sur `INTITULE`, `DESCRIPTION`, `ENTREPRISE_ID`)

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/agrements?per_page=10&page=1&search=ANTT" \
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
      "INTITULE": "Agrement ANTT",
      "DESCRIPTION": "Autorisation d'exploitation",
      "ENTREPRISE_ID": 2,
      "IS_DELETE": false,
      "created_at": "2026-05-12T10:00:00.000000Z",
      "updated_at": "2026-05-12T10:00:00.000000Z",
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

### 2) Creer un agrement
- Methode: `POST`
- URL: `/v1/agrements`
- Autorisation: `ADMIN` ou `POWER_USER`
- Content-Type: `application/json`

Body JSON:
- `INTITULE` (required, string, max 500)
- `DESCRIPTION` (optionnel, string)
- `DATE_OBTENTION` (optionnel, date)
- `DATE_VALIDITE` (optionnel, date)
- `DELAI_RENOUVELLEMENT` (optionnel, integer)
- `DOCUMENTATION_ID` (optionnel, integer, exists `TB_DOCUMENTATION.ID`)
- `FICHIERS_IMAGES` (required, string)
- `ENTREPRISE_ID` (optionnel, integer, exists `TB_ENTREPRISE.ID`)

Exemple:
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/agrements" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "INTITULE": "Agrement ANTT",
    "DESCRIPTION": "Autorisation d'exploitation",
    "DATE_OBTENTION": "2026-01-15",
    "DATE_VALIDITE": "2027-01-15",
    "DELAI_RENOUVELLEMENT": 30,
    "DOCUMENTATION_ID": 12,
    "FICHIERS_IMAGES": "[\"/uploads/agrement_1.pdf\"]",
    "ENTREPRISE_ID": 2
  }'
```

Reponse 201:
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 2,
    "INTITULE": "Agrement ANTT",
    "DESCRIPTION": "Autorisation d'exploitation",
    "DATE_OBTENTION": "2026-01-15",
    "DATE_VALIDITE": "2027-01-15",
    "DELAI_RENOUVELLEMENT": 30,
    "DOCUMENTATION_ID": 12,
    "FICHIERS_IMAGES": "[\"/uploads/agrement_1.pdf\"]",
    "ENTREPRISE_ID": 2,
    "IS_DELETE": false,
    "created_at": "2026-05-12T10:10:00.000000Z",
    "updated_at": "2026-05-12T10:10:00.000000Z",
    "deleted_at": null
  }
}
```

### 3) Recuperer un agrement
- Methode: `GET`
- URL: `/v1/agrements/{id}`
- Autorisation: utilisateur authentifie

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/agrements/2" \
  -H "Authorization: Bearer <token>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "INTITULE": "Agrement ANTT",
    "DESCRIPTION": "Autorisation d'exploitation",
    "DATE_OBTENTION": "2026-01-15",
    "DATE_VALIDITE": "2027-01-15",
    "DELAI_RENOUVELLEMENT": 30,
    "DOCUMENTATION_ID": 12,
    "FICHIERS_IMAGES": "[\"/uploads/agrement_1.pdf\"]",
    "ENTREPRISE_ID": 2,
    "IS_DELETE": false,
    "created_at": "2026-05-12T10:10:00.000000Z",
    "updated_at": "2026-05-12T10:10:00.000000Z",
    "deleted_at": null
  }
}
```

### 4) Mettre a jour un agrement
- Methode: `PUT`
- URL: `/v1/agrements/{id}`
- Autorisation: `ADMIN` ou `POWER_USER`
- Content-Type: `application/json`

Body JSON:
- tous les champs sont optionnels
- `DOCUMENTATION_ID` doit exister dans `TB_DOCUMENTATION.ID` si fourni
- `ENTREPRISE_ID` doit exister dans `TB_ENTREPRISE.ID` si fourni

Exemple:
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/agrements/2" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "DATE_VALIDITE": "2028-01-15",
    "DELAI_RENOUVELLEMENT": 60
  }'
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "INTITULE": "Agrement ANTT",
    "DATE_VALIDITE": "2028-01-15",
    "DELAI_RENOUVELLEMENT": 60,
    "updated_at": "2026-05-12T10:20:00.000000Z"
  }
}
```

### 5) Supprimer un agrement
- Methode: `DELETE`
- URL: `/v1/agrements/{id}`
- Autorisation: `ADMIN` ou `POWER_USER`

Comportement:
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigne)

Exemple:
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/agrements/2" \
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
    "deleted_at": "2026-05-12T10:30:00.000000Z"
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
    "The INTITULE field is required.",
    "The FICHIERS IMAGES field is required."
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
  "erreurs": "L'agrement n'existe pas."
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
- Controller: `app/Http/Controllers/Api/V1/AgrementsController.php`
- Model: `app/Models/Agrement.php`
- Policy: `app/Policies/AgrementsPolicy.php`


