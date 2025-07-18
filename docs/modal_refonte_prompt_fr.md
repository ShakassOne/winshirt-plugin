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
- Adopter une esthétique moderne, claire et épurée, inspirée de ShirtUp ( fond blanc texte noir ).
- Avec des panneaux latéraux bien délimités
- des boutons arrondis et colorés, une interface sur fond uni avec ombres douces.
- Une hiérarchie visuelle lisible et intuitive.



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
## Proposition de refonte détaillée
L'objectif est d'adopter une ergonomie inspirée de ShirtUp tout en améliorant les fonctionnalités existantes. Les points clés :
- **Panneau latéral fixe** contenant des boutons (icône + libellé) ouvrant chacun un modal secondaire.
- Les modals secondaires présentent des interactions fluides et affichent les options suivantes :
  1. **Galerie Visuelle** : images en grille, possibilité de changer les couleurs des SVG par clic direct.
  2. **Ajouter Texte** : saisie dynamique, choix de police, taille, couleur, alignements et contour. Prévisualisation instantanée.
  3. **Téléchargement Image** : zone de dépôt claire, formats autorisés (PNG, JPG, SVG, GIF) et indication des résolutions conseillées.
  4. **Illustrations SVG** : bibliothèque interne par catégories, couleurs modifiables à la volée.
  5. **IA Générative** : formulaire simplifié avec aperçu immédiat du résultat.
- Boutons de validation et d’annulation visibles dans chaque modal.
- Ajout d’animations légères pour l’ouverture et la fermeture des différentes fenêtres.
- Prévisualisation actualisée en temps réel sur le T-shirt pour toutes les modifications.
- Chargement des ressources (galerie, SVG, IA) de manière asynchrone pour optimiser les performances.
- Interface responsive et accessible (contrastes renforcés, taille des boutons adaptée).
