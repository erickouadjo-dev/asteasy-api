# Documentation API - Sous-Catégories de Risque (Risk Subcategories)

## Aperçu

- Racine endpoints : `https://asteasy.deepinovia.com/api/api`
- Préfixe API : `/v1`
- Ressource : `/risk-subcategories`
- Middleware de groupe : `cors`, `multi_authentication`
- **Isolation Multi-Tenant** : Non applicable (la table `TB_RISK_SUBCATEGORY` ne contient pas de colonne `ENTREPRISE_ID` et n'est pas cloisonnée par locataire).
- Policy :
  - Lecture (`index`, `show`) : tout utilisateur authentifié.
  - Écriture (`store`, `update`, `destroy`) : utilisateur de type `ADMIN` ou `POWER_USER`.

## Structure de la ressource Sous-Catégorie de Risque

```json
{
  "ID": 1,
  "INTITULE": "Chute de hauteur",
  "DESCRIPTION": "Risques liés aux travaux en hauteur sans protection adéquate.",
  "ID_RISK_CATEGORY": 1,
  "IS_DELETE": false,
  "created_at": "2026-08-13T13:00:00.000000Z",
  "updated_at": "2026-08-13T13:00:00.000000Z",
  "deleted_at": null,
  "category": {
    "ID": 1,
    "INTITULE": "Risques Professionnels",
    "DESCRIPTION": "Catégorie regroupant les risques de l'environnement de travail direct.",
    "IS_DELETE": false,
    "created_at": "2026-08-13T13:00:00.000000Z",
    "updated_at": "2026-08-13T13:00:00.000000Z",
    "deleted_at": null
  }
}
```

## Endpoints

### 1) Lister les sous-catégories de risque
- Méthode : `GET`
- URL : `/v1/risk-subcategories`
- Autorisation : utilisateur authentifié

Paramètres query optionnels :
- `per_page` (int, défaut : `15`)
- `page` (int, défaut : `1`)
- `search` (string, filtre sur `INTITULE` et `DESCRIPTION`)

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/risk-subcategories?per_page=10&page=1&search=Chute" \
  -H "Authorization: Bearer <token>"
```

Réponse 200 (la relation `category` est chargée par défaut) :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": [
    {
      "ID": 1,
      "INTITULE": "Chute de hauteur",
      "DESCRIPTION": "Risques liés aux travaux en hauteur sans protection adéquate.",
      "ID_RISK_CATEGORY": 1,
      "IS_DELETE": false,
      "created_at": "2026-08-13T13:00:00.000000Z",
      "updated_at": "2026-08-13T13:00:00.000000Z",
      "deleted_at": null,
      "category": {
        "ID": 1,
        "INTITULE": "Risques Professionnels",
        "DESCRIPTION": "Catégorie regroupant les risques de l'environnement de travail direct.",
        "IS_DELETE": false,
        "created_at": "2026-08-13T13:00:00.000000Z",
        "updated_at": "2026-08-13T13:00:00.000000Z",
        "deleted_at": null
      }
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

### 2) Créer une sous-catégorie de risque
- Méthode : `POST`
- URL : `/v1/risk-subcategories`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
- `INTITULE` (requis, string, max 255, unique dans `TB_RISK_SUBCATEGORY`)
- `DESCRIPTION` (requis, string)
- `ID_RISK_CATEGORY` (requis, integer, doit exister dans la table `TB_RISK_CATEGORY` sous la colonne `ID`)

Exemple :
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/risk-subcategories" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "INTITULE": "Exposition aux produits chimiques",
    "DESCRIPTION": "Inhalation ou contact cutané avec des substances corrosives.",
    "ID_RISK_CATEGORY": 1
  }'
```

Réponse 201 (la relation `category` est renvoyée) :
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 2,
    "INTITULE": "Exposition aux produits chimiques",
    "DESCRIPTION": "Inhalation ou contact cutané avec des substances corrosives.",
    "ID_RISK_CATEGORY": 1,
    "IS_DELETE": false,
    "created_at": "2026-08-13T13:05:00.000000Z",
    "updated_at": "2026-08-13T13:05:00.000000Z",
    "deleted_at": null,
    "category": {
      "ID": 1,
      "INTITULE": "Risques Professionnels",
      "DESCRIPTION": "Catégorie regroupant les risques de l'environnement de travail direct.",
      "IS_DELETE": false,
      "created_at": "2026-08-13T13:00:00.000000Z",
      "updated_at": "2026-08-13T13:00:00.000000Z",
      "deleted_at": null
    }
  }
}
```

---

### 3) Récupérer une sous-catégorie de risque
- Méthode : `GET`
- URL : `/v1/risk-subcategories/{id}`
- Autorisation : utilisateur authentifié

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/risk-subcategories/2" \
  -H "Authorization: Bearer <token>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "INTITULE": "Exposition aux produits chimiques",
    "DESCRIPTION": "Inhalation ou contact cutané avec des substances corrosives.",
    "ID_RISK_CATEGORY": 1,
    "IS_DELETE": false,
    "created_at": "2026-08-13T13:05:00.000000Z",
    "updated_at": "2026-08-13T13:05:00.000000Z",
    "deleted_at": null,
    "category": {
      "ID": 1,
      "INTITULE": "Risques Professionnels",
      "DESCRIPTION": "Catégorie regroupant les risques de l'environnement de travail direct.",
      "IS_DELETE": false,
      "created_at": "2026-08-13T13:00:00.000000Z",
      "updated_at": "2026-08-13T13:00:00.000000Z",
      "deleted_at": null
    }
  }
}
```

---

### 4) Mettre à jour une sous-catégorie de risque
- Méthode : `PUT`
- URL : `/v1/risk-subcategories/{id}`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
- tous les champs sont optionnels
- `INTITULE` (optionnel, string, max 255, unique dans `TB_RISK_SUBCATEGORY` sauf pour cet ID)
- `DESCRIPTION` (optionnel, string)
- `ID_RISK_CATEGORY` (optionnel, integer, doit exister dans `TB_RISK_CATEGORY` sous la colonne `ID`)

Exemple :
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/risk-subcategories/2" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "DESCRIPTION": "Nouvelle description mise à jour."
  }'
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "INTITULE": "Exposition aux produits chimiques",
    "DESCRIPTION": "Nouvelle description mise à jour.",
    "ID_RISK_CATEGORY": 1,
    "IS_DELETE": false,
    "created_at": "2026-08-13T13:05:00.000000Z",
    "updated_at": "2026-08-13T13:10:00.000000Z",
    "deleted_at": null,
    "category": {
      "ID": 1,
      "INTITULE": "Risques Professionnels",
      "DESCRIPTION": "Catégorie regroupant les risques de l'environnement de travail direct.",
      "IS_DELETE": false,
      "created_at": "2026-08-13T13:00:00.000000Z",
      "updated_at": "2026-08-13T13:00:00.000000Z",
      "deleted_at": null
    }
  }
}
```

---

### 5) Supprimer une sous-catégorie de risque
- Méthode : `DELETE`
- URL : `/v1/risk-subcategories/{id}`
- Autorisation : `ADMIN` ou `POWER_USER`

Comportement :
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigné)

Exemple :
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/risk-subcategories/2" \
  -H "Authorization: Bearer <token_admin_ou_power_user>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "INTITULE": "Exposition aux produits chimiques",
    "DESCRIPTION": "Nouvelle description mise à jour.",
    "ID_RISK_CATEGORY": 1,
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
    "The DESCRIPTION field is required.",
    "The ID RISK CATEGORY field is required."
  ]
}
```

Ou si le corps de la requête est vide ou si la catégorie n'existe pas :
```json
{
  "code_http": 400,
  "code_message": "ERR_VALIDATION",
  "erreurs": [
    "The selected id risk category is invalid."
  ]
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
  "erreurs": "La sous-catégorie de risque n'existe pas."
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
- Contrôleur : `app/Http/Controllers/Api/V1/RiskSubcategoriesController.php`
- Modèle : `app/Models/RiskSubcategory.php`
- Stratégie d'autorisation (Policy) : `app/Policies/RiskSubcategoriesPolicy.php`
