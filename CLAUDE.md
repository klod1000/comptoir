# Comptoir : instructions projet

<!-- Fichier versionné, partagé par toute l'équipe. Cible : moins de 200 lignes.
     Les commentaires HTML comme celui-ci sont retirés avant l'injection dans le contexte de Claude. -->

Comptoir est une application e-commerce B2B en deux parties, `api/` (Laravel 12, PHP 8.3, PostgreSQL, tests Pest) et `web/` (Next.js App Router, TypeScript, tests Vitest et Playwright).
Modèles métier : Product, Category, Customer, Order, OrderLine, Discount.

- `api/` : Instructions : `api/CLAUDE.md`
- `web/` : Instructions : `web/CLAUDE.md`

Ces deux fichiers se chargent automatiquement dès que Claude lit ou modifie un fichier de leur dossier.
Avant de travailler dans une partie sans avoir encore ouvert de fichier, lire son CLAUDE.md.

Toujours lancer les commandes depuis le bon sous-dossier.

## Démarrer la base

| Action | Commande |
|---|---|
| Démarrer PostgreSQL | `docker compose up -d` |
| Vérifier que le conteneur tourne | `docker compose ps` |

## Façon de travailler

- Avant une modification de plus d'un fichier : proposer un plan et attendre la validation.
- Toute nouvelle fonctionnalité ou tout correctif s'accompagne d'un test (Pest côté API, Vitest ou Playwright côté front).
- Terminer chaque tâche en lançant les tests concernés et en donnant le résultat.
- Terminer par `/api:verifier` ou `/web:verifier` selon la partie touchée.
- Messages de commit au format Conventional Commits (`feat:`, `fix:`, `test:`, `refactor:`), en français.

## Interdits

- Ne jamais lire, afficher ni modifier les fichiers `.env` (secrets). Utiliser `.env.example` comme référence.
- Ne jamais faire `git push`, ni de commit sans demande explicite.
- Ne pas ajouter de dépendance (Composer ou npm) sans le justifier et demander.
