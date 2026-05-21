# API Documentation - Employes

## Apercu

- Racine endpoints: `https://asteasy.deepinovia.com/api/api`
- Prefix API: `/v1`
- Ressource: `/employes`
- Middleware de groupe: `cors`, `multi_authentication`
- Policy:
  - Lecture (`index`, `show`): utilisateur authentifie
  - Ecriture (`store`, `update`, `destroy`): utilisateur de type `ADMIN` ou `POWER_USER`

## Structure de la ressource Employe

```json
{
  "ID": 1,
  "IS_DELETE": false,
  "USER_ID": 10,
  "PHONE_WHATSAPP": "+22990000000",
  "PHONE2": "+22991111111",
  "WHATSAPP": "+22990000000",
  "E-MAIL": "employe@example.com",
  "ADRESSE1": "Rue 1",
  "ADRESSE2": null,
  "ADRESSE3": null,
  "CODE POSTAL": "00000",
  "VILLE": "Cotonou",
  "PAYS": "Benin",
  "DATE_EMBAUCHE": "2026-01-01T00:00:00.000000Z",
  "DATE_FIN_CONTRAT": null,
  "STATUT": "ACTIF",
  "NATIONALITE1": "Beninoise",
  "FICHIER_PHOTO_PASSEPORT1": null,
  "NATIONALITE2": "Beninoise",
  "FICHIER_PHOTO_NATIONALITE2": null,
  "GROUPE_SANGUIN": "O+",
  "PROFIL_EMPLOYE_ID": 1,
  "URG_NOM": "Doe",
  "URG_PRENOM": "Jane",
  "URG_LIEN_PARENTE": "SOEUR",
  "URG_TEL1": "+22992222222",
  "URG_TEL2": null,
  "URG_EMAIL": "urgence@example.com",
  "MODIFICATION": null,
  "ENTREPRISE_ID": 1,
  "created_at": "2026-05-08T10:00:00.000000Z",
  "updated_at": "2026-05-08T10:00:00.000000Z",
  "deleted_at": null
}
```

## Endpoints

### 1) Lister les employes
- Methode: `GET`
- URL: `/v1/employes`
- Autorisation: utilisateur authentifie

Parametres query optionnels:
- `per_page` (int, defaut: `15`)
- `page` (int, defaut: `1`)
- `search` (string, filtre sur `PHONE_WHATSAPP`, `VILLE`, `PAYS`, `NATIONALITE1`, `NATIONALITE2`)

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/employes?per_page=10&page=1&search=benin" \
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
      "STATUT": "ACTIF",
      "NATIONALITE1": "Beninoise",
      "VILLE": "Cotonou",
      "PAYS": "Benin",
      "IS_DELETE": false,
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

### 2) Creer un employe
- Methode: `POST`
- URL: `/v1/employes`
- Autorisation: `ADMIN` ou `POWER_USER`
- Content-Type: `application/json`

Body JSON:
- `STATUT` (required, `ACTIF` ou `NON_ACTIF`)
- `NATIONALITE1` (required, string)
- `NATIONALITE2` (required, string)
- `USER_ID` (optionnel, integer, exists `utilisateurs.id`)
- `PROFIL_EMPLOYE_ID` (optionnel, integer, exists `TB_PROFIL_EMPLOYE.ID`)
- `ENTREPRISE_ID` (optionnel, integer, exists `TB_ENTREPRISE.ID`)
- autres champs optionnels: `PHONE_WHATSAPP`, `PHONE2`, `WHATSAPP`, `E-MAIL`, `ADRESSE1`, `ADRESSE2`, `ADRESSE3`, `CODE POSTAL`, `VILLE`, `PAYS`, `DATE_EMBAUCHE`, `DATE_FIN_CONTRAT`, `FICHIER_PHOTO_PASSEPORT1`, `FICHIER_PHOTO_NATIONALITE2`, `GROUPE_SANGUIN`, `URG_NOM`, `URG_PRENOM`, `URG_LIEN_PARENTE`, `URG_TEL1`, `URG_TEL2`, `URG_EMAIL`, `MODIFICATION`, `IS_DELETE`

Exemple:
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/employes" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "STATUT": "ACTIF",
    "NATIONALITE1": "Beninoise",
    "NATIONALITE2": "Beninoise",
    "ENTREPRISE_ID": 1
  }'
```

Reponse 201:
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 2,
    "STATUT": "ACTIF",
    "NATIONALITE1": "Beninoise",
    "NATIONALITE2": "Beninoise",
    "ENTREPRISE_ID": 1,
    "IS_DELETE": false,
    "created_at": "2026-05-08T11:00:00.000000Z",
    "updated_at": "2026-05-08T11:00:00.000000Z",
    "deleted_at": null
  }
}
```

### 3) Recuperer un employe
- Methode: `GET`
- URL: `/v1/employes/{id}`
- Autorisation: utilisateur authentifie

Exemple:
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/employes/2" \
  -H "Authorization: Bearer <token>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "STATUT": "ACTIF",
    "NATIONALITE1": "Beninoise",
    "NATIONALITE2": "Beninoise",
    "ENTREPRISE_ID": 1,
    "IS_DELETE": false,
    "deleted_at": null
  }
}
```

### 4) Mettre a jour un employe
- Methode: `PUT`
- URL: `/v1/employes/{id}`
- Autorisation: `ADMIN` ou `POWER_USER`
- Content-Type: `application/json`

Body JSON:
- tous les champs sont optionnels
- `STATUT` doit etre `ACTIF` ou `NON_ACTIF` si fourni

Exemple:
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/employes/2" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "STATUT": "NON_ACTIF",
    "VILLE": "Porto-Novo"
  }'
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "STATUT": "NON_ACTIF",
    "VILLE": "Porto-Novo",
    "updated_at": "2026-05-08T11:10:00.000000Z"
  }
}
```

### 5) Supprimer un employe
- Methode: `DELETE`
- URL: `/v1/employes/{id}`
- Autorisation: `ADMIN` ou `POWER_USER`

Comportement:
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigne)

Exemple:
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/employes/2" \
  -H "Authorization: Bearer <token_admin_ou_power_user>"
```

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "IS_DELETE": true,
    "deleted_at": "2026-05-08T11:15:00.000000Z"
  }
}
```

## Erreurs communes

### 400 - Validation
```json
{
  "code_http": 400,
  "code_message": "ERR_VALIDATION",
  "erreurs": [
    "The STATUT field is required."
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
  "erreurs": "L'employe n'existe pas."
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
- Routes: `routes/api.php`
- Controller: `app/Http/Controllers/Api/V1/EmployesController.php`
- Model: `app/Models/Employe.php`
- Policy: `app/Policies/EmployesPolicy.php`

