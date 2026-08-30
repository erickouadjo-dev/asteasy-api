# Documentation API - Véhicules (Vehicules)

## Aperçu

- Racine endpoints : `https://asteasy.deepinovia.com/api/api`
- Préfixe API : `/v1`
- Ressource : `/vehicules`
- Middleware de groupe : `cors`, `multi_authentication`
- **Isolation Multi-Tenant** : Activée. La table `TB_VEHICULES` contient la colonne `ENTREPRISE_ID`. Les requêtes sont filtrées automatiquement par l'ID de l'entreprise de l'utilisateur authentifié.
- Policy :
  - Lecture (`index`, `show`) : tout utilisateur authentifié de l'entreprise.
  - Écriture (`store`, `update`, `destroy`) : utilisateur de type `ADMIN` ou `POWER_USER` de l'entreprise.

## Structure de la ressource Véhicule

```json
{
  "ID": 1,
  "MARQUE": "Renault",
  "TYPE_MODELE": "Kangoo",
  "IMMATRICULATION": "XX-123-XX",
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

### 1) Lister les véhicules
- Méthode : `GET`
- URL : `/v1/vehicules`
- Autorisation : utilisateur authentifié de la même entreprise

Paramètres query optionnels :
- `per_page` (int, défaut : `15`)
- `page` (int, défaut : `1`)
- `search` (string, filtre sur `MARQUE`, `TYPE_MODELE`, `IMMATRICULATION`)

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/vehicules?per_page=10&page=1&search=Renault" \
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
      "MARQUE": "Renault",
      "TYPE_MODELE": "Kangoo",
      "IMMATRICULATION": "XX-123-XX",
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

### 2) Créer un véhicule
- Méthode : `POST`
- URL : `/v1/vehicules`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
- `MARQUE` (requis, string, max 250)
- `TYPE_MODELE` (requis, string, max 500)
- `IMMATRICULATION` (optionnel, string, max 255)
- `DATE_MISE_EN_SERVICE` (optionnel, date au format `Y-m-d`)
- `DOCUMENT_ID` (optionnel, integer, doit exister dans `TB_DOCUMENTS`)
- `ENTREPRISE_ID` (optionnel, integer, doit exister dans `TB_ENTREPRISE`. Si omis, il est automatiquement assigné à l'entreprise de l'utilisateur).

Exemple :
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/vehicules" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "MARQUE": "Citroën",
    "TYPE_MODELE": "Berlingo",
    "IMMATRICULATION": "YY-456-YY",
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
    "MARQUE": "Citroën",
    "TYPE_MODELE": "Berlingo",
    "IMMATRICULATION": "YY-456-YY",
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

### 3) Récupérer un véhicule
- Méthode : `GET`
- URL : `/v1/vehicules/{id}`
- Autorisation : utilisateur authentifié de la même entreprise

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/vehicules/2" \
  -H "Authorization: Bearer <token>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "MARQUE": "Citroën",
    "TYPE_MODELE": "Berlingo",
    "IMMATRICULATION": "YY-456-YY",
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

### 4) Mettre à jour un véhicule
- Méthode : `PUT`
- URL : `/v1/vehicules/{id}`
- Autorisation : `ADMIN` ou `POWER_USER` de la même entreprise
- Content-Type : `application/json`

Body JSON :
- tous les champs sont optionnels
- `MARQUE` (optionnel, string, max 250)
- `TYPE_MODELE` (optionnel, string, max 500)
- `IMMATRICULATION` (optionnel, string, max 255)
- `DATE_MISE_EN_SERVICE` (optionnel, date au format `Y-m-d`)
- `DOCUMENT_ID` (optionnel, integer, doit exister dans `TB_DOCUMENTS`)

Exemple :
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/vehicules/2" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "IMMATRICULATION": "ZZ-789-ZZ"
  }'
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "MARQUE": "Citroën",
    "TYPE_MODELE": "Berlingo",
    "IMMATRICULATION": "ZZ-789-ZZ",
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

### 5) Supprimer un véhicule
- Méthode : `DELETE`
- URL : `/v1/vehicules/{id}`
- Autorisation : `ADMIN` ou `POWER_USER` de la même entreprise

Comportement :
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigné)

Exemple :
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/vehicules/2" \
  -H "Authorization: Bearer <token_admin_ou_power_user>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "MARQUE": "Citroën",
    "TYPE_MODELE": "Berlingo",
    "IMMATRICULATION": "ZZ-789-ZZ",
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
- Contrôleur : `app/Http/Controllers/Api/V1/VehiculesController.php`
- Modèle : `app/Models/Vehicule.php`
- Stratégie d'autorisation (Policy) : `app/Policies/VehiculesPolicy.php`
