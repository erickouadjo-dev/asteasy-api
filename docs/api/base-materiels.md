# Documentation API - Matériel de Base (Base Materiel)

## Aperçu

- Racine endpoints : `https://asteasy.deepinovia.com/api/api`
- Préfixe API : `/v1`
- Ressource : `/base-materiels`
- Middleware de groupe : `cors`, `multi_authentication`
- **Isolation Multi-Tenant** : Activée. La table `TB_BASE_MATERIEL` contient la colonne `ENTREPRISE_ID`. Les requêtes sont filtrées automatiquement par l'ID de l'entreprise de l'utilisateur authentifié.
- Policy :
  - Lecture (`index`, `show`) : tout utilisateur authentifié de l'entreprise.
  - Écriture (`store`, `update`, `destroy`) : utilisateur de type `ADMIN` ou `POWER_USER` de l'entreprise.

## Structure de la ressource Matériel de Base

```json
{
  "ID": 1,
  "BASE_ID": 1,
  "AERONEF_ID": 2,
  "VEHICULE_ID": null,
  "EQUIPEMENT_ID": 5,
  "ENTREPRISE_ID": 1,
  "IS_DELETE": false,
  "created_at": "2026-08-14T10:00:00.000000Z",
  "updated_at": "2026-08-14T10:00:00.000000Z",
  "deleted_at": null,
  "base": {
    "ID": 1,
    "INTITULE": "Base Paris Nord",
    "TYPE_BASE": "PRINCIPALE"
  },
  "aeronef": {
    "ID": 2,
    "MARQUE": "DJI",
    "TYPE_MODELE": "Matrice 300 RTK",
    "IMMATRICULATION": "F-D123"
  },
  "vehicule": null,
  "equipement": {
    "ID": 5,
    "MARQUE": "Hilti",
    "TYPE_MODELE": "Télémètre laser"
  }
}
```

## Endpoints

### 1) Lister le matériel de base
- Méthode : `GET`
- URL : `/v1/base-materiels`
- Autorisation : utilisateur authentifié de la même entreprise

Paramètres query optionnels :
- `per_page` (int, défaut : `15`)
- `page` (int, défaut : `1`)
- `search` (string, filtre sur les intitulés des bases et marques/modèles des aéronefs/véhicules/équipements)

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/base-materiels?per_page=10&page=1&search=Paris" \
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
      "BASE_ID": 1,
      "AERONEF_ID": 2,
      "VEHICULE_ID": null,
      "EQUIPEMENT_ID": 5,
      "ENTREPRISE_ID": 1,
      "IS_DELETE": false,
      "created_at": "2026-08-14T10:00:00.000000Z",
      "updated_at": "2026-08-14T10:00:00.000000Z",
      "deleted_at": null,
      "base": {
        "ID": 1,
        "INTITULE": "Base Paris Nord"
      },
      "aeronef": {
        "ID": 2,
        "MARQUE": "DJI",
        "TYPE_MODELE": "Matrice 300"
      },
      "vehicule": null,
      "equipement": {
        "ID": 5,
        "MARQUE": "Hilti"
      }
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

### 2) Créer une liaison de matériel de base
- Méthode : `POST`
- URL : `/v1/base-materiels`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
- `BASE_ID` (requis, integer, doit exister dans `TB_BASE`)
- `AERONEF_ID` (optionnel, integer, doit exister dans `TB_AERONEFS`)
- `VEHICULE_ID` (optionnel, integer, doit exister dans `TB_VEHICULES`)
- `EQUIPEMENT_ID` (optionnel, integer, doit exister dans `TB_EQUIPEMENTS`)
- `ENTREPRISE_ID` (optionnel, integer, doit exister dans `TB_ENTREPRISE`. Si omis, il est automatiquement assigné à l'entreprise de l'utilisateur).

Exemple :
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/base-materiels" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "BASE_ID": 1,
    "AERONEF_ID": 2,
    "EQUIPEMENT_ID": 5
  }'
```

Réponse 201 :
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 1,
    "BASE_ID": 1,
    "AERONEF_ID": 2,
    "VEHICULE_ID": null,
    "EQUIPEMENT_ID": 5,
    "ENTREPRISE_ID": 1,
    "IS_DELETE": false,
    "created_at": "2026-08-14T10:05:00.000000Z",
    "updated_at": "2026-08-14T10:05:00.000000Z",
    "deleted_at": null,
    "base": {
      "ID": 1,
      "INTITULE": "Base Paris Nord"
    },
    "aeronef": {
      "ID": 2,
      "MARQUE": "DJI"
    },
    "vehicule": null,
    "equipement": {
      "ID": 5,
      "MARQUE": "Hilti"
    }
  }
}
```

---

### 3) Récupérer une liaison de matériel de base
- Méthode : `GET`
- URL : `/v1/base-materiels/{id}`
- Autorisation : utilisateur authentifié de la même entreprise

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/base-materiels/1" \
  -H "Authorization: Bearer <token>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "BASE_ID": 1,
    "AERONEF_ID": 2,
    "VEHICULE_ID": null,
    "EQUIPEMENT_ID": 5,
    "ENTREPRISE_ID": 1,
    "IS_DELETE": false,
    "created_at": "2026-08-14T10:05:00.000000Z",
    "updated_at": "2026-08-14T10:05:00.000000Z",
    "deleted_at": null,
    "base": {
      "ID": 1,
      "INTITULE": "Base Paris Nord"
    },
    "aeronef": {
      "ID": 2,
      "MARQUE": "DJI"
    },
    "vehicule": null,
    "equipement": {
      "ID": 5,
      "MARQUE": "Hilti"
    }
  }
}
```

---

### 4) Mettre à jour une liaison de matériel de base
- Méthode : `PUT`
- URL : `/v1/base-materiels/{id}`
- Autorisation : `ADMIN` ou `POWER_USER` de la même entreprise
- Content-Type : `application/json`

Body JSON :
- tous les champs sont optionnels
- `BASE_ID` (optionnel, integer, doit exister dans `TB_BASE`)
- `AERONEF_ID` (optionnel, integer, doit exister dans `TB_AERONEFS`)
- `VEHICULE_ID` (optionnel, integer, doit exister dans `TB_VEHICULES`)
- `EQUIPEMENT_ID` (optionnel, integer, doit exister dans `TB_EQUIPEMENTS`)

Exemple :
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/base-materiels/1" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "VEHICULE_ID": 3
  }'
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "BASE_ID": 1,
    "AERONEF_ID": 2,
    "VEHICULE_ID": 3,
    "EQUIPEMENT_ID": 5,
    "ENTREPRISE_ID": 1,
    "IS_DELETE": false,
    "created_at": "2026-08-14T10:05:00.000000Z",
    "updated_at": "2026-08-14T10:10:00.000000Z",
    "deleted_at": null,
    "base": {
      "ID": 1,
      "INTITULE": "Base Paris Nord"
    },
    "aeronef": {
      "ID": 2,
      "MARQUE": "DJI"
    },
    "vehicule": {
      "ID": 3,
      "MARQUE": "Renault"
    },
    "equipement": {
      "ID": 5,
      "MARQUE": "Hilti"
    }
  }
}
```

---

### 5) Supprimer une liaison de matériel de base
- Méthode : `DELETE`
- URL : `/v1/base-materiels/{id}`
- Autorisation : `ADMIN` ou `POWER_USER` de la même entreprise

Comportement :
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigné)

Exemple :
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/base-materiels/1" \
  -H "Authorization: Bearer <token_admin_ou_power_user>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 1,
    "BASE_ID": 1,
    "AERONEF_ID": 2,
    "VEHICULE_ID": 3,
    "EQUIPEMENT_ID": 5,
    "ENTREPRISE_ID": 1,
    "IS_DELETE": true,
    "created_at": "2026-08-14T10:05:00.000000Z",
    "updated_at": "2026-08-14T10:10:00.000000Z",
    "deleted_at": "2026-08-14T10:15:00.000000Z"
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
    "The BASE_ID field is required."
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
  "erreurs": "Le matériel de base n'existe pas."
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
- Routes : `routes/api.php`
- Contrôleur : `app/Http/Controllers/Api/V1/BaseMaterielsController.php`
- Modèle : `app/Models/BaseMateriel.php`
- Stratégie d'autorisation (Policy) : `app/Policies/BaseMaterielsPolicy.php`
