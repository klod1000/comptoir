---
description: Vérifie l'API Laravel (tests Pest et formatage Pint) et rend un verdict, sans rien corriger.
argument-hint: "[filtre de test facultatif, ex. DiscountTier]"
allowed-tools: Bash(php artisan test *), Bash(./vendor/bin/pint --test *)
---

Fichiers modifiés côté API :
!`git status --short -- api`

Filtre de tests demandé : « $ARGUMENTS »

Vérifie l'API, sans rien modifier, depuis le dossier `api/` :
Lance chaque commande telle quelle (`cd api` devant est accepté), sans `;`, sans `|` et sans `echo` : sous cette forme, elle est pré-autorisée.

1. Tests : si un filtre figure entre les guillemets ci-dessus, `php artisan test --compact --filter=<ce filtre>` ;
   si les guillemets sont vides, `php artisan test --compact` (toute la suite).
2. Formatage : `./vendor/bin/pint --test`.
3. Rends un tableau `Commande | Résultat | Détail` (tests passés et échoués, fichiers mal formatés),
   puis une dernière ligne : `VERDICT API : OK` ou `VERDICT API : À CORRIGER`.

Ne corrige rien, même si une vérification échoue : c'est le développeur qui décide de la suite.
