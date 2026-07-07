# API Documentation - Utilisateurs Roles

## Apercu
Cette documentation couvre les endpoints CRUD de la ressource `utilisateurs-roles` permettant d'associer et de dissocier des rôles aux utilisateurs du système.

- Racine endpoints: `https://asteasy.deepinovia.com/api/api`
- Prefix API: `/v1`
- Ressource: `/utilisateurs-roles`
- Middleware de groupe: `cors`, `multi_authentication`
- Policy:
  - Lecture (`index`, `show`): tout utilisateur authentifie
  - Ecriture (`store`, `update`, `destroy`): utilisateur de type `ADMIN`

## Structure de la ressource UtilisateurRole

```json
{
  "ID": 1,
  "UTILISATEUR_ID": 15,
  "ROLE_ID": 2,
  "IS_DELETE": false,
  "created_at": "2026-07-07T02:30:00.000000Z",
  "updated_at": "2026-07-07T02:30:00.000000Z",
  "deleted_at": null
}
```

## Endpoints

### 1) Lister les liaisons utilisateurs-roles
- Methode: `GET`
- URL: `/v1/utilisateurs-roles`
- Autorisation: utilisateur authentifie

Parametres query optionnels:
- `per_page` (int, defaut: `15`)
- `page` (int, defaut: `1`)
- `search` (string, filtre sur `UTILISATEUR_ID` ou `ROLE_ID`)

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/utilisateurs-roles?per_page=10&page=1" \
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
      "UTILISATEUR_ID": 15,
      "ROLE_ID": 2,
      "IS_DELETE": false,
      "created_at": "2026-07-07T02:30:00.000000Z",
      "updated_at": "2026-07-07T02:30:00.000000Z",
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

### 2) Associer un role a un utilisateur
- Methode: `POST`
- URL: `/v1/utilisateurs-roles`
- Autorisation: `ADMIN`
- Content-Type: `application/json`

Body JSON:
- `UTILISATEUR_ID` (integer, requis, doit exister dans `utilisateurs.id`)
- `ROLE_ID` (integer, requis, doit exister dans `TB_ROLE.ID`)

Exemple:
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/utilisateurs-roles" \
  -H "Authorization: Bearer <token_admin>" \
  -H "Content-Type: application/json" \
  -d '{
    "UTILISATEUR_ID": 15,
    "ROLE_ID": 2
  }'
```

Reponse 201:
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 2,
    "UTILISATEUR_ID": 15,
    "ROLE_ID": 2,
    "IS_DELETE": false,
    "created_at": "2026-07-07T02:32:00.000000Z",
    "updated_at": "2026-07-07T02:32:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 3) Recuperer une liaison utilisateur-role
- Methode: `GET`
- URL: `/v1/utilisateurs-roles/{id}`
- Autorisation: utilisateur authentifie

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/utilisateurs-roles/2" \
  -H "Authorization: Bearer <token>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "UTILISATEUR_ID": 15,
    "ROLE_ID": 2,
    "IS_DELETE": false,
    "created_at": "2026-07-07T02:32:00.000000Z",
    "updated_at": "2026-07-07T02:32:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 4) Mettre a jour une liaison utilisateur-role
- Methode: `PUT`
- URL: `/v1/utilisateurs-roles/{id}`
- Autorisation: `ADMIN`
- Content-Type: `application/json`

Body JSON (optionnel):
- `UTILISATEUR_ID` (integer, optionnel, doit exister dans `utilisateurs.id`)
- `ROLE_ID` (integer, optionnel, doit exister dans `TB_ROLE.ID`)

Exemple:
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/utilisateurs-roles/2" \
  -H "Authorization: Bearer <token_admin>" \
  -H "Content-Type: application/json" \
  -d '{
    "ROLE_ID": 3
  }'
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "UTILISATEUR_ID": 15,
    "ROLE_ID": 3,
    "IS_DELETE": false,
    "created_at": "2026-07-07T02:32:00.000000Z",
    "updated_at": "2026-07-07T02:34:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 5) Dissocier un role d'un utilisateur (Supprimer)
- Methode: `DELETE`
- URL: `/v1/utilisateurs-roles/{id}`
- Autorisation: `ADMIN`

Comportement:
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigne)

Exemple:
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/utilisateurs-roles/2" \
  -H "Authorization: Bearer <token_admin>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "UTILISATEUR_ID": 15,
    "ROLE_ID": 3,
    "IS_DELETE": true,
    "created_at": "2026-07-07T02:32:00.000000Z",
    "updated_at": "2026-07-07T02:34:00.000000Z",
    "deleted_at": "2026-07-07T02:36:00.000000Z"
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
    "The UTILISATEUR ID field is required.",
    "The selected ROLE ID is invalid."
  ]
}
```

Ou si body invalide/vide:
```json
{
  "code_http": 400,
  "code_message": "ERR_VALIDATION",
  "erreurs": "Corps de la requete vide."
```

Ou si la liaison existe déjà :
```json
{
  "code_http": 400,
  "code_message": "ERR_VALIDATION",
  "erreurs": [
    "Cette association utilisateur-rôle existe déjà."
  ]
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
  "erreurs": "La liaison utilisateur-rôle n'existe pas."
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
Cette doc est alignee avec l'implementation actuelle:
- Routes: `routes/api.php`
- Controller: `app/Http/Controllers/Api/V1/UtilisateursRolesController.php`
- Model: `app/Models/UtilisateurRole.php`
- Policy: `app/Policies/UtilisateursRolesPolicy.php`
