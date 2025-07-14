# Refonte complète du modal de personnalisation WinShirt

Ce document sert de cahier des charges pour la refonte du modal de personnalisation WordPress basé sur WinShirt. Il vise à guider Codex (ou toute IA similaire) afin de générer un code propre et modulaire respectant les bonnes pratiques d’UX et de responsive design.

## Correction UX des interactions
- **Clic simple** : sélectionner l’élément visuel sans décaler les éléments voisins.
- **Glisser-déposer** : déplacer l’élément de manière fluide sans afficher d’options superflues.
- **Double-clic ou clic droit** : ouvrir le panneau d’édition contextuel de l’élément.

## Gestion claire des états
- Prévoir des états distincts pour chaque élément : sélectionné, en déplacement, en édition.
- Centraliser l’état via **React Context** ou **Redux** (si la stack le permet).

## Correction CSS
- Utiliser exclusivement des positions relatives pour les conteneurs et absolues pour les panneaux contextuels.
- Ajouter des transitions fluides : `transition: all 0.3s ease-in-out;`.

## Interface esthétique et ergonomique
- Implémenter une grille CSS stricte pour aligner boutons et options.
- Appliquer un effet **glassmorphisme** léger sur l’ensemble du modal.
- Harmoniser les espacements et uniformiser les boutons.

## Options d’édition isolées
- Les options de taille et de rotation ne s’affichent que lorsque l’élément est en édition (double clic ou clic droit).
- L’affichage contextuel ne doit en aucun cas provoquer le déplacement du reste de l’interface.

## Galerie et sélection de visuels
- Conserver la sélection actuelle lors d’un déplacement d’élément dans la zone de travail.
- Mettre un contour net sur l’élément sélectionné pour renforcer la visibilité.

## Boutons « Supprimer » et « Supprimer le fond »
- Intégrer ces actions dans la zone d’édition contextuelle, visible uniquement lors de l’édition.
- Supprimer tout affichage aléatoire de ces boutons lors d’un simple déplacement.

## Optimisation responsive
- Tester systématiquement sur mobile et tablette afin de garantir la bonne accessibilité des contrôles.
- Adapter automatiquement les espacements et tailles pour les différents formats d’écran.

## Qualité du code
- Générer un code clair, commenté et découpé en modules réutilisables pour simplifier la maintenance future.
