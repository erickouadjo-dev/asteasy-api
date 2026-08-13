# Documentation API - Risques (Risks)

## Aperçu

- Racine endpoints : `https://asteasy.deepinovia.com/api/api`
- Préfixe API : `/v1`
- Ressource : `/risques`
- Middleware de groupe : `cors`, `multi_authentication`
- **Isolation Multi-Tenant** : Non applicable (la table `TB_RISQUES` ne contient pas de colonne `ENTREPRISE_ID` et n'est pas cloisonnée par locataire).
- Policy :
  - Lecture (`index`, `show`) : tout utilisateur authentifié.
  - Écriture (`store`, `update`, `destroy`) : utilisateur de type `ADMIN` ou `POWER_USER`.

## Structure de la ressource Risque

```json
{
  "ID": 1,
  "REFERENCE_RISK": "RISK-001",
  "INTITULE_RISK": "Chute de hauteur lors du montage de l'échafaudage",
  "ID_RISK_CATEGORY": 1,
  "ID_RISK_SUBCATEGORY": 1,
  "CONSEQUENSE_ULTIME": "Fractures graves ou décès",
  "ID_MESURES_CONTROLE": 1,
  "FREQUENCE_RISK_INITIAL": "Mensuelle",
  "GRAVITE_RISK_INITIAL": "Critique",
  "ID_MATRICE_RISQUE": null,
  "ID_MESURES_ADDITIONNELLES": 1,
  "FREQUENCE_RISK_FINAL": "Faible",
  "GRAVITE_RISK_FINAL": "Faible",
  "NIVEAU_MAITRISE": "ELEVE",
  "DATE_STATUT_RISK": "2026-08-13",
  "STATUT_RISK": "MAITRISE",
  "RESPONSABLE": 3,
  "DATE_CONTROLE": "2026-08-13",
  "COMMENTAIRES": "Harnais de sécurité vérifié et fonctionnel.",
  "IS_DELETE": false,
  "created_at": "2026-08-13T13:00:00.000000Z",
  "updated_at": "2026-08-13T13:00:00.000000Z",
  "deleted_at": null,
  "category": {
    "ID": 1,
    "INTITULE": "Risques Professionnels",
    "DESCRIPTION": "Catégorie regroupant les risques de l'environnement de travail direct."
  },
  "subcategory": {
    "ID": 1,
    "INTITULE": "Chute de hauteur",
    "DESCRIPTION": "Risques liés aux travaux en hauteur sans protection adéquate.",
    "ID_RISK_CATEGORY": 1
  },
  "mesure_controle": {
    "ID": 1,
    "INTITULE": "Port de masque obligatoire",
    "DESCRIPTION": "Port obligatoire de masques FFP2 dans les zones poussiéreuses.",
    "FREQUENCE": "Permanent",
    "GRAVITE": "Haute"
  },
  "mesure_additionnelle": {
    "ID": 1,
    "INTITULE": "Installation d'un filet de protection",
    "DESCRIPTION": "Installer des filets antichute en dessous de la zone d'échafaudage.",
    "FREQUENCE": "Une fois",
    "GRAVITE": "Moyenne"
  },
  "matrice_risque": null,
  "responsable_user": {
    "id": 3,
    "nom": "DUPONT",
    "prenom": "Jean",
    "email": "jean.dupont@example.com"
  }
}
```

## Endpoints

### 1) Lister les risques
- Méthode : `GET`
- URL : `/v1/risques`
- Autorisation : utilisateur authentifié

Paramètres query optionnels :
- `per_page` (int, défaut : `15`)
- `page` (int, défaut : `1`)
- `search` (string, filtre sur `REFERENCE_RISK`, `INTITULE_RISK`, `CONSEQUENSE_ULTIME` et `COMMENTAIRES`)

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/risques?per_page=10&page=1&search=chute" \
  -H "Authorization: Bearer <token>"
```

Réponse 200 (les relations associées sont chargées automatiquement) :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": [
    {
      "ID": 1,
      "REFERENCE_RISK": "RISK-001",
      "INTITULE_RISK": "Chute de hauteur lors du montage de l'échafaudage",
      "ID_RISK_CATEGORY": 1,
      "ID_RISK_SUBCATEGORY": 1,
      "CONSEQUENSE_ULTIME": "Fractures graves ou décès",
      "ID_MESURES_CONTROLE": 1,
      "FREQUENCE_RISK_INITIAL": "Mensuelle",
      "GRAVITE_RISK_INITIAL": "Critique",
      "ID_MATRICE_RISQUE": null,
      "ID_MESURES_ADDITIONNELLES": 1,
      "FREQUENCE_RISK_FINAL": "Faible",
      "GRAVITE_RISK_FINAL": "Faible",
      "NIVEAU_MAITRISE": "ELEVE",
      "DATE_STATUT_RISK": "2026-08-13",
      "STATUT_RISK": "MAITRISE",
      "RESPONSABLE": 3,
      "DATE_CONTROLE": "2026-08-13",
      "COMMENTAIRES": "Harnais de sécurité vérifié et fonctionnel.",
      "IS_DELETE": false,
      "created_at": "2026-08-13T13:00:00.000000Z",
      "updated_at": "2026-08-13T13:00:00.000000Z",
      "deleted_at": null,
      "category": {
        "ID": 1,
        "INTITULE": "Risques Professionnels",
        "DESCRIPTION": "Catégorie regroupant les risques de l'environnement de travail direct."
      },
      "subcategory": {
        "ID": 1,
        "INTITULE": "Chute de hauteur",
        "DESCRIPTION": "Risques liés aux travaux en hauteur sans protection adéquate.",
        "ID_RISK_CATEGORY": 1
      },
      "mesure_controle": {
        "ID": 1,
        "INTITULE": "Port de masque obligatoire"
      },
      "mesure_additionnelle": {
        "ID": 1,
        "INTITULE": "Installation d'un filet de protection"
      },
      "matrice_risque": null,
      "responsable_user": {
        "id": 3,
        "nom": "DUPONT"
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

### 2) Créer un risque
- Méthode : `POST`
- URL : `/v1/risques`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
- `REFERENCE_RISK` (requis, string, max 255, unique dans `TB_RISQUES`)
- `INTITULE_RISK` (requis, string, max 255)
- `ID_RISK_CATEGORY` (optionnel, integer, doit exister dans `TB_RISK_CATEGORY`)
- `ID_RISK_SUBCATEGORY` (optionnel, integer, doit exister dans `TB_RISK_SUBCATEGORY`)
- `CONSEQUENSE_ULTIME` (requis, string, max 255)
- `ID_MESURES_CONTROLE` (optionnel, integer, doit exister dans `TB_MESURES_CONTROLE`)
- `FREQUENCE_RISK_INITIAL` (requis, string, max 255)
- `GRAVITE_RISK_INITIAL` (requis, string, max 255)
- `ID_MATRICE_RISQUE` (optionnel, integer, doit exister dans `TB_MATRICE_RISQUE`)
- `ID_MESURES_ADDITIONNELLES` (optionnel, integer, doit exister dans `TB_MESURES_ADDITIONNELLES`)
- `FREQUENCE_RISK_FINAL` (requis, string, max 255)
- `GRAVITE_RISK_FINAL` (requis, string, max 255)
- `NIVEAU_MAITRISE` (requis, enum: `ELEVE`, `MOYENNE`, `FAIBLE`)
- `DATE_STATUT_RISK` (requis, date au format `Y-m-d`)
- `STATUT_RISK` (requis, enum: `MAITRISE`, `PARTIELLEMENT_MAITRISE`, `NON_MAITRISE`)
- `RESPONSABLE` (optionnel, integer, doit exister dans `utilisateurs` sous `id`)
- `DATE_CONTROLE` (requis, date au format `Y-m-d`)
- `COMMENTAIRES` (requis, string)

Exemple :
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/risques" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "REFERENCE_RISK": "RISK-002",
    "INTITULE_RISK": "Inhalation de gaz nocifs",
    "CONSEQUENSE_ULTIME": "Intoxication et perte de conscience",
    "FREQUENCE_RISK_INITIAL": "Moyenne",
    "GRAVITE_RISK_INITIAL": "Critique",
    "FREQUENCE_RISK_FINAL": "Faible",
    "GRAVITE_RISK_FINAL": "Moyenne",
    "NIVEAU_MAITRISE": "MOYENNE",
    "DATE_STATUT_RISK": "2026-08-13",
    "STATUT_RISK": "PARTIELLEMENT_MAITRISE",
    "DATE_CONTROLE": "2026-08-13",
    "COMMENTAIRES": "Ventilation installée.",
    "ID_RISK_CATEGORY": 1,
    "ID_RISK_SUBCATEGORY": 1,
    "ID_MESURES_CONTROLE": 1,
    "ID_MESURES_ADDITIONNELLES": 1
  }'
```

Réponse 201 :
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 2,
    "REFERENCE_RISK": "RISK-002",
    "INTITULE_RISK": "Inhalation de gaz nocifs",
    "ID_RISK_CATEGORY": 1,
    "ID_RISK_SUBCATEGORY": 1,
    "CONSEQUENSE_ULTIME": "Intoxication et perte de conscience",
    "ID_MESURES_CONTROLE": 1,
    "FREQUENCE_RISK_INITIAL": "Moyenne",
    "GRAVITE_RISK_INITIAL": "Critique",
    "ID_MATRICE_RISQUE": null,
    "ID_MESURES_ADDITIONNELLES": 1,
    "FREQUENCE_RISK_FINAL": "Faible",
    "GRAVITE_RISK_FINAL": "Moyenne",
    "NIVEAU_MAITRISE": "MOYENNE",
    "DATE_STATUT_RISK": "2026-08-13",
    "STATUT_RISK": "PARTIELLEMENT_MAITRISE",
    "RESPONSABLE": null,
    "DATE_CONTROLE": "2026-08-13",
    "COMMENTAIRES": "Ventilation installée.",
    "IS_DELETE": false,
    "created_at": "2026-08-13T13:05:00.000000Z",
    "updated_at": "2026-08-13T13:05:00.000000Z",
    "deleted_at": null,
    "category": {
      "ID": 1,
      "INTITULE": "Risques Professionnels"
    },
    "subcategory": {
      "ID": 1,
      "INTITULE": "Chute de hauteur"
    },
    "mesure_controle": {
      "ID": 1,
      "INTITULE": "Port de masque obligatoire"
    },
    "mesure_additionnelle": {
      "ID": 1,
      "INTITULE": "Installation d'un filet de protection"
    },
    "matrice_risque": null,
    "responsable_user": null
  }
}
```

---

### 3) Récupérer un risque
- Méthode : `GET`
- URL : `/v1/risques/{id}`
- Autorisation : utilisateur authentifié

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/risques/2" \
  -H "Authorization: Bearer <token>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "REFERENCE_RISK": "RISK-002",
    "INTITULE_RISK": "Inhalation de gaz nocifs",
    "ID_RISK_CATEGORY": 1,
    "ID_RISK_SUBCATEGORY": 1,
    "CONSEQUENSE_ULTIME": "Intoxication et perte de conscience",
    "ID_MESURES_CONTROLE": 1,
    "FREQUENCE_RISK_INITIAL": "Moyenne",
    "GRAVITE_RISK_INITIAL": "Critique",
    "ID_MATRICE_RISQUE": null,
    "ID_MESURES_ADDITIONNELLES": 1,
    "FREQUENCE_RISK_FINAL": "Faible",
    "GRAVITE_RISK_FINAL": "Moyenne",
    "NIVEAU_MAITRISE": "MOYENNE",
    "DATE_STATUT_RISK": "2026-08-13",
    "STATUT_RISK": "PARTIELLEMENT_MAITRISE",
    "RESPONSABLE": null,
    "DATE_CONTROLE": "2026-08-13",
    "COMMENTAIRES": "Ventilation installée.",
    "IS_DELETE": false,
    "created_at": "2026-08-13T13:05:00.000000Z",
    "updated_at": "2026-08-13T13:05:00.000000Z",
    "deleted_at": null,
    "category": {
      "ID": 1,
      "INTITULE": "Risques Professionnels"
    },
    "subcategory": {
      "ID": 1,
      "INTITULE": "Chute de hauteur"
    },
    "mesure_controle": {
      "ID": 1,
      "INTITULE": "Port de masque obligatoire"
    },
    "mesure_additionnelle": {
      "ID": 1,
      "INTITULE": "Installation d'un filet de protection"
    },
    "matrice_risque": null,
    "responsable_user": null
  }
}
```

---

### 4) Mettre à jour un risque
- Méthode : `PUT`
- URL : `/v1/risques/{id}`
- Autorisation : `ADMIN` ou `POWER_USER`
- Content-Type : `application/json`

Body JSON :
- tous les champs sont optionnels
- `REFERENCE_RISK` (optionnel, string, max 255, unique sauf pour cet ID)
- `INTITULE_RISK` (optionnel, string, max 255)
- `ID_RISK_CATEGORY` (optionnel, integer, doit exister dans `TB_RISK_CATEGORY`)
- `ID_RISK_SUBCATEGORY` (optionnel, integer, doit exister dans `TB_RISK_SUBCATEGORY`)
- `CONSEQUENSE_ULTIME` (optionnel, string, max 255)
- `ID_MESURES_CONTROLE` (optionnel, integer, doit exister dans `TB_MESURES_CONTROLE`)
- `FREQUENCE_RISK_INITIAL` (optionnel, string, max 255)
- `GRAVITE_RISK_INITIAL` (optionnel, string, max 255)
- `ID_MATRICE_RISQUE` (optionnel, integer, doit exister dans `TB_MATRICE_RISQUE`)
- `ID_MESURES_ADDITIONNELLES` (optionnel, integer, doit exister dans `TB_MESURES_ADDITIONNELLES`)
- `FREQUENCE_RISK_FINAL` (optionnel, string, max 255)
- `GRAVITE_RISK_FINAL` (optionnel, string, max 255)
- `NIVEAU_MAITRISE` (optionnel, enum: `ELEVE`, `MOYENNE`, `FAIBLE`)
- `DATE_STATUT_RISK` (optionnel, date au format `Y-m-d`)
- `STATUT_RISK` (optionnel, enum: `MAITRISE`, `PARTIELLEMENT_MAITRISE`, `NON_MAITRISE`)
- `RESPONSABLE` (optionnel, integer, doit exister dans `utilisateurs` sous `id`)
- `DATE_CONTROLE` (optionnel, date au format `Y-m-d`)
- `COMMENTAIRES` (optionnel, string)

Exemple :
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/risques/2" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "STATUT_RISK": "MAITRISE"
  }'
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "REFERENCE_RISK": "RISK-002",
    "INTITULE_RISK": "Inhalation de gaz nocifs",
    "ID_RISK_CATEGORY": 1,
    "ID_RISK_SUBCATEGORY": 1,
    "CONSEQUENSE_ULTIME": "Intoxication et perte de conscience",
    "ID_MESURES_CONTROLE": 1,
    "FREQUENCE_RISK_INITIAL": "Moyenne",
    "GRAVITE_RISK_INITIAL": "Critique",
    "ID_MATRICE_RISQUE": null,
    "ID_MESURES_ADDITIONNELLES": 1,
    "FREQUENCE_RISK_FINAL": "Faible",
    "GRAVITE_RISK_FINAL": "Moyenne",
    "NIVEAU_MAITRISE": "MOYENNE",
    "DATE_STATUT_RISK": "2026-08-13",
    "STATUT_RISK": "MAITRISE",
    "RESPONSABLE": null,
    "DATE_CONTROLE": "2026-08-13",
    "COMMENTAIRES": "Ventilation installée.",
    "IS_DELETE": false,
    "created_at": "2026-08-13T13:05:00.000000Z",
    "updated_at": "2026-08-13T13:10:00.000000Z",
    "deleted_at": null,
    "category": {
      "ID": 1,
      "INTITULE": "Risques Professionnels"
    },
    "subcategory": {
      "ID": 1,
      "INTITULE": "Chute de hauteur"
    },
    "mesure_controle": {
      "ID": 1,
      "INTITULE": "Port de masque obligatoire"
    },
    "mesure_additionnelle": {
      "ID": 1,
      "INTITULE": "Installation d'un filet de protection"
    },
    "matrice_risque": null,
    "responsable_user": null
  }
}
```

---

### 5) Supprimer un risque
- Méthode : `DELETE`
- URL : `/v1/risques/{id}`
- Autorisation : `ADMIN` ou `POWER_USER`

Comportement :
- Met `IS_DELETE = true`
- Effectue un soft delete (`deleted_at` renseigné)

Exemple :
```bash
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/risques/2" \
  -H "Authorization: Bearer <token_admin_ou_power_user>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 2,
    "REFERENCE_RISK": "RISK-002",
    "INTITULE_RISK": "Inhalation de gaz nocifs",
    "ID_RISK_CATEGORY": 1,
    "ID_RISK_SUBCATEGORY": 1,
    "CONSEQUENSE_ULTIME": "Intoxication et perte de conscience",
    "ID_MESURES_CONTROLE": 1,
    "FREQUENCE_RISK_INITIAL": "Moyenne",
    "GRAVITE_RISK_INITIAL": "Critique",
    "ID_MATRICE_RISQUE": null,
    "ID_MESURES_ADDITIONNELLES": 1,
    "FREQUENCE_RISK_FINAL": "Faible",
    "GRAVITE_RISK_FINAL": "Moyenne",
    "NIVEAU_MAITRISE": "MOYENNE",
    "DATE_STATUT_RISK": "2026-08-13",
    "STATUT_RISK": "MAITRISE",
    "RESPONSABLE": null,
    "DATE_CONTROLE": "2026-08-13",
    "COMMENTAIRES": "Ventilation installée.",
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
- Contrôleur : `app/Http/Controllers/Api/V1/RisquesController.php`
- Modèle : `app/Models/Risque.php`
- Stratégie d'autorisation (Policy) : `app/Policies/RisquesPolicy.php`
