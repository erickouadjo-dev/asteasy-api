# API Documentation - Employe Formations

## Apercu

- Racine endpoints: `https://asteasy.deepinovia.com/api`
- Prefix API: `/v1`
- Ressource: `/employe-formations`
- Middleware de groupe: `cors`, `multi_authentication`
- Policy:
  - Lecture (`index`, `show`): utilisateur authentifie
  - Ecriture (`store`, `update`, `destroy`): utilisateur de type `ADMIN` ou `POWER_USER`

## Structure de la ressource EmployeFormation

```json
{
  "ID": 1,
  "EMPLOYE_ID": 5,
  "FORMATION_ID": 2,
  "DATE_REALISATION": "2026-03-10T08:00:00.000000Z",
  "DATE_VALIDITE": "2027-03-10T08:00:00.000000Z",
  "FICHIERS_IMAGES": "[\"/uploads/attestation_1.pdf\"]",
  "STATUT": "ACTIF",
  "MODIFICATION": null,
  "IS_DELETE": false,
  "created_at": "2026-05-12T10:00:00.000000Z",
  "updated_at": "2026-05-12T10:00:00.000000Z",
  "deleted_at": null
}
```

Valeurs possibles:
- `STATUT`: `ACTIF`, `EXPIRE`

## Endpoints

### 1) Lister les formations des employes
- Methode: `GET`
- URL: `/v1/employe-formations`
- Autorisation: utilisateur authentifie

Parametres query optionnels:
- `per_page` (int, defaut: `15`)
- `page` (int, defaut: `1`)
- `employe_id` (int, filtre par employe)
- `formation_id` (int, filtre par formation)
- `statut` (string, filtre par statut: `ACTIF` ou `EXPIRE`)
- `search` (string, filtre sur `EMPLOYE_ID`, `FORMATION_ID`)

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/employe-formations?per_page=10&page=1&employe_id=5&statut=ACTIF" \
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
      "EMPLOYE_ID": 5,
      "FORMATION_ID": 2,
      "DATE_REALISATION": "2026-03-10T08:00:00.000000Z",
      "DATE_VALIDITE": "2027-03-10T08:00:00.000000Z",
      "FICHIERS_IMAGES": "[\"/uploads/attestation_1.pdf\"]",
      "STATUT": "ACTIF",
      "MODIFICATION": null,
      "IS_DELETE": false,
      "created_at": "2026-05-12T10:00:00.000000Z",
      "updated_at": "2026-05-12T10:00:00.000000Z",
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

### 2) Creer une formation employe
- Methode: `POST`
- URL: `/v1/employe-formations`
- Autorisation: `ADMIN` ou `POWER_USER`
- Content-Type: `application/json`

Body JSON:
- `EMPLOYE_ID` (optionnel, integer, exists `TB_EMPLOYE.ID`)
- `FORMATION_ID` (optionnel, integer, exists `TB_FORMATION.ID`)
- `DATE_REALISATION` (optionnel, date)
- `DATE_VALIDITE` (optionnel, date, modifiable)
- `FICHIERS_IMAGES` (optionnel, string JSON)
- `STATUT` (optionnel, enum: `ACTIF`, `EXPIRE`)
- `MODIFICATION` (optionnel, integer)

Exemple:
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/employe-formations" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "EMPLOYE_ID": 5,
    "FORMATION_ID": 2,
    "DATE_REALISATION": "2026-03-10",
    "DATE_VALIDITE": "2027-03-10",
    "FICHIERS_IMAGES": "[\"/uploads/attestation_1.pdf\"]",
    "STATUT": "ACTIF"
  }'
```

Reponse 201:
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 1,
    "EMPLOYE_ID": 5,
    "FORMATION_ID": 2,
    "DATE_REALISATION": "2026-03-10T00:00:00.000000Z",
    "DATE_VALIDITE": "2027-03-10T00:00:00.000000Z",
    "FICHIERS_IMAGES": "[\"/uploads/attestation_1.pdf\"]",
    "STATUT": "ACTIF",
    "MODIFICATION": null,
    "IS_DELETE": false,
    "created_at": "2026-05-19T10:00:00.000000Z",
    "updated_at": "2026-05-19T10:00:00.000000Z",
    "deleted_at": null
  }
}
```

Reponse 400 (validation):
```json
{
  "code_http": 400,
  "code_message": "ERR_VALIDATION",
  "erreurs": ["The selected EMPLOYE_ID is invalid."]
}
```

### 3) Recuperer une formation employe
- Methode: `GET`
- URL: `/v1/employe-formations/{id}`
- Autorisation: utilisateur authentifie

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/employe-formations/1" \
  -H "Authorization: Bearer <token>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "EMPLOYE_ID": 5,
    "FORMATION_ID": 2,
    "DATE_REALISATION": "2026-03-10T08:00:00.000000Z",
    "DATE_VALIDITE": "2027-03-10T08:00:00.000000Z",
    "FICHIERS_IMAGES": "[\"/uploads/attestation_1.pdf\"]",
    "STATUT": "ACTIF",
    "MODIFICATION": null,
    "IS_DELETE": false,
    "created_at": "2026-05-12T10:00:00.000000Z",
    "updated_at": "2026-05-12T10:00:00.000000Z",
    "deleted_at": null
  }
}
```

Reponse 404:
```json
{
  "code_http": 404,
  "code_message": "ERR_NOT_FOUND",
  "erreurs": "La formation de l'employe n'existe pas."
}
```

### 4) Modifier une formation employe
- Methode: `PUT`
- URL: `/v1/employe-formations/{id}`
- Autorisation: `ADMIN` ou `POWER_USER`
- Content-Type: `application/json`

Body JSON (tous les champs sont optionnels):
- `EMPLOYE_ID` (optionnel, integer, exists `TB_EMPLOYE.ID`)
- `FORMATION_ID` (optionnel, integer, exists `TB_FORMATION.ID`)
- `DATE_REALISATION` (optionnel, date)
- `DATE_VALIDITE` (optionnel, date, modifiable)
- `FICHIERS_IMAGES` (optionnel, string JSON)
- `STATUT` (optionnel, enum: `ACTIF`, `EXPIRE`)
- `MODIFICATION` (optionnel, integer)

Exemple:
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/employe-formations/1" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "DATE_VALIDITE": "2028-03-10",
    "STATUT": "ACTIF",
    "MODIFICATION": 3
  }'
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "EMPLOYE_ID": 5,
    "FORMATION_ID": 2,
    "DATE_REALISATION": "2026-03-10T08:00:00.000000Z",
    "DATE_VALIDITE": "2028-03-10T00:00:00.000000Z",
    "FICHIERS_IMAGES": "[\"/uploads/attestation_1.pdf\"]",
    "STATUT": "ACTIF",
    "MODIFICATION": 3,
    "IS_DELETE": false,
    "created_at": "2026-05-12T10:00:00.000000Z",
    "updated_at": "2026-05-19T10:00:00.000000Z",
    "deleted_at": null
  }
}
```

### 5) Supprimer une formation employe
- Methode: `DELETE`
- URL: `/v1/employe-formations/{id}`
- Autorisation: `ADMIN` ou `POWER_USER`

Exemple:
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/employe-formations/1" \
  -H "Authorization: Bearer <token_admin_ou_power_user>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "IS_DELETE": true,
    "deleted_at": "2026-05-19T10:00:00.000000Z"
  }
}
```

## Codes d'erreur communs

| Code HTTP | code_message          | Description                                           |
|-----------|-----------------------|-------------------------------------------------------|
| 400       | ERR_VALIDATION        | Donnees de la requete invalides                       |
| 403       | Requete non autorisee | Acces refuse (token manquant ou droits insuffisants)  |
| 404       | ERR_NOT_FOUND         | Enregistrement introuvable                            |
| 500       | ERR_SERVER            | Erreur interne du serveur                             |
