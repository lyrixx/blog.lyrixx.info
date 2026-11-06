# Blog de Grégoire Pineau

Blog personnel, titré « Grégoire Pineau », avec des articles en anglais. Il est généré avec Hugo,
dont la version est épinglée dans `mise.toml`.

Pour écrire ou relire un article, suivre `docs/writing-style.md`.

## Articles

Un article vit dans `content/posts/<slug>/`, avec son `index.md` et sa plante (`print.svg` pour la
liste, `plant.svg` pour le bandeau). Ces deux fichiers se commitent : la plante d'un article publié
ne doit pas changer.

- `castor post:new "Title" "Latin name"` crée un brouillon et sa plante (`--style` pour la choisir, `--date` pour l'antidater).
- `castor plant:generate umbel [seed] > plant.svg` écrit une plante sur STDOUT, sans toucher aux articles.
- `castor site:preview` affiche le site avec les brouillons, sur le port 1313 ou un autre s'il est pris.
- `castor site:build` construit le site dans `public/`, sans les brouillons.

Dans le Markdown :

- `{{< note >}}Text{{< /note >}}`, après un paragraphe, écrit une note à la main dans la marge.
- `==text==` souligne au crayon.
- `signoff` dans le front matter ajoute une dernière ligne écrite à la main.

## Déploiement

Chaque push sur `main` construit le site avec `castor site:build` et le publie sur GitHub Pages
(`.github/workflows/deploy.yml`). Un article passe en ligne quand il perd son `draft: true`.
