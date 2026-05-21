# API Documentation - Deconnecter

## Apercu
Cette documentation couvre l endpoint de deconnexion utilisateur.

- Racine endpoints: https://asteasy.deepinovia.com/api/api
- Prefix API: /v1
- Endpoint: /utilisateurs/deconnecter
- Methode: POST
- Middleware de groupe: cors, multi_authentication
- Policy actuelle: autorise uniquement un utilisateur authentifie

## Endpoint de deconnexion
- URL complete: https://asteasy.deepinovia.com/api/api/v1/utilisateurs/deconnecter
- Header requis:
  - Authorization: Bearer <access_token>
  - Accept: application/json

Exemple:
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/utilisateurs/deconnecter" \
  -H "Authorization: Bearer <access_token>" \
  -H "Accept: application/json"
```

Comportement:
- Revoque le token d acces courant (Passport)
- Enregistre une trace d activite de type deconnexion

Reponse 200:
```json
{
  "code_http": 200,
  "code_message": 200
}
```

Reponse 403:
```json
{
  "http_code": 403,
  "code": 403,
  "code_message": "RequÃªte non autorisÃ©e."
}
```

Reponse 500:
```json
{
  "code_http": 500,
  "code_message": "ERR_SERVER",
  "erreurs": "Une erreur est survenue."
}
```

## Sources techniques
- Route: routes/api.php
- Controller: app/Http/Controllers/Api/V1/Utilisateurs/DeconnecterController.php
- Model: app/Models/Utilisateur.php (methode deconnecter)
- Policy: app/Policies/Utilisateurs/DeconnecterPolicy.php
- Policy resource: app/Utility/PolicyResources/Utilisateurs/Deconnecter.php


