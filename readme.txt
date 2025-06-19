=== WinShirt ===
Contributors: shakass
Tags: woocommerce, personnalisation, t-shirt, loterie
Requires at least: 6.0
Tested up to: 6.5
Stable tag: 1.0.2
License: GPLv2 or later

Plugin pour personnalisation de produits et loteries via WooCommerce.

## Gestion des produits
Une page "Produits" est disponible dans le menu WinShirt pour associer des mockups, visuels et loteries aux produits WooCommerce.

## Gestion des visuels
L'onglet "Visuels" permet d'importer ou supprimer des images. Les visuels peuvent être filtrés par type ou date et validés avant utilisation.

## Personnalisation de produits
Un bouton "Personnaliser ce produit" ouvre un modale sur la fiche produit pour choisir un design, saisir du texte ou importer une image. Les sélections sont temporairement sauvegardées via localStorage et sont automatiquement restaurées lors de la réouverture du modale.

## Gestion des loteries
Une page "Loteries" permet de créer et d'administrer les tirages. Chaque loterie peut être liée à un produit WooCommerce, disposer de dates de début/fin, de lots à gagner et d'une animation personnalisée. Les participants enregistrés et leur nombre sont visibles depuis cette interface.
## Shortcodes
* `[loterie_box id="123" vedette="true"]` – carte individuelle de loterie avec tous les détails.
* `[winshirt_lotteries]` – liste de toutes les loteries sous forme de cartes.
* `[loterie_thumb id="123"]` – uniquement l'image miniature.