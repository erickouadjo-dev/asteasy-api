# Documentation API - Mesures Additionnelles (Additional Measures)

## Aperçu

- Racine endpoints : `https://asteasy.deepinovia.com/api/api`
- Préfixe API : `/v1`
- Ressource : `/mesures-additionnelles`
- Middleware de groupe : `cors`, `multi_authentication`
- **Isolation Multi-Tenant** : Actif (la table `TB_MESURES_ADDITIONNELLES` contient la colonne `ENTREPRISE_ID` et est filtrée par locataire via le trait `BelongsToTenant`).
- Policy :
  - Lecture (`index`, `show`) : tout utilisateur authentifié.
  - Écriture (`store`, `update`, `destroy`) : utilisateur de type `ADMIN` ou `POWER_USER`.

## Structure de la ressource Mesure Additionnelle

```json
{
  "ID": 1,
  "INTITULE": "Installation d'un filet de protection",
  "DESCRIPTION": "Installer des filets antichute en dessous de la zone d'échafaudage.",
  "FREQUENCE": "Une fois",
  "GRAVITE": "Moyenne",
  "COMMENTAIRES": "À réaliser avant le début des travaux de gros oeuvre.",
  "ID_TAG_ETIQUETTE": 1,
  "DEPARTEMENT_RESPONSABLE": 3,
  "ENTREPRISE_ID": 1,
  "IS_DELETE": false,
  "created_at": "2026-08-13T13:00:00.000000Z",
  "updated_at": "2026-08-13T13:00:00.000000Z",
  "deleted_at": null,
  "tag": {
    "ID": 1,
    "LIBELLE": "Sécurité respiratoire",
    "created_at": "2026-08-13T13:00:00.000000Z",
    "updated_at": "2026-08-13T13:00:00.000000Z"
  },
  "responsable": {
    "id": 3,
    "nom": "DUPONT",
    "prenom": "Jean",
    "email": "jean.dupont@example.com"
  }
}
```

## Endpoints

### 1) Lister les mesures additionnelles
- Méthode : `GET`
- URL : `/v1/mesures-additionnelles`
- Autorisation : utilisateur authentifié

Paramètres query optionnels :
- `per_page` (int, défaut : `15`)
- `page` (int, défaut : `1`)
- `search` (string, filtre sur `INTITULE`, `DESCRIPTION`, `FREQUENCE`, `GRAVITE` et `COMMENTAIRES`)

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/mesures-additionnelles?per_page=10&page=1&search=filet" \
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
      "INTITULE": "Installation d'un filet de protection",
      "DESCRIPTION": "Installer des filets antichute en dessous de la zone d'échafaudage.",
      "FREQUENCE": "Une fois",
      "GRAVITE": "Moyenne",
      "COMMENTAIRES": "À réaliser avant le début des travaux de gros oeuvre.",
      "ID_TAG_ETIQUETTE": 1,
      "DEPARTEMENT_RESPONSABLE": 3,
      "ENTREPRISE_ID": 1,
      "IS_DELETE": false,
      "created_at": "2026-08-13T13:00:00.000000Z",
      "updated_at": "2026-08-13T13:00:00.000000Z",
      "deleted_at": null,
      "tag": {
        "ID": 1,
        "LIBELLE": "Sécurité respiratoire"
      },
      "responsable": {
        "id": 3,
        "nom": "DUPONT",
        "prenom": "Jean",
        "email": "jean.dupont@example.com"
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

### 2) Créer une mesure additionnelle
- Méthode : `POST`
- URL : `/v1/mesures-additionnelles`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
- `INTITULE` (requis, string, max 255, unique dans `TB_MESURES_ADDITIONNELLES`)
- `DESCRIPTION` (requis, string)
- `FREQUENCE` (requis, string, max 255)
- `GRAVITE` (requis, string, max 255)
- `COMMENTAIRES` (requis, string)
- `ID_TAG_ETIQUETTE` (optionnel, integer, doit exister dans `TB_TARG_ETIQUETTE`)
- `DEPARTEMENT_RESPONSABLE` (optionnel, integer, doit exister dans la table `utilisateurs` sous `id`)
- `ENTREPRISE_ID` (optionnel, integer, doit exister dans `TB_ENTREPRISE`)

Exemple :
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/mesures-additionnelles" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "INTITULE": "Achat de talkie-walkies",
    "DESCRIPTION": "Fournir des talkie-walkies pour la coordination en sous-sol.",
    "FREQUENCE": "Une fois",
    "GRAVITE": "Faible",
    "COMMENTAIRES": "À distribuer aux superviseurs.",
    "ID_TAG_ETIQUETTE": 1,
    "DEPARTEMENT_RESPONSABLE": 3
  }'
```

Réponse 201 :
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 2,
    "INTITULE": "Achat de talkie-walkies",
    "DESCRIPTION": "Fournir des talkie-walkies pour la coordination en sous-sol.",
    "FREQUENCE": "Une fois",
    "GRAVITE": "Faible",
    "COMMENTAIRES": "À distribuer aux superviseurs.",
    "ID_TAG_ETIQUETTE": 1,
    "DEPARTEMENT_RESPONSABLE": 3,
    "ENTREPRISE_ID": 1,
    "IS_DELETE": false,
    "created_at": "2026-08-13T13:05:00.000000Z",
    "updated_at": "2026-08-13T13:05:00.000000Z",
    "deleted_at": null,
    "tag": {
      "ID": 1,
      "LIBELLE": "Sécurité respiratoire"
    },
    "responsable": {
      "id": 3,
      "nom": "DUPONT",
      "prenom": "Jean",
      "email": "jean.dupont@example.com"
    }
  }
}
```

---

### 3) Récupérer une mesure additionnelle
- Méthode : `GET`
- URL : `/v1/mesures-additionnelles/{id}`
- Autorisation : utilisateur authentifié

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/mesures-additionnelles/2" \
  -H "Authorization: Bearer <token>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "INTITULE": "Achat de talkie-walkies",
    "DESCRIPTION": "Fournir des talkie-walkies pour la coordination en sous-sol.",
    "FREQUENCE": "Une fois",
    "GRAVITE": "Faible",
    "COMMENTAIRES": "À distribuer aux superviseurs.",
    "ID_TAG_ETIQUETTE": 1,
    "DEPARTEMENT_RESPONSABLE": 3,
    "ENTREPRISE_ID": 1,
    "IS_DELETE": false,
    "created_at": "2026-08-13T13:05:00.000000Z",
    "updated_at": "2026-08-13T13:05:00.000000Z",
    "deleted_at": null,
    "tag": {
      "ID": 1,
      "LIBELLE": "Sécurité respiratoire"
    },
    "responsable": {
      "id": 3,
      "nom": "DUPONT",
      "prenom": "Jean",
      "email": "jean.dupont@example.com"
    }
  }
}
```

---

### 4) Mettre à jour une mesure additionnelle
- Méthode : `PUT`
- URL : `/v1/mesures-additionnelles/{id}`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
- tous les champs sont optionnels
- `INTITULE` (optionnel, string, max 255, unique sauf pour cet ID)
- `DESCRIPTION` (optionnel, string)
- `FREQUENCE` (optionnel, string, max 255)
- `GRAVITE` (optionnel, string, max 255)
- `COMMENTAIRES` (optionnel, string)
- `ID_TAG_ETIQUETTE` (optionnel, integer, doit exister dans `TB_TARG_ETIQUETTE`)
- `DEPARTEMENT_RESPONSABLE` (optionnel, integer, doit exister dans la table `utilisateurs` sous `id`)
- `ENTREPRISE_ID` (optionnel, integer, doit exister dans `TB_ENTREPRISE`)

Exemple :
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/mesures-additionnelles/2" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "GRAVITE": "Moyenne"
  }'
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "INTITULE": "Achat de talkie-walkies",
    "DESCRIPTION": "Fournir des talkie-walkies pour la coordination en sous-sol.",
    "FREQUENCE": "Une fois",
    "GRAVITE": "Moyenne",
    "COMMENTAIRES": "À distribuer aux superviseurs.",
    "ID_TAG_ETIQUETTE": 1,
    "DEPARTEMENT_RESPONSABLE": 3,
    "ENTREPRISE_ID": 1,
    "IS_DELETE": false,
    "created_at": "2026-08-13T13:05:00.000000Z",
    "updated_at": "2026-08-13T13:10:00.000000Z",
    "deleted_at": null,
    "tag": {
      "ID": 1,
      "LIBELLE": "Sécurité respiratoire"
    },
    "responsable": {
      "id": 3,
      "nom": "DUPONT",
      "prenom": "Jean",
      "email": "jean.dupont@example.com"
    }
  }
}
```

---

### 5) Supprimer une mesure additionnelle
- Méthode : `DELETE`
- URL : `/v1/mesures-additionnelles/{id}`
- Autorisation : `ADMIN` ou `POWER_USER`

Comportement :
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigné)

Exemple :
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/mesures-additionnelles/2" \
  -H "Authorization: Bearer <token_admin_ou_power_user>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "INTITULE": "Achat de talkie-walkies",
    "DESCRIPTION": "Fournir des talkie-walkies pour la coordination en sous-sol.",
    "FREQUENCE": "Une fois",
    "GRAVITE": "Moyenne",
    "COMMENTAIRES": "À distribuer aux superviseurs.",
    "ID_TAG_ETIQUETTE": 1,
    "DEPARTEMENT_RESPONSABLE": 3,
    "ENTREPRISE_ID": 1,
    "IS_DELETE": true,
    "created_at": "2026-08-13T13:05:00.000000Z",
    "updated_at": "2026-08-13T13:10:00.000000Z",
    "deleted_at": "2026-08-13T13:15:00.000000Z"
  }
}
```

---

## Sources techniques
- Routes : `routes/api.php`
- Contrôleur : `app/Http/Controllers/Api/V1/MesuresAdditionnellesController.php`
- Modèle : `app/Models/MesureAdditionnelle.php`
- Stratégie d'autorisation (Policy) : `app/Policies/MesuresAdditionnellesPolicy.php`
