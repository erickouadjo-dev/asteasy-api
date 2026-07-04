# API Documentation - Nominations Employes

## Apercu

- Racine endpoints: `https://asteasy.deepinovia.com/api/api`
- Prefix API: `/v1`
- Ressource: `/nominations-employes`
- Middleware de groupe: `cors`, `multi_authentication`
- **Isolation Multi-Tenant** : Cloisonnement automatique par entreprise. Chaque entreprise ne voit et ne gère que les nominations de sa propre structure (`ENTREPRISE_ID`). Les super-administrateurs système ont un accès global.
- Policy:
  - Lecture (`index`, `show`): utilisateur authentifie
  - Ecriture (`store`, `update`, `destroy`): utilisateur de type `ADMIN` ou `POWER_USER`

## Structure de la ressource NominationEmploye

```json
{
  "ID": 1,
  "EMPLOYE_ID": 5,
  "INTITULE_POSTE": "Chef d'equipe",
  "DESCRIPTION_POSTE": "Responsable de la coordination de l'equipe terrain",
  "AGREMENT_CONCERNE": "Agrement ANTT",
  "DATE_ACCEPTATION": "2026-03-10T08:00:00.000000Z",
  "FICHIERS": "[\"/uploads/nomination_1.pdf\"]",
  "DATE_PRISE_DE_FONCTION": "2026-04-01T08:00:00.000000Z",
  "DATE_FIN": "2027-04-01T08:00:00.000000Z",
  "IS_DELETE": false,
  "created_at": "2026-05-12T10:00:00.000000Z",
  "updated_at": "2026-05-12T10:00:00.000000Z",
  "deleted_at": null
}
```

## Endpoints

### 1) Lister les nominations employes
- Methode: `GET`
- URL: `/v1/nominations-employes`
- Autorisation: utilisateur authentifie

Parametres query optionnels:
- `per_page` (int, defaut: `15`)
- `page` (int, defaut: `1`)
- `employe_id` (int, filtre par employe)
- `search` (string, filtre sur `INTITULE_POSTE`, `DESCRIPTION_POSTE`, `AGREMENT_CONCERNE`, `EMPLOYE_ID`)

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/nominations-employes?per_page=10&page=1&employe_id=5&search=Chef" \
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
      "INTITULE_POSTE": "Chef d'equipe",
      "DESCRIPTION_POSTE": "Responsable de la coordination de l'equipe terrain",
      "AGREMENT_CONCERNE": "Agrement ANTT",
      "DATE_ACCEPTATION": "2026-03-10T08:00:00.000000Z",
      "FICHIERS": "[\"/uploads/nomination_1.pdf\"]",
      "DATE_PRISE_DE_FONCTION": "2026-04-01T08:00:00.000000Z",
      "DATE_FIN": "2027-04-01T08:00:00.000000Z",
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

### 2) Creer une nomination employe
- Methode: `POST`
- URL: `/v1/nominations-employes`
- Autorisation: `ADMIN` ou `POWER_USER`
- Content-Type: `application/json`

Body JSON:
- `EMPLOYE_ID` (optionnel, integer, exists `TB_EMPLOYE.ID`)
- `INTITULE_POSTE` (optionnel, string, max 255)
- `DESCRIPTION_POSTE` (optionnel, string, max 500)
- `AGREMENT_CONCERNE` (optionnel, string, max 255)
- `DATE_ACCEPTATION` (optionnel, date)
- `FICHIERS` (optionnel, string JSON)
- `DATE_PRISE_DE_FONCTION` (optionnel, date)
- `DATE_FIN` (optionnel, date)

Exemple:
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/nominations-employes" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "EMPLOYE_ID": 5,
    "INTITULE_POSTE": "Chef d'equipe",
    "DESCRIPTION_POSTE": "Responsable de la coordination de l'equipe terrain",
    "AGREMENT_CONCERNE": "Agrement ANTT",
    "DATE_ACCEPTATION": "2026-03-10",
    "FICHIERS": "[\"/uploads/nomination_1.pdf\"]",
    "DATE_PRISE_DE_FONCTION": "2026-04-01",
    "DATE_FIN": "2027-04-01"
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
    "INTITULE_POSTE": "Chef d'equipe",
    "DESCRIPTION_POSTE": "Responsable de la coordination de l'equipe terrain",
    "AGREMENT_CONCERNE": "Agrement ANTT",
    "DATE_ACCEPTATION": "2026-03-10T00:00:00.000000Z",
    "FICHIERS": "[\"/uploads/nomination_1.pdf\"]",
    "DATE_PRISE_DE_FONCTION": "2026-04-01T00:00:00.000000Z",
    "DATE_FIN": "2027-04-01T00:00:00.000000Z",
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

### 3) Recuperer une nomination employe
- Methode: `GET`
- URL: `/v1/nominations-employes/{id}`
- Autorisation: utilisateur authentifie

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/nominations-employes/1" \
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
    "INTITULE_POSTE": "Chef d'equipe",
    "DESCRIPTION_POSTE": "Responsable de la coordination de l'equipe terrain",
    "AGREMENT_CONCERNE": "Agrement ANTT",
    "DATE_ACCEPTATION": "2026-03-10T08:00:00.000000Z",
    "FICHIERS": "[\"/uploads/nomination_1.pdf\"]",
    "DATE_PRISE_DE_FONCTION": "2026-04-01T08:00:00.000000Z",
    "DATE_FIN": "2027-04-01T08:00:00.000000Z",
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
  "erreurs": "La nomination employe n'existe pas."
}
```

### 4) Modifier une nomination employe
- Methode: `PUT`
- URL: `/v1/nominations-employes/{id}`
- Autorisation: `ADMIN` ou `POWER_USER`
- Content-Type: `application/json`

Body JSON (tous les champs sont optionnels):
- `EMPLOYE_ID` (optionnel, integer, exists `TB_EMPLOYE.ID`)
- `INTITULE_POSTE` (optionnel, string, max 255)
- `DESCRIPTION_POSTE` (optionnel, string, max 500)
- `AGREMENT_CONCERNE` (optionnel, string, max 255)
- `DATE_ACCEPTATION` (optionnel, date)
- `FICHIERS` (optionnel, string JSON)
- `DATE_PRISE_DE_FONCTION` (optionnel, date)
- `DATE_FIN` (optionnel, date)

Exemple:
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/nominations-employes/1" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "INTITULE_POSTE": "Superviseur terrain",
    "DATE_FIN": "2028-04-01"
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
    "INTITULE_POSTE": "Superviseur terrain",
    "DESCRIPTION_POSTE": "Responsable de la coordination de l'equipe terrain",
    "AGREMENT_CONCERNE": "Agrement ANTT",
    "DATE_ACCEPTATION": "2026-03-10T08:00:00.000000Z",
    "FICHIERS": "[\"/uploads/nomination_1.pdf\"]",
    "DATE_PRISE_DE_FONCTION": "2026-04-01T08:00:00.000000Z",
    "DATE_FIN": "2028-04-01T00:00:00.000000Z",
    "IS_DELETE": false,
    "created_at": "2026-05-12T10:00:00.000000Z",
    "updated_at": "2026-05-19T10:00:00.000000Z",
    "deleted_at": null
  }
}
```

### 5) Supprimer une nomination employe
- Methode: `DELETE`
- URL: `/v1/nominations-employes/{id}`
- Autorisation: `ADMIN` ou `POWER_USER`

Exemple:
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/nominations-employes/1" \
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
