# Style des dessins

Les dessins des articles imitent un tirage de cyanotype annoté au crayon, comme la plante du
bandeau. Ce guide suffit pour en faire un nouveau sans relire les anciens.

## Quand dessiner

- Pour montrer un mécanisme qui s'explique mal en phrases : une comparaison, un chemin, une
  structure. Pas pour décorer.
- Le dessin montre le mécanisme réel. Chaque élément est vrai : pas de chiffre ni d'étiquette
  inventés.
- Comme un bloc de code : une phrase courte finie par deux-points, le dessin, puis seulement ce
  qui ne saute pas aux yeux.

## Format

- SVG de 780 × 440, dans le dossier de l'article, inséré avec `![texte alternatif](nom.svg)`.
- Le texte alternatif décrit ce que montre le dessin, en une ou deux phrases. Pas de légende
  sous l'image.
- Le CSS retire l'ombre et l'arrondi des images SVG (`article img[src$=".svg"]`).
- Le SVG se commite. Comme la plante, il ne doit plus changer une fois l'article publié : le
  générer avec une graine fixe.

## Fond

- Dégradé radial `#1f5592` → `#143f74` → `#0c2b54` (`cx=".2" cy=".1" r="1.1"`, arrêts à 0, .5 et 1).
- Bord de feuille irrégulier, à 6 px du bord : une somme de trois sinusoïdes d'environ 1,5 px plus
  un peu de bruit, utilisée à la fois pour le fond et comme `clipPath` du contenu.
- Quadrillage de 65 px, trait de 1 px en `#eef3f8` à 7 % d'opacité.

## Couleurs

Trois couleurs, pas une de plus :

- `#eef3f8` (pâle) : les traits et les textes principaux.
- `#a9bed4` (doux) : le contexte. Axes, titres de colonnes, flèche du temps, légendes secondaires.
- `#f6e7b4` (crème) : ce qu'il faut regarder. Une seule idée par dessin, souvent entourée à la
  main, avec sa flèche et son annotation.

## Trait

- Crayon : extrémités et jointures arrondies, tracé tremblé. Un point tous les 12 px, décalé par
  deux sinusoïdes d'environ 1 px (moins pour les petits éléments).
- Épaisseurs : 3,4 pour le trait principal, 2,4 à 2,8 pour le secondaire, 1,5 à 2,2 pour les
  détails. Pointillés `2 11` pour séparer des zones.
- Formes récurrentes :
  - un point plein (rayon 8 à 11) pour un commit ou une valeur ;
  - une feuille à coin corné pour un fichier ;
  - une flèche légèrement courbe, pointe à deux traits ;
  - un anneau ovale tracé à la main, qui dépasse un peu son point de départ, pour entourer.

## Texte

- Écrit à la main : Caveat 700 (`static/fonts/caveat-700-normal.woff2`), converti en chemins. Un
  SVG affiché dans une `<img>` n'a pas accès aux polices de la page.
- En anglais, comme l'article. Minuscules, phrases courtes, deux lignes au plus par annotation.
- Taille 26 à 42. Incliné de -1,5° pour une annotation, droit pour une étiquette alignée.
- Rien à moins de 14 px du bord.

## Fabrication

- Le crayon et le papier sont dans `drawings/blueprint.py` : le fond (`draw()`), le texte écrit à
  la main (`text()`), le trait (`stroke()`), le point, l'anneau, la flèche et sa pointe, la feuille
  à coin corné. Une forme qui resservira s'ajoute là.
- Les dessins d'un article sont dans le `drawings.py` de son dossier : un appel à `draw()` par
  dessin, avec une graine fixe. Le script écrit les SVG à côté de lui. Hugo ne publie pas les
  fichiers `.py` (`ignoreFiles` dans `hugo.toml`).
- `castor drawing:build <slug>` lance ce script. fontTools y est épinglé, car une autre version
  pourrait écrire autrement les chemins du texte, et donc changer les dessins publiés.
- Après une modification de `blueprint.py`, relancer les dessins des articles publiés :
  `git diff` doit rester vide.
- Vérifier le rendu en PNG avant de l'intégrer, par exemple avec Chrome sans interface :
  `google-chrome --headless=new --window-size=780,440 --screenshot=out.png file://…/nom.svg`.

## Image de partage

- Chaque article a un `cover.png` de 1200 × 630, affiché dans les aperçus de liens : la feuille
  bleue, le nom latin, le titre écrit à la main, l'auteur et la plante.
- C'est un PNG, car ces aperçus n'affichent pas les SVG. Il se commite, et ne doit plus changer une
  fois l'article publié.
- `drawings/cover.py` le dessine, avec `sheet()` de `drawings/blueprint.py`, et `castor post:covers`
  le lance. Hugo le trouve grâce à son nom : ne pas laisser d'autre fichier `*cover*` dans le dossier
  de l'article.

## Exemples

- `what-s-new-in-private-stuff-my-emergency-website/drawings.py` : des courbes sur des axes.
- `why-libgit2-takes-10-minutes-to-blame-a-file-when-git-takes-0-6-s/drawings.py` : des fichiers,
  des commits, une comparaison sur deux lignes.
