# Documentation API - Étiquettes / Tags (Targ Etiquettes)

## Aperçu

Cette documentation décrit les endpoints CRUD de la ressource `targ-etiquettes` associée à la table `TB_TARG_ETIQUETTE`.

- Racine endpoints : `https://asteasy.deepinovia.com/api/api`
- Préfixe API : `/v1`
- Ressource : `/targ-etiquettes`
- Middleware de groupe : `cors`, `multi_authentication`
- **Isolation Multi-Tenant** : Non applicable.
- Policy :
  - Lecture (`index`, `show`) : tout utilisateur authentifié.
  - Écriture (`store`, `update`, `destroy`) : utilisateur de type `ADMIN` ou `POWER_USER`.

---

## Structure de la ressource TargEtiquette

```json
{
  "ID": 1,
  "TAG": "Important",
  "DESCRIPTION": "Étiquette pour les événements ou actions prioritaires.",
  "FAMILLE_ID": 2,
  "IS_DELETE": false,
  "created_at": "2026-07-09T14:00:00.000000Z",
  "updated_at": "2026-07-09T14:00:00.000000Z",
  "deleted_at": null
}
```

---

## Endpoints

### 1) Lister les étiquettes
- Méthode : `GET`
- URL : `/v1/targ-etiquettes`
- Autorisation : utilisateur authentifié

Paramètres query optionnels :
- `per_page` (int, défaut : `15`)
- `page` (int, défaut : `1`)
- `search` (string, filtre sur `TAG` et `DESCRIPTION`)

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/targ-etiquettes?per_page=10&page=1&search=Important" \
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
      "TAG": "Important",
      "DESCRIPTION": "Étiquette pour les événements ou actions prioritaires.",
      "FAMILLE_ID": 2,
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

### 2) Créer une étiquette
- Méthode : `POST`
- URL : `/v1/targ-etiquettes`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
* `TAG` (requis, string, max 255, unique dans `TB_TARG_ETIQUETTE`)
* `DESCRIPTION` (requis, string)
* `FAMILLE_ID` (optionnel, integer, doit exister dans `TB_FAMILLE.ID`)

Exemple :
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/targ-etiquettes" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "TAG": "Important",
    "DESCRIPTION": "Étiquette pour les événements ou actions prioritaires."
  }'
```

Réponse 201 :
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 1,
    "TAG": "Important",
    "DESCRIPTION": "Étiquette pour les événements ou actions prioritaires.",
    "FAMILLE_ID": null,
    "IS_DELETE": false,
    "created_at": "2026-07-09T14:00:00.000000Z",
    "updated_at": "2026-07-09T14:00:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 3) Récupérer une étiquette
- Méthode : `GET`
- URL : `/v1/targ-etiquettes/{id}`
- Autorisation : utilisateur authentifié

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/targ-etiquettes/1" \
  -H "Authorization: Bearer <token>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "TAG": "Important",
    "DESCRIPTION": "Étiquette pour les événements ou actions prioritaires.",
    "FAMILLE_ID": null,
    "IS_DELETE": false,
    "created_at": "2026-07-09T14:00:00.000000Z",
    "updated_at": "2026-07-09T14:00:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 4) Mettre à jour une étiquette
- Méthode : `PUT`
- URL : `/v1/targ-etiquettes/{id}`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
- tous les champs sont optionnels

Exemple :
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/targ-etiquettes/1" \
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
    "ID": 1,
    "TAG": "Important",
    "DESCRIPTION": "Mise à jour de la description.",
    "FAMILLE_ID": null,
    "IS_DELETE": false,
    "created_at": "2026-07-09T14:00:00.000000Z",
    "updated_at": "2026-07-09T14:15:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 5) Supprimer une étiquette
- Méthode : `DELETE`
- URL : `/v1/targ-etiquettes/{id}`
- Autorisation : `ADMIN` ou `POWER_USER`

Comportement :
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigné)

Exemple :
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/targ-etiquettes/1" \
  -H "Authorization: Bearer <token_admin_ou_power_user>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "TAG": "Important",
    "DESCRIPTION": "Mise à jour de la description.",
    "FAMILLE_ID": null,
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
    "The TAG field is required.",
    "The DESCRIPTION field is required."
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
  "erreurs": "L'étiquette n'existe pas."
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
- Contrôleur : `app/Http/Controllers/Api/V1/TargEtiquettesController.php`
- Modèle : `app/Models/TargEtiquette.php`
- Stratégie d'autorisation (Policy) : `app/Policies/TargEtiquettesPolicy.php`
