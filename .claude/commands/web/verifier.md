---
description: Vérifie le front Next.js (lint, types, tests Vitest) et rend un verdict, sans rien corriger.
allowed-tools: Bash(npm run lint *), Bash(npm run typecheck *), Bash(npm run test *)
---

Fichiers modifiés côté front :
!`git status --short -- web`

Vérifie le front, sans rien modifier, depuis le dossier `web/` :
Lance chaque commande telle quelle (`cd web` devant est accepté), sans `;`, sans `|` et sans `echo` : sous cette forme, elle est pré-autorisée.

1. `npm run lint`
2. `npm run typecheck`
3. `npm run test`
4. Rends un tableau `Commande | Résultat | Détail`, puis une dernière ligne :
   `VERDICT WEB : OK` ou `VERDICT WEB : À CORRIGER`.

Ne corrige rien, même si une vérification échoue : c'est le développeur qui décide de la suite.
