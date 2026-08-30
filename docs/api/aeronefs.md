# Documentation API - Aéronefs (Aeronefs)

## Aperçu

- Racine endpoints : `https://asteasy.deepinovia.com/api/api`
- Préfixe API : `/v1`
- Ressource : `/aeronefs`
- Middleware de groupe : `cors`, `multi_authentication`
- **Isolation Multi-Tenant** : Activée. La table `TB_AERONEFS` contient la colonne `ENTREPRISE_ID`. Les requêtes sont filtrées automatiquement par l'ID de l'entreprise de l'utilisateur authentifié.
- Policy :
  - Lecture (`index`, `show`) : tout utilisateur authentifié de l'entreprise.
  - Écriture (`store`, `update`, `destroy`) : utilisateur de type `ADMIN` ou `POWER_USER` de l'entreprise.

## Structure de la ressource Aéronef

```json
{
  "ID": 1,
  "MARQUE": "DJI",
  "TYPE_MODELE": "Matrice 300 RTK",
  "IMMATRICULATION": "F-D123",
  "SN": "SN987654321",
  "DATE_MISE_EN_SERVICE": "2026-08-14",
  "DOCUMENT_ID": null,
  "ENTREPRISE_ID": 1,
  "IS_DELETE": false,
  "created_at": "2026-08-14T11:00:00.000000Z",
  "updated_at": "2026-08-14T11:00:00.000000Z",
  "deleted_at": null
}
```

## Endpoints

### 1) Lister les aéronefs
- Méthode : `GET`
- URL : `/v1/aeronefs`
- Autorisation : utilisateur authentifié de la même entreprise

Paramètres query optionnels :
- `per_page` (int, défaut : `15`)
- `page` (int, défaut : `1`)
- `search` (string, filtre sur `MARQUE`, `TYPE_MODELE`, `IMMATRICULATION`, `SN`)

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/aeronefs?per_page=10&page=1&search=DJI" \
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
      "MARQUE": "DJI",
      "TYPE_MODELE": "Matrice 300 RTK",
      "IMMATRICULATION": "F-D123",
      "SN": "SN987654321",
      "DATE_MISE_EN_SERVICE": "2026-08-14",
      "DOCUMENT_ID": null,
      "ENTREPRISE_ID": 1,
      "IS_DELETE": false,
      "created_at": "2026-08-14T11:00:00.000000Z",
      "updated_at": "2026-08-14T11:00:00.000000Z",
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

### 2) Créer un aéronef
- Méthode : `POST`
- URL : `/v1/aeronefs`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
- `MARQUE` (requis, string, max 250)
- `TYPE_MODELE` (requis, string, max 500)
- `IMMATRICULATION` (requis, string, max 500)
- `SN` (optionnel, string, max 500)
- `DATE_MISE_EN_SERVICE` (optionnel, date au format `Y-m-d`)
- `DOCUMENT_ID` (optionnel, integer, doit exister dans `TB_DOCUMENTS`)
- `ENTREPRISE_ID` (optionnel, integer, doit exister dans `TB_ENTREPRISE`. Si omis, il est automatiquement assigné à l'entreprise de l'utilisateur).

Exemple :
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/aeronefs" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "MARQUE": "DJI",
    "TYPE_MODELE": "Mavic 3",
    "IMMATRICULATION": "F-M321",
    "SN": "SN11223344",
    "DATE_MISE_EN_SERVICE": "2026-08-14"
  }'
```

Réponse 201 :
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 2,
    "MARQUE": "DJI",
    "TYPE_MODELE": "Mavic 3",
    "IMMATRICULATION": "F-M321",
    "SN": "SN11223344",
    "DATE_MISE_EN_SERVICE": "2026-08-14",
    "DOCUMENT_ID": null,
    "ENTREPRISE_ID": 1,
    "IS_DELETE": false,
    "created_at": "2026-08-14T11:05:00.000000Z",
    "updated_at": "2026-08-14T11:05:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 3) Récupérer un aéronef
- Méthode : `GET`
- URL : `/v1/aeronefs/{id}`
- Autorisation : utilisateur authentifié de la même entreprise

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/aeronefs/2" \
  -H "Authorization: Bearer <token>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "MARQUE": "DJI",
    "TYPE_MODELE": "Mavic 3",
    "IMMATRICULATION": "F-M321",
    "SN": "SN11223344",
    "DATE_MISE_EN_SERVICE": "2026-08-14",
    "DOCUMENT_ID": null,
    "ENTREPRISE_ID": 1,
    "IS_DELETE": false,
    "created_at": "2026-08-14T11:05:00.000000Z",
    "updated_at": "2026-08-14T11:05:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 4) Mettre à jour un aéronef
- Méthode : `PUT`
- URL : `/v1/aeronefs/{id}`
- Autorisation : `ADMIN` ou `POWER_USER` de la même entreprise
- Content-Type : `application/json`

Body JSON :
- tous les champs sont optionnels
- `MARQUE` (optionnel, string, max 250)
- `TYPE_MODELE` (optionnel, string, max 500)
- `IMMATRICULATION` (optionnel, string, max 500)
- `SN` (optionnel, string, max 500)
- `DATE_MISE_EN_SERVICE` (optionnel, date au format `Y-m-d`)
- `DOCUMENT_ID` (optionnel, integer, doit exister dans `TB_DOCUMENTS`)

Exemple :
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/aeronefs/2" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "SN": "SN88888888"
  }'
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "MARQUE": "DJI",
    "TYPE_MODELE": "Mavic 3",
    "IMMATRICULATION": "F-M321",
    "SN": "SN88888888",
    "DATE_MISE_EN_SERVICE": "2026-08-14",
    "DOCUMENT_ID": null,
    "ENTREPRISE_ID": 1,
    "IS_DELETE": false,
    "created_at": "2026-08-14T11:05:00.000000Z",
    "updated_at": "2026-08-14T11:10:00.000000Z",
    "deleted_at": null
  }
}
```

---

### 5) Supprimer un aéronef
- Méthode : `DELETE`
- URL : `/v1/aeronefs/{id}`
- Autorisation : `ADMIN` ou `POWER_USER` de la même entreprise

Comportement :
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigné)

Exemple :
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/aeronefs/2" \
  -H "Authorization: Bearer <token_admin_ou_power_user>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "MARQUE": "DJI",
    "TYPE_MODELE": "Mavic 3",
    "IMMATRICULATION": "F-M321",
    "SN": "SN88888888",
    "DATE_MISE_EN_SERVICE": "2026-08-14",
    "DOCUMENT_ID": null,
    "ENTREPRISE_ID": 1,
    "IS_DELETE": true,
    "created_at": "2026-08-14T11:05:00.000000Z",
    "updated_at": "2026-08-14T11:10:00.000000Z",
    "deleted_at": "2026-08-14T11:15:00.000000Z"
  }
}
```

---

## Sources techniques
- Routes : `routes/api.php`
- Contrôleur : `app/Http/Controllers/Api/V1/AeronefsController.php`
- Modèle : `app/Models/Aeronef.php`
- Stratégie d'autorisation (Policy) : `app/Policies/AeronefsPolicy.php`
