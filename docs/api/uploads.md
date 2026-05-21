# API Documentation - Uploads

## Apercu
Cette documentation couvre l'endpoint d'upload de fichier de la ressource `uploads`.

- Racine endpoints: `https://asteasy.deepinovia.com/api`
- Prefix API: `/v1`
- Ressource: `/uploads`
- Middleware de groupe: `cors`, `multi_authentication`
- Policy:
  - Ecriture (`store`): utilisateur authentifie

## Endpoint

### 1) Uploader un fichier
- Methode: `POST`
- URL: `/v1/uploads`
- Autorisation: utilisateur authentifie
- Content-Type: `multipart/form-data`

Champ formulaire:
- `fichier` (file, requis)

Types de fichiers autorises (`mimes`):
- `doc`
- `csv`
- `xlsx`
- `xls`
- `docx`
- `jpeg`
- `png`
- `pdf`
- `txt`

Exemple:
```bash
curl -X POST "https://asteasy.deepinovia.com/api/api/v1/uploads" \
  -H "Authorization: Bearer <token>" \
  -F "fichier=@C:/temp/rapport.pdf"
```

Reponse 201:
```json
{
  "code_http": 201,
  "code_message": 201,
  "url": "/uploads/rapport_f_1_20260512093045.pdf"
}
```

## Erreurs communes

### 400 - Validation
```json
{
  "code_http": 400,
  "code_message": "ERR_VALIDATION",
  "erreurs": [
    "The fichier field is required."
  ]
}
```

### 400 - Echec upload
```json
{
  "code_http": 400,
  "code_message": "ERR_UPLOAD",
  "erreurs": "Impossible d'enregistrer le fichier."
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

### 500 - Erreur serveur
```json
{
  "code_http": 500,
  "code_message": "ERR_SERVER",
  "erreurs": "Une erreur est survenue."
}
```

## Notes techniques
- Le fichier est enregistre dans le dossier serveur `uploads` a la racine du projet.
- Le nom du fichier est renomme au format:
  `<nom_courte>_f_<id_utilisateur>_<timestamp>.<extension>`
- Le champ `url` retourne un chemin relatif public commenceant par `/uploads/`.

## Sources techniques
Cette doc est alignee avec l'implementation actuelle:
- Routes: `routes/api.php`
- Controller: `app/Http/Controllers/Api/V1/UploadsController.php`
- Utility: `app/Utility/Upload.php`
- Policy: `app/Policies/UploadsPolicy.php`


