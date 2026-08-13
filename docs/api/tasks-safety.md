# Documentation API - Tâches de Sécurité (Tasks Safety)

## Aperçu

Cette documentation décrit les endpoints CRUD de la ressource `tasks-safety` associée à la table `TB_TASKS_SAFETY`.

- Racine endpoints : `https://asteasy.deepinovia.com/api/api`
- Préfixe API : `/v1`
- Ressource : `/tasks-safety`
- Middleware de groupe : `cors`, `multi_authentication`
- **Isolation Multi-Tenant** : Non applicable.
- Policy :
  - Lecture (`index`, `show`) : tout utilisateur authentifié.
  - Écriture (`store`, `update`, `destroy`) : utilisateur de type `ADMIN` ou `POWER_USER`.

---

## Structure de la ressource TasksSafety

```json
{
  "ID": 1,
  "ID_ORIGINE": 3,
  "REF_ACTION": "ACT-2026-004",
  "TASKS_TYPE": "PREVENTIF",
  "DATE_OUVERTURE": "2026-07-09",
  "DESCRIPTION_TASK": "Mettre en place le balisage de sécurité autour de la zone A.",
  "ID_PUBLICATION": null,
  "ID_RECURRENCE": 1,
  "ID_STATUT": 2,
  "ID_AVANCEMENT": 3,
  "DATE_BUTEE": "2026-07-31",
  "DATE_FERMETURE": null,
  "RAPPEL_DATE_BUTEE": "2026-07-28",
  "RESPONSABLE_PROPRIETAIRE": 15,
  "COMMENTAIRES_OBSERVATIONS": "Balisage plastique requis.",
  "FICHIERS_IMAGES": "http://example.com/images/safety.jpg",
  "ID_TAG_ETIQUETTE": null,
  "IS_DELETE": false,
  "created_at": "2026-07-09T12:00:00.000000Z",
  "updated_at": "2026-07-09T12:00:00.000000Z",
  "deleted_at": null
}
```

---

## Endpoints

### 1) Lister les tâches de sécurité
- Méthode : `GET`
- URL : `/v1/tasks-safety`
- Autorisation : utilisateur authentifié

Paramètres query optionnels :
- `per_page` (int, défaut : `15`)
- `page` (int, défaut : `1`)
- `search` (string, filtre sur `REF_ACTION` et `DESCRIPTION_TASK`)

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/tasks-safety?per_page=10&page=1&search=ACT-2026" \
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
      "ID_ORIGINE": 3,
      "REF_ACTION": "ACT-2026-004",
      "TASKS_TYPE": "PREVENTIF",
      "DATE_OUVERTURE": "2026-07-09",
      "DESCRIPTION_TASK": "Mettre en place le balisage de sécurité autour de la zone A.",
      "ID_PUBLICATION": null,
      "ID_RECURRENCE": 1,
      "ID_STATUT": 2,
      "ID_AVANCEMENT": 3,
      "DATE_BUTEE": "2026-07-31",
      "DATE_FERMETURE": null,
      "RAPPEL_DATE_BUTEE": "2026-07-28",
      "RESPONSABLE_PROPRIETAIRE": 15,
      "COMMENTAIRES_OBSERVATIONS": "Balisage plastique requis.",
      "FICHIERS_IMAGES": "http://example.com/images/safety.jpg",
      "ID_TAG_ETIQUETTE": null,
      "IS_DELETE": false,
      "created_at": "2026-07-09T12:00:00.000000Z",
      "updated_at": "2026-07-09T12:00:00.000000Z",
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

### 2) Créer une tâche de sécurité
- Méthode : `POST`
- URL : `/v1/tasks-safety`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
* `REF_ACTION` (requis, string, max 255)
* `TASKS_TYPE` (requis, string, doit être un parmi : `CURRATIF`, `PREVENTIF`, `AUTRE`)
* `DATE_OUVERTURE` (requis, date au format YYYY-MM-DD)
* `DESCRIPTION_TASK` (requis, string)
* `DATE_BUTEE` (requis, date au format YYYY-MM-DD)
* `ID_ORIGINE` (optionnel, integer, doit exister dans `TB_TYPE_ORIGINE_ACTION.ID`)
* `ID_RECURRENCE` (optionnel, integer, doit exister dans `TB_RECURRENCE.ID`)
* `ID_STATUT` (optionnel, integer, doit exister dans `TB_STATUT.ID`)
* `ID_AVANCEMENT` (optionnel, integer, doit exister dans `TB_AVANCEMENT.ID`)
* `DATE_FERMETURE` (optionnel, date au format YYYY-MM-DD)
* `RAPPEL_DATE_BUTEE` (optionnel, date au format YYYY-MM-DD)
* `RESPONSABLE_PROPRIETAIRE` (optionnel, integer, doit exister dans `utilisateurs.id`)
* `COMMENTAIRES_OBSERVATIONS` (optionnel, string)
* `FICHIERS_IMAGES` (optionnel, string)
* `ID_TAG_ETIQUETTE` (optionnel, integer)

Exemple :
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/tasks-safety" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "REF_ACTION": "ACT-2026-004",
    "TASKS_TYPE": "PREVENTIF",
    "DATE_OUVERTURE": "2026-07-09",
    "DESCRIPTION_TASK": "Mettre en place le balisage de sécurité autour de la zone A.",
    "DATE_BUTEE": "2026-07-31"
  }'
```

Réponse 201 :
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 1,
    "REF_ACTION": "ACT-2026-004",
    "TASKS_TYPE": "PREVENTIF",
    "DATE_OUVERTURE": "2026-07-09",
    "DESCRIPTION_TASK": "Mettre en place le balisage de sécurité autour de la zone A.",
    "DATE_BUTEE": "2026-07-31",
    "IS_DELETE": false,
    "created_at": "2026-07-09T12:00:00.000000Z",
    "updated_at": "2026-07-09T12:00:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 3) Récupérer une tâche de sécurité
- Méthode : `GET`
- URL : `/v1/tasks-safety/{id}`
- Autorisation : utilisateur authentifié

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/tasks-safety/1" \
  -H "Authorization: Bearer <token>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "REF_ACTION": "ACT-2026-004",
    "TASKS_TYPE": "PREVENTIF",
    "DATE_OUVERTURE": "2026-07-09",
    "DESCRIPTION_TASK": "Mettre en place le balisage de sécurité autour de la zone A.",
    "DATE_BUTEE": "2026-07-31",
    "IS_DELETE": false,
    "created_at": "2026-07-09T12:00:00.000000Z",
    "updated_at": "2026-07-09T12:00:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 4) Mettre à jour une tâche de sécurité
- Méthode : `PUT`
- URL : `/v1/tasks-safety/{id}`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
- tous les champs sont optionnels

Exemple :
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/tasks-safety/1" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "DESCRIPTION_TASK": "Mise à jour de la description."
  }'
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "REF_ACTION": "ACT-2026-004",
    "TASKS_TYPE": "PREVENTIF",
    "DATE_OUVERTURE": "2026-07-09",
    "DESCRIPTION_TASK": "Mise à jour de la description.",
    "DATE_BUTEE": "2026-07-31",
    "IS_DELETE": false,
    "created_at": "2026-07-09T12:00:00.000000Z",
    "updated_at": "2026-07-09T12:15:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 5) Supprimer une tâche de sécurité
- Méthode : `DELETE`
- URL : `/v1/tasks-safety/{id}`
- Autorisation : `ADMIN` ou `POWER_USER`

Comportement :
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigné)

Exemple :
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/tasks-safety/1" \
  -H "Authorization: Bearer <token_admin_ou_power_user>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "REF_ACTION": "ACT-2026-004",
    "TASKS_TYPE": "PREVENTIF",
    "DATE_OUVERTURE": "2026-07-09",
    "DESCRIPTION_TASK": "Mise à jour de la description.",
    "DATE_BUTEE": "2026-07-31",
    "IS_DELETE": true,
    "created_at": "2026-07-09T12:00:00.000000Z",
    "updated_at": "2026-07-09T12:15:00.000000Z",
    "deleted_at": "2026-07-09T12:20:00.000000Z"
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
    "The REF ACTION field is required.",
    "The TASKS TYPE field is required."
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
  "erreurs": "La tâche n'existe pas."
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
- Contrôleur : `app/Http/Controllers/Api/V1/TasksSafetyController.php`
- Modèle : `app/Models/TasksSafety.php`
- Stratégie d'autorisation (Policy) : `app/Policies/TasksSafetyPolicy.php`
