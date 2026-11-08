# Blog de Grégoire Pineau

Blog personnel, titré « Grégoire Pineau », avec des articles en anglais. Il est généré avec Hugo,
dont la version est épinglée dans `mise.toml`.

Pour écrire ou relire un article, suivre `docs/writing-style.md`. Pour un dessin, suivre
`docs/drawing-style.md`.

## Articles

Un article vit dans `content/posts/<slug>/`, avec son `index.md`, sa plante (`print.svg` pour la
liste, `plant.svg` pour le bandeau) et son image de partage (`cover.png`). Ces fichiers se
commitent : la plante d'un article publié ne doit pas changer.

- `castor post:new "Title" "Latin name"` crée un brouillon et sa plante (`--style` pour la choisir, `--date` pour l'antidater).
- `castor post:covers` dessine les images de partage qui manquent, et redessine celles des brouillons.
- `castor plant:generate umbel [seed] > plant.svg` écrit une plante sur STDOUT, sans toucher aux articles.
- `castor drawing:build <slug>` dessine les figures d'un article, depuis le `drawings.py` de son dossier.
- `castor site:preview` affiche le site avec les brouillons, sur le port 1313 ou un autre s'il est pris.
- `castor site:build` construit le site dans `public/`, sans les brouillons.

Dans le Markdown :

- `{{< note >}}Text{{< /note >}}`, après un paragraphe, écrit une note à la main dans la marge.
- `==text==` souligne au crayon.
- `signoff` dans le front matter ajoute une dernière ligne écrite à la main.
- `images: ['cover-custom.png']` dans le front matter remplace `cover.png` dans les aperçus de liens, avec `imageAlt` pour son texte alternatif. Le fichier va dans le dossier de l'article, en PNG ou JPG (`cover-custom.jpg`). `cover.png` reste généré à côté.

## Talks et Projects

`content/talks/index.md` et `content/projects/index.md` listent leurs cartes dans le front matter : des `groups`, avec un `title` facultatif et des `cards` (`image`, `title`, `url`, et `highlight: true` pour faire ressortir une carte), affichés dans l'ordre du fichier. L'image va à côté de `index.md`, de préférence en 16:9 et sans transparence : le vide autour d'une image d'un autre format prend sa couleur dominante.

## Déploiement

Chaque push sur `main` construit le site avec `castor site:build` et le publie sur GitHub Pages
(`.github/workflows/deploy.yml`). Un article passe en ligne quand il perd son `draft: true`.
