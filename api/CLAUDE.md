# Comptoir API : instructions

<!-- Fichier versionné, partagé par toute l'équipe. Cible : moins de 200 lignes.
     Les commentaires HTML comme celui-ci sont retirés avant l'injection dans le contexte de Claude. -->

## Commandes (depuis `api/`)

| Action | Commande |
|---|---|
| Installer l'API | `composer install` |
| Migrer et remplir la base de dev | `php artisan migrate --seed` |
| Tests API (Pest) | `php artisan test` |
| Un seul fichier de test | `php artisan test tests/Feature/OrderTest.php` |
| Formater le PHP | `./vendor/bin/pint` |
| Vérifier le formatage sans modifier | `./vendor/bin/pint --test` |

## Conventions API (Laravel)

- Contrôleurs minces : validation dans une FormRequest, logique métier dans `app/Services/`, sérialisation par une API Resource.
- Routes d'API dans `routes/api.php`, préfixe `/api`, noms au pluriel (`/api/orders`).
- Toute relation lue dans une boucle ou une collection est chargée avec `with()` (pas de N+1).
- Montants stockés en centimes (entiers). Jamais de `float` pour de l'argent.
- Types stricts : `declare(strict_types=1);` en tête de chaque nouveau fichier PHP, types de retour explicites.
- Pour les conventions détaillées, utiliser le skill `conventions-laravel`.

## Interdits

- Ne jamais modifier une migration déjà fusionnée : créer une nouvelle migration.
- Ne jamais lancer `php artisan migrate:fresh` ni `db:wipe` (base de développement utilisée par les exercices).

## Pièges connus

- `app/Services/LegacyInvoiceCalculator.php` (~400 lignes) n'a aucun test : écrire des tests de caractérisation avant toute modification.
- PostgreSQL ne crée pas d'index automatiquement sur une clé étrangère : ajouter l'index explicitement dans la migration.
- `GET /api/orders` est lent (sujet de la séance 3) : ne pas le « corriger au passage » dans une autre tâche.
- Les tests API utilisent une base dédiée (`comptoir_test`) avec `RefreshDatabase` : ne jamais les pointer sur la base de dev.
