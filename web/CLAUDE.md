# Comptoir web : instructions

<!-- Fichier versionné, partagé par toute l'équipe. Cible : moins de 200 lignes.
     Les commentaires HTML comme celui-ci sont retirés avant l'injection dans le contexte de Claude. -->

Scripts disponibles : @package.json

## Commandes (depuis `web/`)

| Action | Commande |
|---|---|
| Installer le front | `npm ci` |
| Lint front | `npm run lint` |
| Tests unitaires front (Vitest) | `npm run test` |
| Tests bout en bout (Playwright) | `npx playwright test` |
| Build front | `npm run build` |

## Conventions front (Next.js)

- App Router : composants serveur par défaut, `"use client"` seulement si nécessaire (état, événements).
- TypeScript strict : pas de `any` ; les types partagés vont dans `web/types/`.
- Appels à l'API via le client centralisé `web/lib/api.ts`, jamais de `fetch` dispersé dans les composants.
