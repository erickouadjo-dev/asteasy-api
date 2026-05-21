# AstEasy API

API Laravel pour la gestion des utilisateurs, permissions, roles, plans et abonnements.

## Informations utiles

- Base URL de production: `https://asteasy.deepinovia.com`
- Prefix API: `/api/v1`
- Authentification: OAuth2 Passport avec Bearer token
- Middleware principal: `cors`, `multi_authentication`

## Ressources documentees

- Plans endpoints: [docs/api/plans.md](docs/api/plans.md)
- Roles endpoints: [docs/api/roles.md](docs/api/roles.md)
- Utilisateurs endpoints: [docs/api/utilisateurs.md](docs/api/utilisateurs.md)
- Authentifier endpoints: [docs/api/authentifier.md](docs/api/authentifier.md)
- Deconnecter endpoints: [docs/api/deconnecter.md](docs/api/deconnecter.md)
- Permissions endpoints: [docs/api/permissions.md](docs/api/permissions.md)
- Abonnements endpoints: [docs/api/abonnements.md](docs/api/abonnements.md)
- Employes endpoints: [docs/api/employes.md](docs/api/employes.md)
- Entreprises endpoints: [docs/api/entreprises.md](docs/api/entreprises.md)
- Bases endpoints: [docs/api/bases.md](docs/api/bases.md)

## Endpoints principaux

- `POST /api/v1/utilisateurs/authentifier`
- `POST /api/v1/utilisateurs/deconnecter`
- `GET|POST|PUT|DELETE /api/v1/plans`
- `GET|POST|PUT|DELETE /api/v1/roles`
- `GET|POST|PUT|DELETE /api/v1/permissions`
- `GET|POST|PUT|DELETE /api/v1/abonnements`
- `GET|POST|PUT|DELETE /api/v1/employes`
- `GET|POST|PUT|DELETE /api/v1/entreprises`
- `GET|POST|PUT|DELETE /api/v1/bases`

## Structure du projet

- `app/Models` : logique metier et CRUD statiques
- `app/Http/Controllers/Api/V1` : controllers API
- `app/Policies` : regles d'autorisation
- `app/Utility/PolicyResources` : classes ressources pour mapping des policies
- `routes/api.php` : declaration des endpoints
- `docs/api` : documentation Markdown des endpoints

## Pattern utilise

Le projet suit un pattern CRUD homogene :

- chaque ressource expose des methodes statiques dans son model : `lister`, `ajouter`, `recuperer`, `modifier`, `supprimer`
- les controllers deleguent au model puis retournent un JSON avec `code_http`, `code_message`, `data` ou `erreurs`
- l'autorisation passe par les policies Laravel et les `PolicyResources`
- la suppression applicative utilise `IS_DELETE` et le soft delete Laravel

## Lancement local

```bash
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```

## Notes

- certaines routes exigent un utilisateur authentifie
- les operations d'ecriture sur plusieurs ressources sont reservees aux utilisateurs `ADMIN`
- la documentation des payloads et reponses est detaillee dans [docs/api](docs/api)
