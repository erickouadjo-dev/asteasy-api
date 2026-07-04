# Documentation API - Avancements

## Aperçu

- Racine endpoints : `https://asteasy.deepinovia.com/api/api`
- Préfixe API : `/v1`
- Ressource : `/avancements`
- Middleware de groupe : `cors`, `multi_authentication`
- **Isolation Multi-Tenant** : Non applicable (la table `TB_AVANCEMENT` ne contient pas de colonne `ENTREPRISE_ID` et n'est pas cloisonnée par locataire).
- Policy :
  - Lecture (`index`, `show`) : tout utilisateur authentifié.
  - Écriture (`store`, `update`, `destroy`) : utilisateur de type `ADMIN` ou `POWER_USER`.

## Structure de la ressource Avancement

```json
{
  "ID": 1,
  "LIBELLE": "Terminé",
  "DESCRIPTION": "La tâche ou l'action a été finalisée avec succès.",
  "IS_DELETE": false,
  "CREATED_BY": 2,
  "created_at": "2026-06-28T22:00:00.000000Z",
  "updated_at": "2026-06-28T22:00:00.000000Z",
  "deleted_at": null
}
```

## Endpoints

### 1) Lister les avancements
- Méthode : `GET`
- URL : `/v1/avancements`
- Autorisation : utilisateur authentifié

Paramètres query optionnels :
- `per_page` (int, défaut : `15`)
- `page` (int, défaut : `1`)
- `search` (string, filtre sur `LIBELLE` et `DESCRIPTION`)

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/avancements?per_page=10&page=1&search=Termine" \
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
      "LIBELLE": "Terminé",
      "DESCRIPTION": "La tâche ou l'action a été finalisée avec succès.",
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

### 2) Créer un avancement
- Méthode : `POST`
- URL : `/v1/avancements`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
- `LIBELLE` (requis, string, max 255, unique dans `TB_AVANCEMENT`)
- `DESCRIPTION` (requis, string)

Exemple :
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/avancements" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "LIBELLE": "En cours",
    "DESCRIPTION": "L action est actuellement en cours de traitement."
  }'
```

Réponse 201 (le champ `CREATED_BY` est automatiquement assigné avec l'ID de l'utilisateur authentifié) :
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 2,
    "LIBELLE": "En cours",
    "DESCRIPTION": "L action est actuellement en cours de traitement.",
    "IS_DELETE": false,
    "CREATED_BY": 2,
    "created_at": "2026-06-28T22:10:00.000000Z",
    "updated_at": "2026-06-28T22:10:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 3) Récupérer un avancement
- Méthode : `GET`
- URL : `/v1/avancements/{id}`
- Autorisation : utilisateur authentifié

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/avancements/2" \
  -H "Authorization: Bearer <token>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "LIBELLE": "En cours",
    "DESCRIPTION": "L action est actuellement en cours de traitement.",
    "IS_DELETE": false,
    "CREATED_BY": 2,
    "created_at": "2026-06-28T22:10:00.000000Z",
    "updated_at": "2026-06-28T22:10:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 4) Mettre à jour un avancement
- Méthode : `PUT`
- URL : `/v1/avancements/{id}`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
- tous les champs sont optionnels
- `LIBELLE` (optionnel, string, max 255, unique dans `TB_AVANCEMENT` sauf pour cet ID)
- `DESCRIPTION` (optionnel, string)

Exemple :
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/avancements/2" \
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
    "LIBELLE": "En cours",
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

### 5) Supprimer un avancement
- Méthode : `DELETE`
- URL : `/v1/avancements/{id}`
- Autorisation : `ADMIN` ou `POWER_USER`

Comportement :
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigné)

Exemple :
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/avancements/2" \
  -H "Authorization: Bearer <token_admin_ou_power_user>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "LIBELLE": "En cours",
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
  "erreurs": "L'avancement n'existe pas."
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
- Contrôleur : `app/Http/Controllers/Api/V1/AvancementsController.php`
- Modèle : `app/Models/Avancement.php`
- Stratégie d'autorisation (Policy) : `app/Policies/AvancementsPolicy.php`
