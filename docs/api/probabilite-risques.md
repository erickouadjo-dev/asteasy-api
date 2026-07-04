# Documentation API - Probabilités de risque

## Aperçu

- Racine endpoints : `https://asteasy.deepinovia.com/api/api`
- Préfixe API : `/v1`
- Ressource : `/probabilite-risques`
- Middleware de groupe : `cors`, `multi_authentication`
- **Isolation Multi-Tenant** : Non applicable (la table `TB_PROBABILITE_RISQUE` ne contient pas de colonne `ENTREPRISE_ID` et n'est pas cloisonnée par locataire).
- Policy :
  - Lecture (`index`, `show`) : tout utilisateur authentifié.
  - Écriture (`store`, `update`, `destroy`) : utilisateur de type `ADMIN` ou `POWER_USER`.

## Structure de la ressource Probabilité de risque

```json
{
  "ID": 1,
  "INTITULE": "Probable",
  "DESCRIPTION": "Le risque a de fortes chances de se réaliser.",
  "VALEUR": "B",
  "IS_DELETE": false,
  "CREATED_BY": 2,
  "created_at": "2026-06-28T22:00:00.000000Z",
  "updated_at": "2026-06-28T22:00:00.000000Z",
  "deleted_at": null
}
```

## Endpoints

### 1) Lister les probabilités de risque
- Méthode : `GET`
- URL : `/v1/probabilite-risques`
- Autorisation : utilisateur authentifié

Paramètres query optionnels :
- `per_page` (int, défaut : `15`)
- `page` (int, défaut : `1`)
- `search` (string, filtre sur `INTITULE`, `DESCRIPTION` et `VALEUR`)

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/probabilite-risques?per_page=10&page=1&search=Probable" \
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
      "INTITULE": "Probable",
      "DESCRIPTION": "Le risque a de fortes chances de se réaliser.",
      "VALEUR": "B",
      "IS_DELETE": false,
      "CREATED_BY": 2,
      "created_at": "2026-06-28T22:00:00.000000Z",
      "updated_at": "2026-06-28T22:00:00.000000Z",
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

### 2) Créer une probabilité de risque
- Méthode : `POST`
- URL : `/v1/probabilite-risques`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
- `INTITULE` (requis, string, max 255, unique dans `TB_PROBABILITE_RISQUE`)
- `DESCRIPTION` (requis, string)
- `VALEUR` (requis, string, max 255, chiffre ou lettre)

Exemple :
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/probabilite-risques" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "INTITULE": "Rare",
    "DESCRIPTION": "Le risque a très peu de chances de se réaliser.",
    "VALEUR": "A"
  }'
```

Réponse 201 (le champ `CREATED_BY` est automatiquement assigné avec l'ID de l'utilisateur authentifié) :
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 2,
    "INTITULE": "Rare",
    "DESCRIPTION": "Le risque a très peu de chances de se réaliser.",
    "VALEUR": "A",
    "IS_DELETE": false,
    "CREATED_BY": 2,
    "created_at": "2026-06-28T22:10:00.000000Z",
    "updated_at": "2026-06-28T22:10:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 3) Récupérer une probabilité de risque
- Méthode : `GET`
- URL : `/v1/probabilite-risques/{id}`
- Autorisation : utilisateur authentifié

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/probabilite-risques/2" \
  -H "Authorization: Bearer <token>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "INTITULE": "Rare",
    "DESCRIPTION": "Le risque a très peu de chances de se réaliser.",
    "VALEUR": "A",
    "IS_DELETE": false,
    "CREATED_BY": 2,
    "created_at": "2026-06-28T22:10:00.000000Z",
    "updated_at": "2026-06-28T22:10:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 4) Mettre à jour une probabilité de risque
- Méthode : `PUT`
- URL : `/v1/probabilite-risques/{id}`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
- tous les champs sont optionnels
- `INTITULE` (optionnel, string, max 255, unique dans `TB_PROBABILITE_RISQUE` sauf pour cet ID)
- `DESCRIPTION` (optionnel, string)
- `VALEUR` (optionnel, string, max 255)

Exemple :
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/probabilite-risques/2" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "VALEUR": "C"
  }'
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "INTITULE": "Rare",
    "DESCRIPTION": "Le risque a très peu de chances de se réaliser.",
    "VALEUR": "C",
    "IS_DELETE": false,
    "CREATED_BY": 2,
    "created_at": "2026-06-28T22:10:00.000000Z",
    "updated_at": "2026-06-28T22:20:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 5) Supprimer une probabilité de risque
- Méthode : `DELETE`
- URL : `/v1/probabilite-risques/{id}`
- Autorisation : `ADMIN` ou `POWER_USER`

Comportement :
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigné)

Exemple :
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/probabilite-risques/2" \
  -H "Authorization: Bearer <token_admin_ou_power_user>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "INTITULE": "Rare",
    "DESCRIPTION": "Le risque a très peu de chances de se réaliser.",
    "VALEUR": "C",
    "IS_DELETE": true,
    "CREATED_BY": 2,
    "created_at": "2026-06-28T22:10:00.000000Z",
    "updated_at": "2026-06-28T22:20:00.000000Z",
    "deleted_at": "2026-06-28T22:30:00.000000Z"
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
    "The VALEUR field is required."
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
  "erreurs": "La probabilité de risque n'existe pas."
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
- Contrôleur : `app/Http/Controllers/Api/V1/ProbabiliteRisquesController.php`
- Modèle : `app/Models/ProbabiliteRisque.php`
- Stratégie d'autorisation (Policy) : `app/Policies/ProbabiliteRisquesPolicy.php`
