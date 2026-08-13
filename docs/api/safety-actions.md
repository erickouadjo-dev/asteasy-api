# Documentation API - Actions de Sécurité (Safety Actions)

## Aperçu

Cette documentation décrit les endpoints CRUD de la ressource `safety-actions` associée à la table `TB_SAFETY_ACTION`.

- Racine endpoints : `https://asteasy.deepinovia.com/api/api`
- Préfixe API : `/v1`
- Ressource : `/safety-actions`
- Middleware de groupe : `cors`, `multi_authentication`
- **Isolation Multi-Tenant** : Non applicable.
- Policy :
  - Lecture (`index`, `show`) : tout utilisateur authentifié.
  - Écriture (`store`, `update`, `destroy`) : utilisateur de type `ADMIN` ou `POWER_USER`.

---

## Structure de la ressource SafetyAction

```json
{
  "ID": 1,
  "INTITULE": "Vérification des extincteurs",
  "DESCRIPTION": "Faire l'inspection trimestrielle de tous les extincteurs portatifs.",
  "DATE_OUVERTURE": "2026-07-09",
  "ID_TYPE_ORIGINE_ACTION": 2,
  "ID_TASK_SAFETY": 1,
  "FICHIERS_IMAGES": "http://example.com/images/action.jpg",
  "ID_RECURRENCE": 1,
  "ID_STATUT": 2,
  "ID_AVANCEMENT": 3,
  "DATE_CLOTURE": null,
  "RESPONSABLE": 15,
  "ID_TAG": null,
  "ACTION_LIEE_RISQUE": "NON",
  "ID_RISQUE": null,
  "ACTION_FREQUENCE_RISQUE": "NON_APPLICABLE",
  "ACTION_GRAVITE_RISQUE": "NON_APPLICABLE",
  "IS_DELETE": false,
  "created_at": "2026-07-09T12:00:00.000000Z",
  "updated_at": "2026-07-09T12:00:00.000000Z",
  "deleted_at": null
}
```

---

## Endpoints

### 1) Lister les actions de sécurité
- Méthode : `GET`
- URL : `/v1/safety-actions`
- Autorisation : utilisateur authentifié

Paramètres query optionnels :
- `per_page` (int, défaut : `15`)
- `page` (int, défaut : `1`)
- `search` (string, filtre sur `INTITULE` et `DESCRIPTION`)

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/safety-actions?per_page=10&page=1&search=Vérification" \
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
      "INTITULE": "Vérification des extincteurs",
      "DESCRIPTION": "Faire l'inspection trimestrielle de tous les extincteurs portatifs.",
      "DATE_OUVERTURE": "2026-07-09",
      "ID_TYPE_ORIGINE_ACTION": 2,
      "ID_TASK_SAFETY": 1,
      "FICHIERS_IMAGES": "http://example.com/images/action.jpg",
      "ID_RECURRENCE": 1,
      "ID_STATUT": 2,
      "ID_AVANCEMENT": 3,
      "DATE_CLOTURE": null,
      "RESPONSABLE": 15,
      "ID_TAG": null,
      "ACTION_LIEE_RISQUE": "NON",
      "ID_RISQUE": null,
      "ACTION_FREQUENCE_RISQUE": "NON_APPLICABLE",
      "ACTION_GRAVITE_RISQUE": "NON_APPLICABLE",
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

### 2) Créer une action de sécurité
- Méthode : `POST`
- URL : `/v1/safety-actions`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
* `INTITULE` (requis, string, max 255)
* `DESCRIPTION` (requis, string)
* `DATE_OUVERTURE` (requis, date au format YYYY-MM-DD)
* `ACTION_LIEE_RISQUE` (requis, string, doit être un parmi : `OUI`, `NON`, `NON_APPLICABLE`)
* `ACTION_FREQUENCE_RISQUE` (requis, string, doit être un parmi : `OUI`, `NON`, `NON_APPLICABLE`)
* `ACTION_GRAVITE_RISQUE` (requis, string, doit être un parmi : `OUI`, `NON`, `NON_APPLICABLE`)
* `ID_TYPE_ORIGINE_ACTION` (optionnel, integer, doit exister dans `TB_TYPE_ORIGINE_ACTION.ID`)
* `ID_TASK_SAFETY` (optionnel, integer, doit exister dans `TB_TASKS_SAFETY.ID`)
* `FICHIERS_IMAGES` (optionnel, string)
* `ID_RECURRENCE` (optionnel, integer, doit exister dans `TB_RECURRENCE.ID`)
* `ID_STATUT` (optionnel, integer, doit exister dans `TB_STATUT.ID`)
* `ID_AVANCEMENT` (optionnel, integer, doit exister dans `TB_AVANCEMENT.ID`)
* `DATE_CLOTURE` (optionnel, date au format YYYY-MM-DD)
* `RESPONSABLE` (optionnel, integer, doit exister dans `utilisateurs.id`)
* `ID_TAG` (optionnel, integer, doit exister dans `TB_TAG_ETIQUETTE.ID`)
* `ID_RISQUE` (optionnel, integer, doit exister dans `TB_RISQUES.ID`)

Exemple :
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/safety-actions" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "INTITULE": "Vérification des extincteurs",
    "DESCRIPTION": "Faire l inspection trimestrielle de tous les extincteurs portatifs.",
    "DATE_OUVERTURE": "2026-07-09",
    "ACTION_LIEE_RISQUE": "NON",
    "ACTION_FREQUENCE_RISQUE": "NON_APPLICABLE",
    "ACTION_GRAVITE_RISQUE": "NON_APPLICABLE"
  }'
```

Réponse 201 :
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 1,
    "INTITULE": "Vérification des extincteurs",
    "DESCRIPTION": "Faire l inspection trimestrielle de tous les extincteurs portatifs.",
    "DATE_OUVERTURE": "2026-07-09",
    "ACTION_LIEE_RISQUE": "NON",
    "ACTION_FREQUENCE_RISQUE": "NON_APPLICABLE",
    "ACTION_GRAVITE_RISQUE": "NON_APPLICABLE",
    "IS_DELETE": false,
    "created_at": "2026-07-09T12:00:00.000000Z",
    "updated_at": "2026-07-09T12:00:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 3) Récupérer une action de sécurité
- Méthode : `GET`
- URL : `/v1/safety-actions/{id}`
- Autorisation : utilisateur authentifié

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/safety-actions/1" \
  -H "Authorization: Bearer <token>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "INTITULE": "Vérification des extincteurs",
    "DESCRIPTION": "Faire l inspection trimestrielle de tous les extincteurs portatifs.",
    "DATE_OUVERTURE": "2026-07-09",
    "ACTION_LIEE_RISQUE": "NON",
    "ACTION_FREQUENCE_RISQUE": "NON_APPLICABLE",
    "ACTION_GRAVITE_RISQUE": "NON_APPLICABLE",
    "IS_DELETE": false,
    "created_at": "2026-07-09T12:00:00.000000Z",
    "updated_at": "2026-07-09T12:00:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 4) Mettre à jour une action de sécurité
- Méthode : `PUT`
- URL : `/v1/safety-actions/{id}`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
- tous les champs sont optionnels

Exemple :
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/safety-actions/1" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "DESCRIPTION": "Mise à jour de la description de l action."
  }'
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "INTITULE": "Vérification des extincteurs",
    "DESCRIPTION": "Mise à jour de la description de l action.",
    "DATE_OUVERTURE": "2026-07-09",
    "ACTION_LIEE_RISQUE": "NON",
    "ACTION_FREQUENCE_RISQUE": "NON_APPLICABLE",
    "ACTION_GRAVITE_RISQUE": "NON_APPLICABLE",
    "IS_DELETE": false,
    "created_at": "2026-07-09T12:00:00.000000Z",
    "updated_at": "2026-07-09T12:15:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 5) Supprimer une action de sécurité
- Méthode : `DELETE`
- URL : `/v1/safety-actions/{id}`
- Autorisation : `ADMIN` ou `POWER_USER`

Comportement :
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigné)

Exemple :
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/safety-actions/1" \
  -H "Authorization: Bearer <token_admin_ou_power_user>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "INTITULE": "Vérification des extincteurs",
    "DESCRIPTION": "Mise à jour de la description de l action.",
    "DATE_OUVERTURE": "2026-07-09",
    "ACTION_LIEE_RISQUE": "NON",
    "ACTION_FREQUENCE_RISQUE": "NON_APPLICABLE",
    "ACTION_GRAVITE_RISQUE": "NON_APPLICABLE",
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
    "The INTITULE field is required.",
    "The ACTION LIEE RISQUE field is required."
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
  "erreurs": "L'action de sécurité n'existe pas."
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
- Contrôleur : `app/Http/Controllers/Api/V1/SafetyActionsController.php`
- Modèle : `app/Models/SafetyAction.php`
- Stratégie d'autorisation (Policy) : `app/Policies/SafetyActionsPolicy.php`
