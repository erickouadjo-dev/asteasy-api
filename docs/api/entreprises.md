# API Documentation - Entreprises

## Apercu

- Racine endpoints: `https://asteasy.deepinovia.com/api/api`
- Prefix API: `/v1`
- Ressource: `/entreprises`
- Middleware de groupe: `cors`, `multi_authentication`
- Policy:
  - Lecture (`index`, `show`): utilisateur authentifie
  - Ecriture (`store`, `update`, `destroy`): utilisateur de type `ADMIN` ou `POWER_USER`

## Structure de la ressource Entreprise

```json
{
  "ID": 1,
  "NON_SOCIETE": "ACME Sarl",
  "SITE_WEB": "https://acme.example.com",
  "TELEPHONE": "+22990000000",
  "FICHIER_LOGO": null,
  "IS_DELETE": false,
  "created_at": "2026-05-08T10:00:00.000000Z",
  "updated_at": "2026-05-08T10:00:00.000000Z",
  "deleted_at": null
}
```

## Endpoints

### 1) Lister les entreprises
- Methode: `GET`
- URL: `/v1/entreprises`
- Autorisation: utilisateur authentifie

Parametres query optionnels:
- `per_page` (int, defaut: `15`)
- `page` (int, defaut: `1`)
- `search` (string, filtre sur `NON_SOCIETE`, `SITE_WEB`, `TELEPHONE`)

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/entreprises?per_page=10&page=1&search=acme" \
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
      "NON_SOCIETE": "ACME Sarl",
      "SITE_WEB": "https://acme.example.com",
      "TELEPHONE": "+22990000000",
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

### 2) Creer une entreprise
- Methode: `POST`
- URL: `/v1/entreprises`
- Autorisation: `ADMIN` ou `POWER_USER`
- Content-Type: `application/json`

Body JSON:
- `NON_SOCIETE` (required, string, max 500, unique)
- `SITE_WEB` (optionnel, string, max 500)
- `TELEPHONE` (optionnel, string, max 500)
- `FICHIER_LOGO` (optionnel, string)

Exemple:
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/entreprises" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "NON_SOCIETE": "ACME Sarl",
    "SITE_WEB": "https://acme.example.com",
    "TELEPHONE": "+22990000000"
  }'
```

Reponse 201:
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 2,
    "NON_SOCIETE": "ACME Sarl",
    "SITE_WEB": "https://acme.example.com",
    "TELEPHONE": "+22990000000",
    "FICHIER_LOGO": null,
    "IS_DELETE": false,
    "created_at": "2026-05-08T11:00:00.000000Z",
    "updated_at": "2026-05-08T11:00:00.000000Z",
    "deleted_at": null
  }
}
```

### 3) Recuperer une entreprise
- Methode: `GET`
- URL: `/v1/entreprises/{id}`
- Autorisation: utilisateur authentifie

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/entreprises/2" \
  -H "Authorization: Bearer <token>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "NON_SOCIETE": "ACME Sarl",
    "SITE_WEB": "https://acme.example.com",
    "TELEPHONE": "+22990000000",
    "FICHIER_LOGO": null,
    "IS_DELETE": false,
    "created_at": "2026-05-08T11:00:00.000000Z",
    "updated_at": "2026-05-08T11:00:00.000000Z",
    "deleted_at": null
  }
}
```

### 4) Mettre a jour une entreprise
- Methode: `PUT`
- URL: `/v1/entreprises/{id}`
- Autorisation: `ADMIN` ou `POWER_USER`
- Content-Type: `application/json`

Body JSON:
- `NON_SOCIETE` (optionnel, string, max 500, unique en ignorant l'entreprise courante)
- `SITE_WEB` (optionnel, string, max 500)
- `TELEPHONE` (optionnel, string, max 500)
- `FICHIER_LOGO` (optionnel, string)

Exemple:
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/entreprises/2" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "TELEPHONE": "+22991111111",
    "SITE_WEB": "https://acme-v2.example.com"
  }'
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "NON_SOCIETE": "ACME Sarl",
    "SITE_WEB": "https://acme-v2.example.com",
    "TELEPHONE": "+22991111111",
    "IS_DELETE": false,
    "updated_at": "2026-05-08T11:10:00.000000Z"
  }
}
```

### 5) Supprimer une entreprise
- Methode: `DELETE`
- URL: `/v1/entreprises/{id}`
- Autorisation: `ADMIN` ou `POWER_USER`

Comportement:
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigne)

Exemple:
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/entreprises/2" \
  -H "Authorization: Bearer <token_admin_ou_power_user>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "NON_SOCIETE": "ACME Sarl",
    "IS_DELETE": true,
    "updated_at": "2026-05-08T11:15:00.000000Z",
    "deleted_at": "2026-05-08T11:15:00.000000Z"
  }
}
```

### 6) Lister les ressources d'une entreprise
- Methode: `GET`
- URL: `/v1/entreprises/{id}/ressources`
- Autorisation: utilisateur authentifie

Comportement:
- Retourne en une seule reponse les `bases`, `agrements`, `abonnements` et `employes` de l'entreprise.

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/entreprises/2/ressources" \
  -H "Authorization: Bearer <token>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "entreprise": {
      "ID": 2,
      "NON_SOCIETE": "ACME Sarl",
      "IS_DELETE": false
    },
    "bases": [
      {
        "ID": 10,
        "INTITULE": "Base Cotonou",
        "ENTREPRISE_ID": 2
      }
    ],
    "agrements": [
      {
        "ID": 5,
        "ENTREPRISE_ID": 2
      }
    ],
    "abonnements": [
      {
        "ID": 7,
        "ENTREPRISE_ID": 2,
        "PLAN_ID": 1,
        "STATUT": "actif"
      }
    ],
    "employes": [
      {
        "ID": 21,
        "ENTREPRISE_ID": 2,
        "STATUT": "ACTIF"
      }
    ],
    "totaux": {
      "bases": 1,
      "agrements": 1,
      "abonnements": 1,
      "employes": 1
    }
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
    "The NON SOCIETE field is required.",
    "The NON SOCIETE has already been taken."
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
  "erreurs": "L'entreprise n'existe pas."
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
- Controller: `app/Http/Controllers/Api/V1/EntreprisesController.php`
- Model: `app/Models/Entreprise.php`
- Policy: `app/Policies/EntreprisesPolicy.php`


