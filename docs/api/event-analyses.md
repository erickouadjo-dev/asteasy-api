# Documentation API - Analyses d'Événements (Event Analyses)

## Aperçu

Cette documentation décrit les endpoints CRUD de la ressource `event-analyses` associée à la table `TB_EVENT_ANALYSE`.

- Racine endpoints : `https://asteasy.deepinovia.com/api/api`
- Préfixe API : `/v1`
- Ressource : `/event-analyses`
- Middleware de groupe : `cors`, `multi_authentication`
- **Isolation Multi-Tenant** : Non applicable.
- Policy :
  - Lecture (`index`, `show`) : tout utilisateur authentifié.
  - Écriture (`store`, `update`, `destroy`) : utilisateur de type `ADMIN` ou `POWER_USER`.

---

## Structure de la ressource EventAnalyse

```json
{
  "ID": 1,
  "ID_EVENT_DECLARATION": 12,
  "ID_STATUT_EVENEMENT": 2,
  "DATE_ANALYSE": "2026-07-09",
  "TITRE_EVENT": "Fuite de liquide hydraulique",
  "EVENT_DESCRIPTION_ANALYSE": "Fuite importante constatée au niveau de la ligne d'assemblage B.",
  "EVENT_LOCATION_ANALYSE": 3,
  "EVENT_TYPE": "INCIDENT MINEUR",
  "ROOTCAUSE": "Joint défectueux dû à l'usure.",
  "FACTEURS_CONTRIBUTIFS": "Absence de maintenance préventive planifiée le mois dernier.",
  "ID_RISQUE": 4,
  "ID_MATRICE_RISQUE": 1,
  "ID_SAFETY_ACTION": 2,
  "RISKLEVEL_FINAL_ACCEPTANCE": "Acceptable sous réserve de vérification.",
  "ANALYSE_PAR": 15,
  "INFO_AUTORITE": "NON",
  "DATE_INFO_AUTORITE": null,
  "INFO_CLIENT": "NON",
  "DATE_INFO_CLIENT": null,
  "COMMENTAIRE": "Aucune victime ni dommage corporel.",
  "RISQUE_SUBSIDIAIRE": "NON",
  "ID_STATUT": 1,
  "PUBLIE": "NON",
  "DATE_PUBLIE": null,
  "ID_TAG_ETIQUETTE": null,
  "DATE_CLOTURE": null,
  "IS_DELETE": false,
  "created_at": "2026-07-09T12:00:00.000000Z",
  "updated_at": "2026-07-09T12:00:00.000000Z",
  "deleted_at": null
}
```

---

## Endpoints

### 1) Lister les analyses d'événements
- Méthode : `GET`
- URL : `/v1/event-analyses`
- Autorisation : utilisateur authentifié

Paramètres query optionnels :
- `per_page` (int, défaut : `15`)
- `page` (int, défaut : `1`)
- `search` (string, filtre sur `TITRE_EVENT` et `EVENT_DESCRIPTION_ANALYSE`)

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/event-analyses?per_page=10&page=1&search=Fuite" \
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
      "ID_EVENT_DECLARATION": 12,
      "ID_STATUT_EVENEMENT": 2,
      "DATE_ANALYSE": "2026-07-09",
      "TITRE_EVENT": "Fuite de liquide hydraulique",
      "EVENT_DESCRIPTION_ANALYSE": "Fuite importante constatée au niveau de la ligne d'assemblage B.",
      "EVENT_LOCATION_ANALYSE": 3,
      "EVENT_TYPE": "INCIDENT MINEUR",
      "ROOTCAUSE": "Joint défectueux dû à l'usure.",
      "FACTEURS_CONTRIBUTIFS": "Absence de maintenance préventive planifiée le mois dernier.",
      "ID_RISQUE": 4,
      "ID_MATRICE_RISQUE": 1,
      "ID_SAFETY_ACTION": 2,
      "RISKLEVEL_FINAL_ACCEPTANCE": "Acceptable sous réserve de vérification.",
      "ANALYSE_PAR": 15,
      "INFO_AUTORITE": "NON",
      "DATE_INFO_AUTORITE": null,
      "INFO_CLIENT": "NON",
      "DATE_INFO_CLIENT": null,
      "COMMENTAIRE": "Aucune victime ni dommage corporel.",
      "RISQUE_SUBSIDIAIRE": "NON",
      "ID_STATUT": 1,
      "PUBLIE": "NON",
      "DATE_PUBLIE": null,
      "ID_TAG_ETIQUETTE": null,
      "DATE_CLOTURE": null,
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

### 2) Créer une analyse d'événement
- Méthode : `POST`
- URL : `/v1/event-analyses`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
* `DATE_ANALYSE` (requis, date au format YYYY-MM-DD)
* `TITRE_EVENT` (requis, string)
* `EVENT_DESCRIPTION_ANALYSE` (requis, string)
* `EVENT_LOCATION_ANALYSE` (requis, integer)
* `EVENT_TYPE` (requis, string, doit être un parmi : `ACCIDENT`, `INCIDENT MAJEUR`, `INCIDENT MINEUR`, `DANGER`)
* `ID_EVENT_DECLARATION` (optionnel, integer)
* `ID_STATUT_EVENEMENT` (optionnel, integer)
* `ROOTCAUSE` (optionnel, string)
* `FACTEURS_CONTRIBUTIFS` (optionnel, string)
* `ID_RISQUE` (optionnel, integer)
* `ID_MATRICE_RISQUE` (optionnel, integer)
* `ID_SAFETY_ACTION` (optionnel, integer, doit exister dans `TB_SAFETY_ACTION.ID`)
* `RISKLEVEL_FINAL_ACCEPTANCE` (optionnel, string)
* `ANALYSE_PAR` (optionnel, integer, doit exister dans `utilisateurs.id`)
* `INFO_AUTORITE` (optionnel, string, `OUI` ou `NON`, défaut : `NON`)
* `DATE_INFO_AUTORITE` (optionnel, date au format YYYY-MM-DD)
* `INFO_CLIENT` (optionnel, string, `OUI` ou `NON`, défaut : `NON`)
* `DATE_INFO_CLIENT` (optionnel, date au format YYYY-MM-DD)
* `COMMENTAIRE` (optionnel, string)
* `RISQUE_SUBSIDIAIRE` (optionnel, string, `OUI` ou `NON`, défaut : `NON`)
* `ID_STATUT` (optionnel, integer, doit exister dans `TB_STATUT.ID`)
* `PUBLIE` (optionnel, string, `OUI` ou `NON`, défaut : `NON`)
* `DATE_PUBLIE` (optionnel, date au format YYYY-MM-DD)
* `ID_TAG_ETIQUETTE` (optionnel, integer)
* `DATE_CLOTURE` (optionnel, date au format YYYY-MM-DD)

Exemple :
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/event-analyses" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "DATE_ANALYSE": "2026-07-09",
    "TITRE_EVENT": "Fuite de liquide hydraulique",
    "EVENT_DESCRIPTION_ANALYSE": "Fuite importante constatée au niveau de la ligne d assemblage B.",
    "EVENT_LOCATION_ANALYSE": 3,
    "EVENT_TYPE": "INCIDENT MINEUR"
  }'
```

Réponse 201 :
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 1,
    "DATE_ANALYSE": "2026-07-09",
    "TITRE_EVENT": "Fuite de liquide hydraulique",
    "EVENT_DESCRIPTION_ANALYSE": "Fuite importante constatée au niveau de la ligne d assemblage B.",
    "EVENT_LOCATION_ANALYSE": 3,
    "EVENT_TYPE": "INCIDENT MINEUR",
    "INFO_AUTORITE": "NON",
    "INFO_CLIENT": "NON",
    "RISQUE_SUBSIDIAIRE": "NON",
    "PUBLIE": "NON",
    "IS_DELETE": false,
    "created_at": "2026-07-09T12:00:00.000000Z",
    "updated_at": "2026-07-09T12:00:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 3) Récupérer une analyse d'événement
- Méthode : `GET`
- URL : `/v1/event-analyses/{id}`
- Autorisation : utilisateur authentifié

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/event-analyses/1" \
  -H "Authorization: Bearer <token>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "DATE_ANALYSE": "2026-07-09",
    "TITRE_EVENT": "Fuite de liquide hydraulique",
    "EVENT_DESCRIPTION_ANALYSE": "Fuite importante constatée au niveau de la ligne d assemblage B.",
    "EVENT_LOCATION_ANALYSE": 3,
    "EVENT_TYPE": "INCIDENT MINEUR",
    "INFO_AUTORITE": "NON",
    "INFO_CLIENT": "NON",
    "RISQUE_SUBSIDIAIRE": "NON",
    "PUBLIE": "NON",
    "IS_DELETE": false,
    "created_at": "2026-07-09T12:00:00.000000Z",
    "updated_at": "2026-07-09T12:00:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 4) Mettre à jour une analyse d'événement
- Méthode : `PUT`
- URL : `/v1/event-analyses/{id}`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
- tous les champs sont optionnels

Exemple :
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/event-analyses/1" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "ROOTCAUSE": "Joint d étanchéité usé prématurément."
  }'
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "DATE_ANALYSE": "2026-07-09",
    "TITRE_EVENT": "Fuite de liquide hydraulique",
    "EVENT_DESCRIPTION_ANALYSE": "Fuite importante constatée au niveau de la ligne d assemblage B.",
    "EVENT_LOCATION_ANALYSE": 3,
    "EVENT_TYPE": "INCIDENT MINEUR",
    "ROOTCAUSE": "Joint d étanchéité usé prématurément.",
    "INFO_AUTORITE": "NON",
    "INFO_CLIENT": "NON",
    "RISQUE_SUBSIDIAIRE": "NON",
    "PUBLIE": "NON",
    "IS_DELETE": false,
    "created_at": "2026-07-09T12:00:00.000000Z",
    "updated_at": "2026-07-09T12:15:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 5) Supprimer une analyse d'événement
- Méthode : `DELETE`
- URL : `/v1/event-analyses/{id}`
- Autorisation : `ADMIN` ou `POWER_USER`

Comportement :
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigné)

Exemple :
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/event-analyses/1" \
  -H "Authorization: Bearer <token_admin_ou_power_user>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "DATE_ANALYSE": "2026-07-09",
    "TITRE_EVENT": "Fuite de liquide hydraulique",
    "EVENT_DESCRIPTION_ANALYSE": "Fuite importante constatée au niveau de la ligne d assemblage B.",
    "EVENT_LOCATION_ANALYSE": 3,
    "EVENT_TYPE": "INCIDENT MINEUR",
    "INFO_AUTORITE": "NON",
    "INFO_CLIENT": "NON",
    "RISQUE_SUBSIDIAIRE": "NON",
    "PUBLIE": "NON",
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
    "The DATE ANALYSE field is required.",
    "The TITRE EVENT field is required."
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
  "erreurs": "L'analyse d'événement n'existe pas."
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
- Contrôleur : `app/Http/Controllers/Api/V1/EventAnalysesController.php`
- Modèle : `app/Models/EventAnalyse.php`
- Stratégie d'autorisation (Policy) : `app/Policies/EventAnalysesPolicy.php`
