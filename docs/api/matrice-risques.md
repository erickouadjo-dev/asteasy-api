# Documentation API - Matrice de Risque (Matrice Risque)

## Aperçu

Cette documentation décrit les endpoints CRUD de la ressource `matrice-risques` associée à la table `TB_MATRICE_RISQUE`.

- Racine endpoints : `https://asteasy.deepinovia.com/api/api`
- Préfixe API : `/v1`
- Ressource : `/matrice-risques`
- Middleware de groupe : `cors`, `multi_authentication`
- **Isolation Multi-Tenant** : Non applicable.
- Policy :
  - Lecture (`index`, `show`) : tout utilisateur authentifié.
  - Écriture (`store`, `update`, `destroy`) : utilisateur de type `ADMIN` ou `POWER_USER`.

---

## Structure de la ressource MatriceRisque

```json
{
  "ID": 1,
  "CODE": "HIGH",
  "COULEUR_CODE": "#FF0000",
  "IS_DELETE": false,
  "created_at": "2026-07-09T14:00:00.000000Z",
  "updated_at": "2026-07-09T14:00:00.000000Z",
  "deleted_at": null
}
```

---

## Endpoints

### 1) Lister les matrices de risque
- Méthode : `GET`
- URL : `/v1/matrice-risques`
- Autorisation : utilisateur authentifié

Paramètres query optionnels :
- `per_page` (int, défaut : `15`)
- `page` (int, défaut : `1`)
- `search` (string, filtre sur `CODE` et `COULEUR_CODE`)

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/matrice-risques?per_page=10&page=1&search=HIGH" \
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
      "CODE": "HIGH",
      "COULEUR_CODE": "#FF0000",
      "IS_DELETE": false,
      "created_at": "2026-07-09T14:00:00.000000Z",
      "updated_at": "2026-07-09T14:00:00.000000Z",
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

### 2) Créer une matrice de risque
- Méthode : `POST`
- URL : `/v1/matrice-risques`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
* `CODE` (requis, string, max 255, unique dans `TB_MATRICE_RISQUE`)
* `COULEUR_CODE` (requis, string, max 255)

Exemple :
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/matrice-risques" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "CODE": "HIGH",
    "COULEUR_CODE": "#FF0000"
  }'
```

Réponse 201 :
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 1,
    "CODE": "HIGH",
    "COULEUR_CODE": "#FF0000",
    "IS_DELETE": false,
    "created_at": "2026-07-09T14:00:00.000000Z",
    "updated_at": "2026-07-09T14:00:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 3) Récupérer une matrice de risque
- Méthode : `GET`
- URL : `/v1/matrice-risques/{id}`
- Autorisation : utilisateur authentifié

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/matrice-risques/1" \
  -H "Authorization: Bearer <token>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "CODE": "HIGH",
    "COULEUR_CODE": "#FF0000",
    "IS_DELETE": false,
    "created_at": "2026-07-09T14:00:00.000000Z",
    "updated_at": "2026-07-09T14:00:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 4) Mettre à jour une matrice de risque
- Méthode : `PUT`
- URL : `/v1/matrice-risques/{id}`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
- tous les champs sont optionnels

Exemple :
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/matrice-risques/1" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "COULEUR_CODE": "#CC0000"
  }'
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "CODE": "HIGH",
    "COULEUR_CODE": "#CC0000",
    "IS_DELETE": false,
    "created_at": "2026-07-09T14:00:00.000000Z",
    "updated_at": "2026-07-09T14:15:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 5) Supprimer une matrice de risque
- Méthode : `DELETE`
- URL : `/v1/matrice-risques/{id}`
- Autorisation : `ADMIN` ou `POWER_USER`

Comportement :
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigné)

Exemple :
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/matrice-risques/1" \
  -H "Authorization: Bearer <token_admin_ou_power_user>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "CODE": "HIGH",
    "COULEUR_CODE": "#CC0000",
    "IS_DELETE": true,
    "created_at": "2026-07-09T14:00:00.000000Z",
    "updated_at": "2026-07-09T14:15:00.000000Z",
    "deleted_at": "2026-07-09T14:20:00.000000Z"
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
    "The CODE field is required.",
    "The COULEUR CODE field is required."
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
  "erreurs": "La matrice de risque n'existe pas."
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

---

## Sources techniques
- Routes : `routes/api.php`
- Contrôleur : `app/Http/Controllers/Api/V1/MatriceRisquesController.php`
- Modèle : `app/Models/MatriceRisque.php`
- Stratégie d'autorisation (Policy) : `app/Policies/MatriceRisquesPolicy.php`
