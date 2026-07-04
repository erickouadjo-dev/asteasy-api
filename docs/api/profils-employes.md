# API Documentation - Profils Employes

## Apercu
Cette documentation couvre les endpoints CRUD de la ressource `profils-employes`.

- Racine endpoints: `https://asteasy.deepinovia.com/api/api`
- Prefix API: `/v1`
- Ressource: `/profils-employes`
- Middleware de groupe: `cors`, `multi_authentication`
- **Isolation Multi-Tenant** : Cloisonnement automatique par entreprise. Chaque entreprise ne voit et ne gère que les profils employés de sa propre structure (`ENTREPRISE_ID`). Les super-administrateurs système ont un accès global.
- Policy:
  - Lecture (`index`, `show`): utilisateur authentifie
  - Ecriture (`store`, `update`, `destroy`): utilisateur de type `ADMIN`

## Structure de la ressource ProfilEmploye

```json
{
  "ID": 1,
  "INTITULE": "Chef de projet",
  "DESCRIPTION": "Profil responsable de la coordination des taches",
  "MODIFICATION": 0,
  "created_at": "2026-05-12T09:00:00.000000Z",
  "updated_at": "2026-05-12T09:00:00.000000Z",
  "deleted_at": null
}
```

## Endpoints

### 1) Lister les profils employes
- Methode: `GET`
- URL: `/v1/profils-employes`
- Autorisation: utilisateur authentifie

Parametres query optionnels:
- `per_page` (int, defaut: `15`)
- `page` (int, defaut: `1`)
- `search` (string, filtre sur `INTITULE` et `DESCRIPTION`)

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/profils-employes?per_page=10&page=1&search=chef" \
  -H "Authorization: Bearer <token>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": [
    {
      "ID": 1,
      "INTITULE": "Chef de projet",
      "DESCRIPTION": "Profil responsable de la coordination des taches",
      "MODIFICATION": 0,
      "created_at": "2026-05-12T09:00:00.000000Z",
      "updated_at": "2026-05-12T09:00:00.000000Z",
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

### 2) Creer un profil employe
- Methode: `POST`
- URL: `/v1/profils-employes/{id}`
- Autorisation: `ADMIN`
- Content-Type: `application/json`

Body JSON:
- `INTITULE` (string, requis, max 200, unique)
- `DESCRIPTION` (string, optionnel, max 500)
- `MODIFICATION` (integer, optionnel)

Exemple:
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/profils-employes" \
  -H "Authorization: Bearer <token_admin>" \
  -H "Content-Type: application/json" \
  -d '{
    "INTITULE": "Responsable RH",
    "DESCRIPTION": "Profil en charge de la gestion RH",
    "MODIFICATION": 0
  }'
```

Reponse 201:
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 2,
    "INTITULE": "Responsable RH",
    "DESCRIPTION": "Profil en charge de la gestion RH",
    "MODIFICATION": 0,
    "created_at": "2026-05-12T09:15:00.000000Z",
    "updated_at": "2026-05-12T09:15:00.000000Z",
    "deleted_at": null
  }
}
```

### 3) Recuperer un profil employe
- Methode: `GET`
- URL: `/v1/profils-employes/{id}`
- Autorisation: utilisateur authentifie

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/profils-employes/2" \
  -H "Authorization: Bearer <token>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "INTITULE": "Responsable RH",
    "DESCRIPTION": "Profil en charge de la gestion RH",
    "MODIFICATION": 0,
    "created_at": "2026-05-12T09:15:00.000000Z",
    "updated_at": "2026-05-12T09:15:00.000000Z",
    "deleted_at": null
  }
}
```

### 4) Mettre a jour un profil employe
- Methode: `PUT`
- URL: `/v1/profils-employes/{id}`
- Autorisation: `ADMIN`
- Content-Type: `application/json`

Body JSON (tout optionnel):
- `INTITULE` (string, optionnel, max 200, unique)
- `DESCRIPTION` (string, optionnel, max 500)
- `MODIFICATION` (integer, optionnel)

Exemple:
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/profils-employes/2" \
  -H "Authorization: Bearer <token_admin>" \
  -H "Content-Type: application/json" \
  -d '{
    "DESCRIPTION": "Profil RH et administration du personnel",
    "MODIFICATION": 1
  }'
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "INTITULE": "Responsable RH",
    "DESCRIPTION": "Profil RH et administration du personnel",
    "MODIFICATION": 1,
    "created_at": "2026-05-12T09:15:00.000000Z",
    "updated_at": "2026-05-12T09:25:00.000000Z",
    "deleted_at": null
  }
}
```

### 5) Supprimer un profil employe
- Methode: `DELETE`
- URL: `/v1/profils-employes/{id}`
- Autorisation: `ADMIN`

Comportement:
- Effectue un soft delete (`deleted_at` renseigne)

Exemple:
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/profils-employes/2" \
  -H "Authorization: Bearer <token_admin>"
```

Reponse 204:
- Aucun contenu (No Content).

## Erreurs communes

### 400 - Validation
```json
{
  "code_http": 400,
  "code_message": "ERR_VALIDATION",
  "erreurs": [
    "The INTITULE field is required."
  ]
}
```

Ou si body invalide/vide:
```json
{
  "code_http": 400,
  "code_message": "ERR_VALIDATION",
  "erreurs": "Corps de la requete vide."
}
```

### 403 - Non autorise
```json
{
  "http_code": 403,
  "code": 403,
  "code_message": "Requete non autorisee."
}
```

### 404 - Non trouve
```json
{
  "code_http": 404,
  "code_message": "ERR_NOT_FOUND",
  "erreurs": "Le profil n'existe pas."
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
Cette doc est alignee avec l'implementation actuelle:
- Routes: `routes/api.php`
- Controller: `app/Http/Controllers/Api/V1/ProfilEmployesController.php`
- Model: `app/Models/ProfilEmploye.php`
- Policy: `app/Policies/ProfilEmployesPolicy.php`


