# API Documentation - Formations

## Apercu

- Racine endpoints: `https://asteasy.deepinovia.com/api`
- Prefix API: `/v1`
- Ressource: `/formations`
- Middleware de groupe: `cors`, `multi_authentication`
- **Isolation Multi-Tenant** : Cloisonnement automatique par entreprise. Chaque entreprise ne voit et ne gère que ses propres formations (`ENTREPRISE_ID`). Les super-administrateurs système ont un accès global.
- Policy:
  - Lecture (`index`, `show`): utilisateur authentifie
  - Ecriture (`store`, `update`, `destroy`): utilisateur de type `ADMIN` ou `POWER_USER`

## Structure de la ressource Formation

```json
{
  "ID": 1,
  "INTITULE": "Formation SST",
  "DESCRIPTION": "Sauveteur Secouriste du Travail",
  "VALIDITE_TYPE": "12M",
  "VALIDITE_DATE": "DATE_A_DATE",
  "VALIDITE_MODIFIABLE": "OUI",
  "FICHIERS_IMAGES": "[\"/uploads/formation_1.pdf\"]",
  "MODIFICATION": null,
  "IS_DELETE": false,
  "created_at": "2026-05-12T10:00:00.000000Z",
  "updated_at": "2026-05-12T10:00:00.000000Z",
  "deleted_at": null
}
```

Valeurs possibles:
- `VALIDITE_TYPE`: `6M`, `12M`, `24M`, `36M`
- `VALIDITE_DATE`: `DATE_A_DATE`, `FIN_DE_MOIS`
- `VALIDITE_MODIFIABLE`: `OUI`, `NON`

## Endpoints

### 1) Lister les formations
- Methode: `GET`
- URL: `/v1/formations`
- Autorisation: utilisateur authentifie

Parametres query optionnels:
- `per_page` (int, defaut: `15`)
- `page` (int, defaut: `1`)
- `search` (string, filtre sur `INTITULE`, `DESCRIPTION`)

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/formations?per_page=10&page=1&search=SST" \
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
      "INTITULE": "Formation SST",
      "DESCRIPTION": "Sauveteur Secouriste du Travail",
      "VALIDITE_TYPE": "12M",
      "VALIDITE_DATE": "DATE_A_DATE",
      "VALIDITE_MODIFIABLE": "OUI",
      "FICHIERS_IMAGES": "[\"/uploads/formation_1.pdf\"]",
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

### 2) Creer une formation
- Methode: `POST`
- URL: `/v1/formations`
- Autorisation: `ADMIN` ou `POWER_USER`
- Content-Type: `application/json`

Body JSON:
- `INTITULE` (required, string, max 200)
- `DESCRIPTION` (optionnel, string, max 500)
- `VALIDITE_TYPE` (optionnel, enum: `6M`, `12M`, `24M`, `36M`)
- `VALIDITE_DATE` (optionnel, enum: `DATE_A_DATE`, `FIN_DE_MOIS`)
- `VALIDITE_MODIFIABLE` (optionnel, enum: `OUI`, `NON`)
- `FICHIERS_IMAGES` (optionnel, string JSON)
- `MODIFICATION` (optionnel, string)

Exemple:
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/formations" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "INTITULE": "Formation SST",
    "DESCRIPTION": "Sauveteur Secouriste du Travail",
    "VALIDITE_TYPE": "12M",
    "VALIDITE_DATE": "DATE_A_DATE",
    "VALIDITE_MODIFIABLE": "OUI",
    "FICHIERS_IMAGES": "[\"/uploads/formation_1.pdf\"]"
  }'
```

Reponse 201:
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 1,
    "INTITULE": "Formation SST",
    "DESCRIPTION": "Sauveteur Secouriste du Travail",
    "VALIDITE_TYPE": "12M",
    "VALIDITE_DATE": "DATE_A_DATE",
    "VALIDITE_MODIFIABLE": "OUI",
    "FICHIERS_IMAGES": "[\"/uploads/formation_1.pdf\"]",
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
  "erreurs": ["The INTITULE field is required."]
}
```

### 3) Recuperer une formation
- Methode: `GET`
- URL: `/v1/formations/{id}`
- Autorisation: utilisateur authentifie

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/formations/1" \
  -H "Authorization: Bearer <token>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "INTITULE": "Formation SST",
    "DESCRIPTION": "Sauveteur Secouriste du Travail",
    "VALIDITE_TYPE": "12M",
    "VALIDITE_DATE": "DATE_A_DATE",
    "VALIDITE_MODIFIABLE": "OUI",
    "FICHIERS_IMAGES": "[\"/uploads/formation_1.pdf\"]",
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
  "erreurs": "La formation n'existe pas."
}
```

### 4) Modifier une formation
- Methode: `PUT`
- URL: `/v1/formations/{id}`
- Autorisation: `ADMIN` ou `POWER_USER`
- Content-Type: `application/json`

Body JSON (tous les champs sont optionnels):
- `INTITULE` (optionnel, string, max 200)
- `DESCRIPTION` (optionnel, string, max 500)
- `VALIDITE_TYPE` (optionnel, enum: `6M`, `12M`, `24M`, `36M`)
- `VALIDITE_DATE` (optionnel, enum: `DATE_A_DATE`, `FIN_DE_MOIS`)
- `VALIDITE_MODIFIABLE` (optionnel, enum: `OUI`, `NON`)
- `FICHIERS_IMAGES` (optionnel, string JSON)
- `MODIFICATION` (optionnel, string)

Exemple:
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/formations/1" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "VALIDITE_TYPE": "24M",
    "MODIFICATION": "Mise a jour duree validite"
  }'
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "INTITULE": "Formation SST",
    "DESCRIPTION": "Sauveteur Secouriste du Travail",
    "VALIDITE_TYPE": "24M",
    "VALIDITE_DATE": "DATE_A_DATE",
    "VALIDITE_MODIFIABLE": "OUI",
    "FICHIERS_IMAGES": "[\"/uploads/formation_1.pdf\"]",
    "MODIFICATION": "Mise a jour duree validite",
    "IS_DELETE": false,
    "created_at": "2026-05-12T10:00:00.000000Z",
    "updated_at": "2026-05-19T10:00:00.000000Z",
    "deleted_at": null
  }
}
```

### 5) Supprimer une formation
- Methode: `DELETE`
- URL: `/v1/formations/{id}`
- Autorisation: `ADMIN` ou `POWER_USER`

Exemple:
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/formations/1" \
  -H "Authorization: Bearer <token_admin_ou_power_user>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "INTITULE": "Formation SST",
    "IS_DELETE": true,
    "deleted_at": "2026-05-19T10:00:00.000000Z"
  }
}
```

## Codes d'erreur communs

| Code HTTP | code_message     | Description                                      |
|-----------|------------------|--------------------------------------------------|
| 400       | ERR_VALIDATION   | Donnees de la requete invalides                  |
| 403       | Requete non autorisee | Acces refuse (token manquant ou droits insuffisants) |
| 404       | ERR_NOT_FOUND    | Formation introuvable                            |
| 500       | ERR_SERVER       | Erreur interne du serveur                        |
