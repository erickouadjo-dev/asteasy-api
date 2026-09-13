# Documentation API - Types d'Événements (Event Types)

## Aperçu

- Racine endpoints : `https://asteasy.deepinovia.com/api/api`
- Préfixe API : `/v1`
- Ressource : `/event-types`
- Middleware de groupe : `cors`, `multi_authentication`
- **Isolation Multi-Tenant** : Actif (la table `TB_EVENT_TYPE` contient la colonne `ENTREPRISE_ID` et est filtrée par locataire via le trait `BelongsToTenant`).
- Policy :
  - Lecture (`index`, `show`) : tout utilisateur authentifié.
  - Écriture (`store`, `update`, `destroy`) : utilisateur de type `ADMIN` ou `POWER_USER`.

## Structure de la ressource Type d'Événement

```json
{
  "ID": 1,
  "CODE": "ACC",
  "LIBELLE": "ACCIDENT",
  "DESCRIPTION": "Événement lié à l'utilisation d'un aéronef ayant entraîné des dommages corporels graves/mortels ou des dégâts majeurs.",
  "ENTREPRISE_ID": 1,
  "IS_DELETE": false,
  "created_at": "2026-09-12T00:00:00.000000Z",
  "updated_at": "2026-09-12T00:00:00.000000Z",
  "deleted_at": null
}
```

## Endpoints

### 1) Lister les types d'événements
- Méthode : `GET`
- URL : `/v1/event-types`
- Autorisation : utilisateur authentifié

Paramètres query optionnels :
- `per_page` (int, défaut : `15`)
- `page` (int, défaut : `1`)
- `search` (string, filtre sur `CODE`, `LIBELLE` et `DESCRIPTION`)

### 2) Créer un type d'événement
- Méthode : `POST`
- URL : `/v1/event-types`
- Autorisation : `ADMIN` ou `POWER_USER`

Corps de la requête (JSON) :
```json
{
  "CODE": "EVT-TECH",
  "LIBELLE": "ÉVÉNEMENT TECHNIQUE",
  "DESCRIPTION": "Anomalie ou dysfonctionnement technique au sol ou en vol."
}
```

### 3) Récupérer un type d'événement
- Méthode : `GET`
- URL : `/v1/event-types/{id}`

### 4) Modifier un type d'événement
- Méthode : `PUT`
- URL : `/v1/event-types/{id}`

### 5) Supprimer un type d'événement
- Méthode : `DELETE`
- URL : `/v1/event-types/{id}`
