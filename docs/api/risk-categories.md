# Documentation API - Catégories de Risque (Risk Categories)

## Aperçu

- Racine endpoints : `https://asteasy.deepinovia.com/api/api`
- Préfixe API : `/v1`
- Ressource : `/risk-categories`
- Middleware de groupe : `cors`, `multi_authentication`
- **Isolation Multi-Tenant** : Actif (la table `TB_RISK_CATEGORY` contient la colonne `ENTREPRISE_ID` et est filtrée par locataire via le trait `BelongsToTenant`).
- Policy :
  - Lecture (`index`, `show`) : tout utilisateur authentifié.
  - Écriture (`store`, `update`, `destroy`) : utilisateur de type `ADMIN` ou `POWER_USER`.

## Structure de la ressource Catégorie de Risque

```json
{
  "ID": 1,
  "CODE": "ENV",
  "INTITULE": "Risques Professionnels",
  "DESCRIPTION": "Catégorie regroupant les risques de l'environnement de travail direct.",
  "ENTREPRISE_ID": 1,
  "IS_DELETE": false,
  "created_at": "2026-08-13T13:00:00.000000Z",
  "updated_at": "2026-08-13T13:00:00.000000Z",
  "deleted_at": null
}
```

## Endpoints

### 1) Lister les catégories de risque
- Méthode : `GET`
- URL : `/v1/risk-categories`
- Autorisation : utilisateur authentifié

Paramètres query optionnels :
- `per_page` (int, défaut : `15`)
- `page` (int, défaut : `1`)
- `search` (string, filtre sur `INTITULE` et `DESCRIPTION`)

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/risk-categories?per_page=10&page=1&search=Professionnels" \
  -H "Authorization: Bearer <token>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": [
    {
      "ID": 1,
      "INTITULE": "Risques Professionnels",
      "DESCRIPTION": "Catégorie regroupant les risques de l'environnement de travail direct.",
      "ENTREPRISE_ID": 1,
      "IS_DELETE": false,
      "created_at": "2026-08-13T13:00:00.000000Z",
      "updated_at": "2026-08-13T13:00:00.000000Z",
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

---

### 2) Créer une catégorie de risque
- Méthode : `POST`
- URL : `/v1/risk-categories`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
- `INTITULE` (requis, string, max 255, unique dans `TB_RISK_CATEGORY`)
- `DESCRIPTION` (requis, string)
- `ENTREPRISE_ID` (optionnel, integer, doit exister dans `TB_ENTREPRISE`)

Exemple :
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/risk-categories" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "INTITULE": "Risques Environnementaux",
    "DESCRIPTION": "Risques liés aux facteurs climatiques et naturels."
  }'
```

Réponse 201 :
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 2,
    "INTITULE": "Risques Environnementaux",
    "DESCRIPTION": "Risques liés aux facteurs climatiques et naturels.",
    "ENTREPRISE_ID": 1,
    "IS_DELETE": false,
    "created_at": "2026-08-13T13:05:00.000000Z",
    "updated_at": "2026-08-13T13:05:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 3) Récupérer une catégorie de risque
- Méthode : `GET`
- URL : `/v1/risk-categories/{id}`
- Autorisation : utilisateur authentifié

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/risk-categories/2" \
  -H "Authorization: Bearer <token>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "INTITULE": "Risques Environnementaux",
    "DESCRIPTION": "Risques liés aux facteurs climatiques et naturels.",
    "ENTREPRISE_ID": 1,
    "IS_DELETE": false,
    "created_at": "2026-08-13T13:05:00.000000Z",
    "updated_at": "2026-08-13T13:05:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 4) Mettre à jour une catégorie de risque
- Méthode : `PUT`
- URL : `/v1/risk-categories/{id}`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
- tous les champs sont optionnels
- `INTITULE` (optionnel, string, max 255, unique dans `TB_RISK_CATEGORY` sauf pour cet ID)
- `DESCRIPTION` (optionnel, string)
- `ENTREPRISE_ID` (optionnel, integer, doit exister dans `TB_ENTREPRISE`)

Exemple :
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/risk-categories/2" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "DESCRIPTION": "Description mise à jour pour les risques naturels."
  }'
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "INTITULE": "Risques Environnementaux",
    "DESCRIPTION": "Description mise à jour pour les risques naturels.",
    "ENTREPRISE_ID": 1,
    "IS_DELETE": false,
    "created_at": "2026-08-13T13:05:00.000000Z",
    "updated_at": "2026-08-13T13:10:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 5) Supprimer une catégorie de risque
- Méthode : `DELETE`
- URL : `/v1/risk-categories/{id}`
- Autorisation : `ADMIN` ou `POWER_USER`

Comportement :
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigné)

Exemple :
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/risk-categories/2" \
  -H "Authorization: Bearer <token_admin_ou_power_user>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "INTITULE": "Risques Environnementaux",
    "DESCRIPTION": "Description mise à jour pour les risques naturels.",
    "ENTREPRISE_ID": 1,
    "IS_DELETE": true,
    "created_at": "2026-08-13T13:05:00.000000Z",
    "updated_at": "2026-08-13T13:10:00.000000Z",
    "deleted_at": "2026-08-13T13:15:00.000000Z"
  }
}
```

---

## Erreurs communes

### 400 - Validation
```json
{
  "code_http": 400,
  "code_message": "ERR_VALIDATION",
  "erreurs": [
    "The INTITULE field is required.",
    "The DESCRIPTION field is required."
  ]
}
```

Ou si le corps de la requête est vide ou invalide :
```json
{
  "code_http": 400,
  "code_message": "ERR_VALIDATION",
  "erreurs": "Corps de la requête vide."
}
```

### 403 - Non autorisé
```json
{
  "http_code": 403,
  "code": 403,
  "code_message": "Requete non autorisee."
}
```

### 404 - Non trouvé
```json
{
  "code_http": 404,
  "code_message": "ERR_NOT_FOUND",
  "erreurs": "La catégorie de risque n'existe pas."
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
- Routes : `routes/api.php`
- Contrôleur : `app/Http/Controllers/Api/V1/RiskCategoriesController.php`
- Modèle : `app/Models/RiskCategory.php`
- Stratégie d'autorisation (Policy) : `app/Policies/RiskCategoriesPolicy.php`
