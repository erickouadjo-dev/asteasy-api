# Documentation API - Risques (Risks)

## Aperçu

- Racine endpoints : `https://asteasy.deepinovia.com/api/api` *(ou `http://asteasy-api.test/api` en local)*
- Préfixe API : `/v1`
- Ressource : `/risques`
- Middleware de groupe : `cors`, `multi_authentication`
- **Isolation Multi-Tenant** : Actif (la table `TB_RISQUES` contient la colonne `ENTREPRISE_ID` et est filtrée par locataire via le trait `BelongsToTenant`).
- Policy :
  - Lecture (`index`, `show`) : tout utilisateur authentifié.
  - Écriture (`store`, `update`, `destroy`) : utilisateur de type `ADMIN` ou `POWER_USER`.

## Structure de la ressource Risque

```json
{
  "ID": 1,
  "REFERENCE_RISK": "RSK-2026-001",
  "INTITULE_RISK": "Ingestion de corps étranger (FOD) lors du décollage",
  "ID_RISK_CATEGORY": 3,
  "ID_RISK_SUBCATEGORY": 1,
  "CONSEQUENSE_ULTIME": "Endommagement compresseur réacteur et interruption de décollage (RTO).",
  "MESURES_CONTROLE": "Inspection systématique de piste anti-FOD et balayage avant chaque vague de décollages",
  "ID_MESURES_CONTROLE": null,
  "FREQUENCE_RISK_INITIAL": "Moyenne",
  "GRAVITE_RISK_INITIAL": "Critique",
  "ID_MATRICE_RISQUE": null,
  "MESURES_ADDITIONNELLES": "Installation d'une balayeuse magnétique de piste et détection automatisée",
  "ID_MESURES_ADDITIONNELLES": null,
  "FREQUENCE_RISK_FINAL": "Faible",
  "GRAVITE_RISK_FINAL": "Faible",
  "NIVEAU_MAITRISE": "ELEVE",
  "DATE_STATUT_RISK": "2026-08-20",
  "STATUT_RISK": "MAITRISE",
  "TOP_RISQUE": "OUI",
  "RESPONSABLE": 1,
  "DATE_CONTROLE": "2026-08-20",
  "COMMENTAIRES": "Balayage de piste rigoureux appliqué avant chaque rotation.",
  "ENTREPRISE_ID": 1,
  "IS_DELETE": false,
  "created_at": "2026-08-20T10:00:00.000000Z",
  "updated_at": "2026-08-20T10:00:00.000000Z",
  "deleted_at": null,
  "category": {
    "ID": 3,
    "INTITULE": "Risques Environnementaux & Piste",
    "DESCRIPTION": "Périls fauniques, FOD, état de piste et conditions météo."
  },
  "subcategory": {
    "ID": 1,
    "INTITULE": "FOD & Débris de piste",
    "DESCRIPTION": "Risques liés aux débris étrangers sur aires de mouvement.",
    "ID_RISK_CATEGORY": 3
  },
  "mesure_controle": null,
  "mesure_additionnelle": null,
  "matrice_risque": null,
  "responsable_user": {
    "id": 1,
    "nom": "ASSEMAN",
    "prenom": "Roger",
    "email": "roger.asseman@compagnie1.com"
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
- `search` (string, filtre textuel sur `REFERENCE_RISK`, `INTITULE_RISK`, `CONSEQUENSE_ULTIME`, `MESURES_CONTROLE`, `MESURES_ADDITIONNELLES` et `COMMENTAIRES`)

Exemple :
```bash
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/risques?per_page=10&page=1&search=FOD" \
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
      "REFERENCE_RISK": "RSK-2026-001",
      "INTITULE_RISK": "Ingestion de corps étranger (FOD) lors du décollage",
      "ID_RISK_CATEGORY": 3,
      "ID_RISK_SUBCATEGORY": 1,
      "CONSEQUENSE_ULTIME": "Endommagement compresseur réacteur et interruption de décollage (RTO).",
      "MESURES_CONTROLE": "Inspection systématique de piste anti-FOD et balayage avant chaque vague de décollages",
      "ID_MESURES_CONTROLE": null,
      "FREQUENCE_RISK_INITIAL": "Moyenne",
      "GRAVITE_RISK_INITIAL": "Critique",
      "ID_MATRICE_RISQUE": null,
      "MESURES_ADDITIONNELLES": "Installation d'une balayeuse magnétique de piste et détection automatisée",
      "ID_MESURES_ADDITIONNELLES": null,
      "FREQUENCE_RISK_FINAL": "Faible",
      "GRAVITE_RISK_FINAL": "Faible",
      "NIVEAU_MAITRISE": "ELEVE",
      "DATE_STATUT_RISK": "2026-08-20",
      "STATUT_RISK": "MAITRISE",
      "RESPONSABLE": 1,
      "DATE_CONTROLE": "2026-08-20",
      "COMMENTAIRES": "Balayage de piste rigoureux appliqué avant chaque rotation.",
      "ENTREPRISE_ID": 1,
      "IS_DELETE": false,
      "created_at": "2026-08-20T10:00:00.000000Z",
      "updated_at": "2026-08-20T10:00:00.000000Z",
      "deleted_at": null,
      "category": {
        "ID": 3,
        "INTITULE": "Risques Environnementaux & Piste"
      },
      "subcategory": {
        "ID": 1,
        "INTITULE": "FOD & Débris de piste"
      },
      "mesure_controle": null,
      "mesure_additionnelle": null,
      "matrice_risque": null,
      "responsable_user": {
        "id": 1,
        "nom": "ASSEMAN",
        "prenom": "Roger"
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
- `CONSEQUENSE_ULTIME` (requis, string)
- `MESURES_CONTROLE` (optionnel, string / texte libre pour la mesure de contrôle associée)
- `ID_MESURES_CONTROLE` (optionnel, integer, clé étrangère de compatibilité)
- `FREQUENCE_RISK_INITIAL` (requis, string, max 255)
- `GRAVITE_RISK_INITIAL` (requis, string, max 255)
- `ID_MATRICE_RISQUE` (optionnel, integer, doit exister dans `TB_MATRICE_RISQUE`)
- `MESURES_ADDITIONNELLES` (optionnel, string / texte libre pour la mesure additionnelle)
- `ID_MESURES_ADDITIONNELLES` (optionnel, integer, clé étrangère de compatibilité)
- `FREQUENCE_RISK_FINAL` (requis, string, max 255)
- `GRAVITE_RISK_FINAL` (requis, string, max 255)
- `NIVEAU_MAITRISE` (requis, enum: `ELEVE`, `MOYENNE`, `FAIBLE`)
- `DATE_STATUT_RISK` (requis, date au format `Y-m-d`)
- `STATUT_RISK` (requis, enum: `MAITRISE`, `PARTIELLEMENT_MAITRISE`, `NON_MAITRISE`)
- `RESPONSABLE` (optionnel, integer, doit exister dans `utilisateurs` sous `id`)
- `DATE_CONTROLE` (requis, date au format `Y-m-d`)
- `COMMENTAIRES` (optionnel, string)
- `ENTREPRISE_ID` (optionnel, integer, doit exister dans `TB_ENTREPRISE`)

Exemple :
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/risques" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "REFERENCE_RISK": "RSK-2026-004",
    "INTITULE_RISK": "Inhalation de gaz nocifs en maintenance",
    "CONSEQUENSE_ULTIME": "Intoxication et perte de conscience temporaire",
    "MESURES_CONTROLE": "Port de masque respiratoire FFP3 et contrôle d'''atmosphère",
    "FREQUENCE_RISK_INITIAL": "Moyenne",
    "GRAVITE_RISK_INITIAL": "Critique",
    "MESURES_ADDITIONNELLES": "Système d'''extraction d'''air localisée et capteurs autonomes",
    "FREQUENCE_RISK_FINAL": "Faible",
    "GRAVITE_RISK_FINAL": "Moyenne",
    "NIVEAU_MAITRISE": "MOYENNE",
    "DATE_STATUT_RISK": "2026-08-28",
    "STATUT_RISK": "PARTIELLEMENT_MAITRISE",
    "DATE_CONTROLE": "2026-08-28",
    "COMMENTAIRES": "Ventilation installée dans les hangars.",
    "ID_RISK_CATEGORY": 1,
    "ID_RISK_SUBCATEGORY": 1
  }'
```

Réponse 201 :
```json
{
  "code_http": 201,
  "code_message": 201,
  "data": {
    "ID": 4,
    "REFERENCE_RISK": "RSK-2026-004",
    "INTITULE_RISK": "Inhalation de gaz nocifs en maintenance",
    "ID_RISK_CATEGORY": 1,
    "ID_RISK_SUBCATEGORY": 1,
    "CONSEQUENSE_ULTIME": "Intoxication et perte de conscience temporaire",
    "MESURES_CONTROLE": "Port de masque respiratoire FFP3 et contrôle d'atmosphère",
    "ID_MESURES_CONTROLE": null,
    "FREQUENCE_RISK_INITIAL": "Moyenne",
    "GRAVITE_RISK_INITIAL": "Critique",
    "ID_MATRICE_RISQUE": null,
    "MESURES_ADDITIONNELLES": "Système d'extraction d'air localisée et capteurs autonomes",
    "ID_MESURES_ADDITIONNELLES": null,
    "FREQUENCE_RISK_FINAL": "Faible",
    "GRAVITE_RISK_FINAL": "Moyenne",
    "NIVEAU_MAITRISE": "MOYENNE",
    "DATE_STATUT_RISK": "2026-08-28",
    "STATUT_RISK": "PARTIELLEMENT_MAITRISE",
    "RESPONSABLE": null,
    "DATE_CONTROLE": "2026-08-28",
    "COMMENTAIRES": "Ventilation installée dans les hangars.",
    "ENTREPRISE_ID": 1,
    "IS_DELETE": false,
    "created_at": "2026-08-28T01:30:00.000000Z",
    "updated_at": "2026-08-28T01:30:00.000000Z",
    "deleted_at": null,
    "category": {
      "ID": 1,
      "INTITULE": "Risques Organisationnels & Humains"
    },
    "subcategory": {
      "ID": 1,
      "INTITULE": "Chute de hauteur / Maintenance en hangar"
    },
    "mesure_controle": null,
    "mesure_additionnelle": null,
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
curl -X GET "https://asteasy.deepinovia.com/api/api/v1/risques/4" \
  -H "Authorization: Bearer <token>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 4,
    "REFERENCE_RISK": "RSK-2026-004",
    "INTITULE_RISK": "Inhalation de gaz nocifs en maintenance",
    "ID_RISK_CATEGORY": 1,
    "ID_RISK_SUBCATEGORY": 1,
    "CONSEQUENSE_ULTIME": "Intoxication et perte de conscience temporaire",
    "MESURES_CONTROLE": "Port de masque respiratoire FFP3 et contrôle d'atmosphère",
    "ID_MESURES_CONTROLE": null,
    "FREQUENCE_RISK_INITIAL": "Moyenne",
    "GRAVITE_RISK_INITIAL": "Critique",
    "ID_MATRICE_RISQUE": null,
    "MESURES_ADDITIONNELLES": "Système d'extraction d'air localisée et capteurs autonomes",
    "ID_MESURES_ADDITIONNELLES": null,
    "FREQUENCE_RISK_FINAL": "Faible",
    "GRAVITE_RISK_FINAL": "Moyenne",
    "NIVEAU_MAITRISE": "MOYENNE",
    "DATE_STATUT_RISK": "2026-08-28",
    "STATUT_RISK": "PARTIELLEMENT_MAITRISE",
    "RESPONSABLE": null,
    "DATE_CONTROLE": "2026-08-28",
    "COMMENTAIRES": "Ventilation installée dans les hangars.",
    "ENTREPRISE_ID": 1,
    "IS_DELETE": false,
    "created_at": "2026-08-28T01:30:00.000000Z",
    "updated_at": "2026-08-28T01:30:00.000000Z",
    "deleted_at": null,
    "category": {
      "ID": 1,
      "INTITULE": "Risques Organisationnels & Humains"
    },
    "subcategory": {
      "ID": 1,
      "INTITULE": "Chute de hauteur / Maintenance en hangar"
    },
    "mesure_controle": null,
    "mesure_additionnelle": null,
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
- `CONSEQUENSE_ULTIME` (optionnel, string)
- `MESURES_CONTROLE` (optionnel, string / texte libre)
- `ID_MESURES_CONTROLE` (optionnel, integer)
- `FREQUENCE_RISK_INITIAL` (optionnel, string, max 255)
- `GRAVITE_RISK_INITIAL` (optionnel, string, max 255)
- `ID_MATRICE_RISQUE` (optionnel, integer, doit exister dans `TB_MATRICE_RISQUE`)
- `MESURES_ADDITIONNELLES` (optionnel, string / texte libre)
- `ID_MESURES_ADDITIONNELLES` (optionnel, integer)
- `FREQUENCE_RISK_FINAL` (optionnel, string, max 255)
- `GRAVITE_RISK_FINAL` (optionnel, string, max 255)
- `NIVEAU_MAITRISE` (optionnel, enum: `ELEVE`, `MOYENNE`, `FAIBLE`)
- `DATE_STATUT_RISK` (optionnel, date au format `Y-m-d`)
- `STATUT_RISK` (optionnel, enum: `MAITRISE`, `PARTIELLEMENT_MAITRISE`, `NON_MAITRISE`)
- `RESPONSABLE` (optionnel, integer, doit exister dans `utilisateurs` sous `id`)
- `DATE_CONTROLE` (optionnel, date au format `Y-m-d`)
- `COMMENTAIRES` (optionnel, string)
- `ENTREPRISE_ID` (optionnel, integer, doit exister dans `TB_ENTREPRISE`)

Exemple :
```bash
curl -X PUT "https://asteasy.deepinovia.com/api/api/v1/risques/4" \
  -H "Authorization: Bearer <token_admin_ou_power_user>" \
  -H "Content-Type: application/json" \
  -d '{
    "STATUT_RISK": "MAITRISE",
    "MESURES_ADDITIONNELLES": "Système d'''extraction d'''air localisée validé et opérationnel"
  }'
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 4,
    "REFERENCE_RISK": "RSK-2026-004",
    "INTITULE_RISK": "Inhalation de gaz nocifs en maintenance",
    "ID_RISK_CATEGORY": 1,
    "ID_RISK_SUBCATEGORY": 1,
    "CONSEQUENSE_ULTIME": "Intoxication et perte de conscience temporaire",
    "MESURES_CONTROLE": "Port de masque respiratoire FFP3 et contrôle d'atmosphère",
    "ID_MESURES_CONTROLE": null,
    "FREQUENCE_RISK_INITIAL": "Moyenne",
    "GRAVITE_RISK_INITIAL": "Critique",
    "ID_MATRICE_RISQUE": null,
    "MESURES_ADDITIONNELLES": "Système d'extraction d'air localisée validé et opérationnel",
    "ID_MESURES_ADDITIONNELLES": null,
    "FREQUENCE_RISK_FINAL": "Faible",
    "GRAVITE_RISK_FINAL": "Moyenne",
    "NIVEAU_MAITRISE": "MOYENNE",
    "DATE_STATUT_RISK": "2026-08-28",
    "STATUT_RISK": "MAITRISE",
    "RESPONSABLE": null,
    "DATE_CONTROLE": "2026-08-28",
    "COMMENTAIRES": "Ventilation installée dans les hangars.",
    "ENTREPRISE_ID": 1,
    "IS_DELETE": false,
    "created_at": "2026-08-28T01:30:00.000000Z",
    "updated_at": "2026-08-28T01:35:00.000000Z",
    "deleted_at": null,
    "category": {
      "ID": 1,
      "INTITULE": "Risques Organisationnels & Humains"
    },
    "subcategory": {
      "ID": 1,
      "INTITULE": "Chute de hauteur / Maintenance en hangar"
    },
    "mesure_controle": null,
    "mesure_additionnelle": null,
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
curl -X DELETE "https://asteasy.deepinovia.com/api/api/v1/risques/4" \
  -H "Authorization: Bearer <token_admin_ou_power_user>"
```

Réponse 200 :
```json
{
  "code_http": 200,
  "code_message": 200,
  "data": {
    "ID": 4,
    "REFERENCE_RISK": "RSK-2026-004",
    "INTITULE_RISK": "Inhalation de gaz nocifs en maintenance",
    "ID_RISK_CATEGORY": 1,
    "ID_RISK_SUBCATEGORY": 1,
    "CONSEQUENSE_ULTIME": "Intoxication et perte de conscience temporaire",
    "MESURES_CONTROLE": "Port de masque respiratoire FFP3 et contrôle d'atmosphère",
    "ID_MESURES_CONTROLE": null,
    "FREQUENCE_RISK_INITIAL": "Moyenne",
    "GRAVITE_RISK_INITIAL": "Critique",
    "ID_MATRICE_RISQUE": null,
    "MESURES_ADDITIONNELLES": "Système d'extraction d'air localisée validé et opérationnel",
    "ID_MESURES_ADDITIONNELLES": null,
    "FREQUENCE_RISK_FINAL": "Faible",
    "GRAVITE_RISK_FINAL": "Moyenne",
    "NIVEAU_MAITRISE": "MOYENNE",
    "DATE_STATUT_RISK": "2026-08-28",
    "STATUT_RISK": "MAITRISE",
    "RESPONSABLE": null,
    "DATE_CONTROLE": "2026-08-28",
    "COMMENTAIRES": "Ventilation installée dans les hangars.",
    "ENTREPRISE_ID": 1,
    "IS_DELETE": true,
    "created_at": "2026-08-28T01:30:00.000000Z",
    "updated_at": "2026-08-28T01:35:00.000000Z",
    "deleted_at": "2026-08-28T01:40:00.000000Z"
  }
}
```

---

## Sources techniques
- Routes : `routes/api.php`
- Contrôleur : `app/Http/Controllers/Api/V1/RisquesController.php`
- Modèle : `app/Models/Risque.php`
- Migration : `database/migrations/2026_08_28_013000_change_mesures_fields_to_text_in_tb_risques_table.php`
- Stratégie d'autorisation (Policy) : `app/Policies/RisquesPolicy.php`
