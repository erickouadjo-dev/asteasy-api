# Documentation API - Statuts

## Aperçu

- Racine endpoints : `https://asteasy.deepinovia.com/api/api`
- Préfixe API : `/v1`
- Ressource : `/statuts`
- Middleware de groupe : `cors`, `multi_authentication`
- **Isolation Multi-Tenant** : Non applicable (la table `TB_STATUT` ne contient pas de colonne `ENTREPRISE_ID` et n'est pas cloisonnée par locataire).
- Policy :
  - Lecture (`index`, `show`) : tout utilisateur authentifié.
  - Écriture (`store`, `update`, `destroy`) : utilisateur de type `ADMIN` ou `POWER_USER`.

## Structure de la ressource Statut

```json
{
  "ID": 1,
  "LIBELLE": "Actif",
  "DESCRIPTION": "Le statut indique que la ressource est utilisable.",
  "IS_DELETE": false,
  "CREATED_BY": 2,
  "created_at": "2026-06-28T22:00:00.000000Z",
  "updated_at": "2026-06-28T22:00:00.000000Z",
  "deleted_at": null
}
```

## Endpoints

### 1) Lister les statuts
- Méthode : `GET`
- URL : `/v1/statuts`
- Autorisation : utilisateur authentifié

Paramètres query optionnels :
- `per_page` (int, défaut : `15`)
- `page` (int, défaut : `1`)
- `search` (string, filtre sur `LIBELLE` et `DESCRIPTION`)

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/statuts?per_page=10&page=1&search=Actif" \
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
      "LIBELLE": "Actif",
      "DESCRIPTION": "Le statut indique que la ressource est utilisable.",
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

### 2) Créer un statut
- Méthode : `POST`
- URL : `/v1/statuts`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
- `LIBELLE` (requis, string, max 255, unique dans `TB_STATUT`)
- `DESCRIPTION` (requis, string)

Exemple :
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/statuts" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "LIBELLE": "Inactif",
    "DESCRIPTION": "La ressource est désactivée et indisponible."
  }'
```

Réponse 201 (le champ `CREATED_BY` est automatiquement assigné avec l'ID de l'utilisateur authentifié) :
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 2,
    "LIBELLE": "Inactif",
    "DESCRIPTION": "La ressource est désactivée et indisponible.",
    "IS_DELETE": false,
    "CREATED_BY": 2,
    "created_at": "2026-06-28T22:10:00.000000Z",
    "updated_at": "2026-06-28T22:10:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 3) Récupérer un statut
- Méthode : `GET`
- URL : `/v1/statuts/{id}`
- Autorisation : utilisateur authentifié

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/statuts/2" \
  -H "Authorization: Bearer <token>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "LIBELLE": "Inactif",
    "DESCRIPTION": "La ressource est désactivée et indisponible.",
    "IS_DELETE": false,
    "CREATED_BY": 2,
    "created_at": "2026-06-28T22:10:00.000000Z",
    "updated_at": "2026-06-28T22:10:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 4) Mettre à jour un statut
- Méthode : `PUT`
- URL : `/v1/statuts/{id}`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
- tous les champs sont optionnels
- `LIBELLE` (optionnel, string, max 255, unique dans `TB_STATUT` sauf pour cet ID)
- `DESCRIPTION` (optionnel, string)

Exemple :
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/statuts/2" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "DESCRIPTION": "Mise à jour de la description."
  }'
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "LIBELLE": "Inactif",
    "DESCRIPTION": "Mise à jour de la description.",
    "IS_DELETE": false,
    "CREATED_BY": 2,
    "created_at": "2026-06-28T22:10:00.000000Z",
    "updated_at": "2026-06-28T22:20:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 5) Supprimer un statut
- Méthode : `DELETE`
- URL : `/v1/statuts/{id}`
- Autorisation : `ADMIN` ou `POWER_USER`

Comportement :
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigné)

Exemple :
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/statuts/2" \
  -H "Authorization: Bearer <token_admin_ou_power_user>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "LIBELLE": "Inactif",
    "DESCRIPTION": "Mise à jour de la description.",
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
    "The LIBELLE field is required.",
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
  "erreurs": "Le statut n'existe pas."
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
- Contrôleur : `app/Http/Controllers/Api/V1/StatutsController.php`
- Modèle : `app/Models/Statut.php`
- Stratégie d'autorisation (Policy) : `app/Policies/StatutsPolicy.php`
