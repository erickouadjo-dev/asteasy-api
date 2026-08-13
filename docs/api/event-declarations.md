# Documentation API - Déclarations d'Événements (Event Declarations)

## Aperçu

Cette documentation décrit les endpoints CRUD de la ressource `event-declarations` associée à la table `TB_EVENT_DECLARATION`.

- Racine endpoints : `https://asteasy.deepinovia.com/api/api`
- Préfixe API : `/v1`
- Ressource : `/event-declarations`
- Middleware de groupe : `cors`, `multi_authentication`
- **Isolation Multi-Tenant** : Non applicable.
- Policy :
  - Lecture (`index`, `show`) : tout utilisateur authentifié.
  - Création (`store`) : tout utilisateur authentifié.
  - Modification (`update`) : utilisateur `ADMIN`, `POWER_USER` ou le `RAPORTEUR` d'origine.
  - Suppression (`destroy`) : utilisateur `ADMIN` ou `POWER_USER`.

---

## Structure de la ressource EventDeclaration

```json
{
  "ID": 1,
  "REF_EVENT": "SOR-2026-004",
  "TYPE_EVENT": "EVENT",
  "CONFIDENTIEL": "NON",
  "RAPORTEUR": 15,
  "BASE_OPERATEUR": "Base Orly",
  "DATE_EVENT": "2026-07-09",
  "HEURE_EVENT": "12:00:00",
  "CLIENT_MISSION": "Air France - Vol AF123",
  "ID_BASE_MATERIEL": 3,
  "EVENT_LOCALISATION": "Piste 2",
  "GPS_POSITION": "48.7262° N, 2.3652° E",
  "EVENT_DESCRIPTION": "Collision mineure d'un oiseau avec le pare-brise lors de la phase d'approche.",
  "FICHIERS_IMAGES": "http://example.com/images/birdstrike.jpg",
  "IS_DELETE": false,
  "created_at": "2026-07-09T12:00:00.000000Z",
  "updated_at": "2026-07-09T12:00:00.000000Z",
  "deleted_at": null
}
```

---

## Endpoints

### 1) Lister les déclarations d'événements
- Méthode : `GET`
- URL : `/v1/event-declarations`
- Autorisation : utilisateur authentifié

Paramètres query optionnels :
- `per_page` (int, défaut : `15`)
- `page` (int, défaut : `1`)
- `search` (string, filtre sur `REF_EVENT` et `EVENT_DESCRIPTION`)

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/event-declarations?per_page=10&page=1&search=SOR-2026" \
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
      "REF_EVENT": "SOR-2026-004",
      "TYPE_EVENT": "EVENT",
      "CONFIDENTIEL": "NON",
      "RAPORTEUR": 15,
      "BASE_OPERATEUR": "Base Orly",
      "DATE_EVENT": "2026-07-09",
      "HEURE_EVENT": "12:00:00",
      "CLIENT_MISSION": "Air France - Vol AF123",
      "ID_BASE_MATERIEL": 3,
      "EVENT_LOCALISATION": "Piste 2",
      "GPS_POSITION": "48.7262° N, 2.3652° E",
      "EVENT_DESCRIPTION": "Collision mineure d'un oiseau avec le pare-brise lors de la phase d'approche.",
      "FICHIERS_IMAGES": "http://example.com/images/birdstrike.jpg",
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

### 2) Créer une déclaration d'événement
- Méthode : `POST`
- URL : `/v1/event-declarations`
- Autorisation : tout utilisateur authentifié
- Content-Type : `application/json`

Body JSON :
* `REF_EVENT` (requis, string, max 255)
* `TYPE_EVENT` (requis, string, max 255)
* `CONFIDENTIEL` (requis, string, `OUI` ou `NON`)
* `DATE_EVENT` (requis, date au format YYYY-MM-DD)
* `HEURE_EVENT` (requis, heure au format HH:MM:SS)
* `EVENT_LOCALISATION` (requis, string, max 255)
* `EVENT_DESCRIPTION` (requis, string)
* `RAPORTEUR` (optionnel, integer, doit exister dans `utilisateurs.id`)
* `BASE_OPERATEUR` (optionnel, string, max 255)
* `CLIENT_MISSION` (optionnel, string, max 255)
* `ID_BASE_MATERIEL` (optionnel, integer)
* `GPS_POSITION` (optionnel, string, max 255)
* `FICHIERS_IMAGES` (optionnel, string)

Exemple :
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/event-declarations" \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "REF_EVENT": "SOR-2026-004",
    "TYPE_EVENT": "EVENT",
    "CONFIDENTIEL": "NON",
    "DATE_EVENT": "2026-07-09",
    "HEURE_EVENT": "12:00:00",
    "EVENT_LOCALISATION": "Piste 2",
    "EVENT_DESCRIPTION": "Collision mineure d un oiseau avec le pare-brise lors de la phase d approche."
  }'
```

Réponse 201 :
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 1,
    "REF_EVENT": "SOR-2026-004",
    "TYPE_EVENT": "EVENT",
    "CONFIDENTIEL": "NON",
    "DATE_EVENT": "2026-07-09",
    "HEURE_EVENT": "12:00:00",
    "EVENT_LOCALISATION": "Piste 2",
    "EVENT_DESCRIPTION": "Collision mineure d un oiseau avec le pare-brise lors de la phase d approche.",
    "IS_DELETE": false,
    "created_at": "2026-07-09T12:00:00.000000Z",
    "updated_at": "2026-07-09T12:00:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 3) Récupérer une déclaration d'événement
- Méthode : `GET`
- URL : `/v1/event-declarations/{id}`
- Autorisation : utilisateur authentifié

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/event-declarations/1" \
  -H "Authorization: Bearer <token>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "REF_EVENT": "SOR-2026-004",
    "TYPE_EVENT": "EVENT",
    "CONFIDENTIEL": "NON",
    "DATE_EVENT": "2026-07-09",
    "HEURE_EVENT": "12:00:00",
    "EVENT_LOCALISATION": "Piste 2",
    "EVENT_DESCRIPTION": "Collision mineure d un oiseau avec le pare-brise lors de la phase d approche.",
    "IS_DELETE": false,
    "created_at": "2026-07-09T12:00:00.000000Z",
    "updated_at": "2026-07-09T12:00:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 4) Mettre à jour une déclaration d'événement
- Méthode : `PUT`
- URL : `/v1/event-declarations/{id}`
- Autorisation : `ADMIN`, `POWER_USER` ou le `RAPORTEUR` d'origine
- Content-Type : `application/json`

Body JSON :
- tous les champs sont optionnels

Exemple :
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/event-declarations/1" \
  -H "Authorization: Bearer <token_du_rapporteur>" \
  -H "Content-Type: application/json" \
  -d '{
    "EVENT_DESCRIPTION": "Mise à jour de la description par le rapporteur."
  }'
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "REF_EVENT": "SOR-2026-004",
    "TYPE_EVENT": "EVENT",
    "CONFIDENTIEL": "NON",
    "DATE_EVENT": "2026-07-09",
    "HEURE_EVENT": "12:00:00",
    "EVENT_LOCALISATION": "Piste 2",
    "EVENT_DESCRIPTION": "Mise à jour de la description par le rapporteur.",
    "IS_DELETE": false,
    "created_at": "2026-07-09T12:00:00.000000Z",
    "updated_at": "2026-07-09T12:15:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 5) Supprimer une déclaration d'événement
- Méthode : `DELETE`
- URL : `/v1/event-declarations/{id}`
- Autorisation : `ADMIN` ou `POWER_USER`

Comportement :
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigné)

Exemple :
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/event-declarations/1" \
  -H "Authorization: Bearer <token_admin_ou_power_user>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "REF_EVENT": "SOR-2026-004",
    "TYPE_EVENT": "EVENT",
    "CONFIDENTIEL": "NON",
    "DATE_EVENT": "2026-07-09",
    "HEURE_EVENT": "12:00:00",
    "EVENT_LOCALISATION": "Piste 2",
    "EVENT_DESCRIPTION": "Mise à jour de la description par le rapporteur.",
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
    "The REF EVENT field is required.",
    "The EVENT DESCRIPTION field is required."
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
  "erreurs": "La déclaration d'événement n'existe pas."
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
- Contrôleur : `app/Http/Controllers/Api/V1/EventDeclarationsController.php`
- Modèle : `app/Models/EventDeclaration.php`
- Stratégie d'autorisation (Policy) : `app/Policies/EventDeclarationsPolicy.php`
