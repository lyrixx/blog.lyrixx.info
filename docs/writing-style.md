# Style d'écriture

Ce guide décrit la façon d'écrire de Grégoire, pour rédiger ou relire les articles de ce blog, qui
sont en anglais. Il vient de la lecture intégrale de ses 81 articles publiés sur le blog de JoliCode
entre 2017 et 2026. Les exemples sont des citations exactes.

La référence est le style des articles de 2018 à 2023, avec les fautes corrigées. Les articles
récents ont été écrits avec des IA sans contrainte de style : ils ne servent pas de modèle.

## Règle numéro un

Ne jamais inventer ce qui fait la voix : anecdote, opinion, chiffre, échec, date. Si un élément
manque, le demander à Grégoire. Un brouillon avec un trou signalé vaut mieux qu'un souvenir
fabriqué.

## L'essentiel

- Le sujet arrive dès la deuxième phrase. Pas de préambule.
- Montrer d'abord la version naïve, puis ce qui casse, puis le correctif.
- Une phrase courte terminée par deux-points, le code, puis seulement ce qui ne saute pas aux yeux.
- Dire « I », parler à « you ». Ton parlé, contractions, phrases courtes.
- Donner son avis en une phrase et une raison. Pas de diatribe.
- Admettre sans détour ce qui a raté et ce qu'on ne sait pas.
- Une ou deux touches d'humour par article, en aparté, jamais de numéro comique.
- Ne jamais dire qu'une chose est facile : elle ne le sera pas pour tout le monde.
- Finir simplement. Pas de grande conclusion.

## Structure d'un article

### Titre

Simple et cherchable, avec le nom de l'outil : "How to fake time in a MySQL Container". La forme
« How to… » est la plus fréquente, sans être une règle. Le jeu de mots reste rare dans les titres
et vit plutôt dans les intertitres : "Fabric to the rescue", "But wait".

L'ancien corpus n'a aucune règle de capitales. Pour ce blog : capitale au premier mot et aux noms
propres seulement.

Les intertitres sont souvent des questions (15 articles anglais sur 30) : "How does it work?",
"What is in the memory?".

### Ouverture

Trois ouvertures reviennent :

- Le vécu : "I faced an issue with Elasticsearch last week", "I had to write a Doctrine migration
  to fix some bad data."
- La situation du lecteur : "Sometimes, you may want to fake the time in your test suite to ensure
  your code behaves well in all situations."
- Le goût personnel : "One of the features I love the most in Symfony is the `TaggedLocator`."

Pas d'accroche travaillée. Annoncer ce qu'on ne traitera pas si ça aide : "In this article, we'll
not cover the why".

### Déroulé

Le schéma le plus typique part d'une version simple, la casse, puis la répare :
"but there is something wrong with this.", ou "But wait a minute, `libuuid` does not work exactly
like that."

Les articles d'enquête gardent les fausses pistes, c'est leur intérêt : "Nothing was wrong here.
Bad news! Or good news: **PHP does not leak**."

Donner de vrais chiffres dès qu'il y en a. Dire clairement les limites de la solution proposée :
"So use it only in a dev environment."

La longueur médiane tourne autour de 700 mots de prose, hors code. Un article peut ne contenir
qu'une astuce et tenir en 300 mots.

### Code

- Annoncer le bloc par une phrase courte finie par deux-points : "We'll also create an interface:".
- Expliquer ensuite uniquement ce qui surprend, jamais ligne à ligne : "Have you noticed the
  `ContainerInterface $definitions`?".
- Pour un gros bloc, dire ce qu'il faut en retenir et renvoyer vers le code complet.
- Le code réel prime sur le pseudo-code.

### Fin

Une fin courte et modeste. Le titre « Conclusion » est fréquent (22 articles anglais sur 30) mais
pas obligatoire. La dernière ligne peut être :

- une limite ou un choix laissé au lecteur : "It's up to you." ;
- un sentiment : "I'm happy." ;
- un conseil pratique : "And don't forget to close your tunnel once you're done!" ;
- une pointe sèche : "it caches aggressively and dumbly!".

Pas d'appel à commenter tant que le blog n'a pas de commentaires.

## Voix

- **Personnes :** « you » partout. Sur ce blog, « I » remplace le « we » d'entreprise, qui dominait
  sur JoliCode. Le « we » de tutoriel (« we can now… ») reste possible. La narration à la première
  personne est déjà sa meilleure veine : `a-journey-to-find-a-memory-leak` compte 42 « I ».
- **Registre :** parlé, contractions, parfois familier : "What the hell is going on here?".
- **Opinions :** à plat, avec une raison : "I prefer the PECL extension over the ramsey/uuid because
  it's simpler". Elles peuvent être tranchées : "I hate useless PHPDoc."
- **Doute et échec :** dits simplement : "But I may be wrong :)", "It's really too soon to tell.",
  "Then I tried blackfire, same, it's not able to see what's going on."
- **Critique :** elle vise des outils et des choix, jamais des personnes ou des projets. Le ton reste
  mesuré : "Doctrine DQL is really behind SQL".
- **Reconnaissance :** citer et remercier les gens dont on a utilisé le travail.

## Humour

Présent dans la plupart des articles, toujours bref, au milieu d'une explication sérieuse. Quand
le sujet ne s'y prête pas, s'en passer.

- Pause comique avec des points de suspension : "`migrate` in order to… migrate your database".
- Aparté entre parenthèses : "(yes again, I do love simplicity)", "(We really don't like to wait
  😅)", "(And maybe a test, but that's another story 😁)".
- Interpellation du lecteur : "What? You committed this key in your repository?!".
- Autodérision : "But Greg, why did you choose complexity?".
- Faux drame : "Damn It!", et le français "Sacrebleu !".
- Outil personnifié : "And this is something PHP does not like."
- Texte barré pour un faux départ, à utiliser rarement.

## Phrases

- Courtes, juxtaposées, souvent 10 à 15 mots.
- Une explication, puis un verdict très court : "And then I opened the file with vim. It was full
  of HTML. OK, I think I found the culprit."
- And, But et So en début de phrase, très souvent (26 articles anglais sur 30).
- Une question, puis sa réponse : "How can we achieve that?".
- Mots familiers à garder sans en abuser : really, a bit, however, unfortunately, anyway,
  "That's why", "You may wonder", "And that's all!".
- « very » revient beaucoup. En enlever quand il y en a deux dans un paragraphe.

## Mise en forme

- Listes courtes, sans point-virgule en fin d'élément. C'est une habitude française à ne pas
  garder en anglais.
- Gras rare, pour l'idée à retenir ou un avertissement : "**you don't need JWT**". Jamais de gras en
  tête de paragraphe ni de phrases entières à répétition.
- « Note: » pour une remarque annexe.
- Emoji possibles en fin de phrase, jamais dans les titres. Quelques-uns par article au maximum.
- Pas de tableau, de note de bas de page, de TL;DR ni de sommaire.
- Pas de tiret cadratin. La virgule, les deux-points et les parenthèses suffisent.

## Anglais

Grégoire écrit dans un anglais direct, avec un accent français. Il faut corriger les erreurs sans
aseptiser le ton.

### À garder

"Et Voilà!", "Oups!", "Cherry on top", et les titres de partie posés comme une vraie question.

### À corriger

| Type | Exemple d'origine | Correction |
|---|---|---|
| Article ou pluriel manquant | "How to Fix Memory Leak" | How to fix a memory leak |
| Accord du verbe | "someone create" | someone creates |
| « few » pour « a few » | "after few successful projects" | after a few |
| Espace avant la ponctuation | "What is libsodium ?" | What is libsodium? |
| « theses » pour « these » | "theses data" | this data |
| Pluriel français | "too shorts" | too short |
| Comparatif | "twice faster" | twice as fast |
| Comparatif | "the same thing than" | the same thing as |
| Durée | "we are using Docker for years" | we have been using Docker for years |
| Préposition | "responsible of" | responsible for |
| Préposition | "Depending to" | Depending on |
| Calque | "allows to return" | makes it possible to return |
| Calque | "presented us some" | showed us some |
| Faux ami | "explications" | explanations |
| Faux ami | "implantation" | implementation |
| Faux ami | "dialogs with" | talks to |
| Faux ami | "chapter" (pour une partie d'article) | section |
| Acronyme français | "IA" | AI |

Vérifier aussi les concordances de temps dans les récits ("So I go back to my code, and I started")
et l'orthographe des noms d'outils ("Dnsmaqs" pour dnsmasq).

## À éviter

Ces formules viennent de l'écriture avec des IA sans contrainte de style, visible dans les articles
récents :

- les fins promotionnelles : "dites adieu aux assertions fastidieuses", "Les possibilités n'ont de
  limite que votre imagination !", "vous fait gagner un temps précieux au quotidien" ;
- le ton de communiqué : "This is the headline change", "you get the best of both worlds" ;
- le rythme fabriqué : énumérations en trois temps, phrases nominales en série, accroches et chutes
  travaillées, « not X but Y ».

Ne jamais présenter une chose comme facile : ce sera toujours difficile pour quelqu'un, et cette
personne se sentira frustrée. Pas de « easy », « simple », « simply », « obviously », « of course »,
ni de « just » au sens de « il suffit de ». Les anciens articles écrivaient souvent "Easy, isn't
it?" : ne plus le reprendre.

Hérité de JoliCode, à ne pas reprendre : « nous » d'équipe, mentions de l'entreprise, invitations
à recruter ou à acheter un service.

Le skill `writing:unslop` aide à retirer les tics d'IA d'un brouillon.

## Articles de référence

À relire pour retrouver la voix :

- [A Journey to find a memory leak](https://jolicode.com/blog/a-journey-to-find-a-memory-leak) :
  récit à la première personne.
- [How to See Full Request URI in FPM Status](https://jolicode.com/blog/how-to-see-full-request-uri-in-fpm-status) :
  enquête, humour.
- [Why you don't need JWT](https://jolicode.com/blog/why-you-dont-need-jwt) : opinion argumentée.
- [How TaggedLocator Can Help You Design Better Symfony Application](https://jolicode.com/blog/how-taggedlocator-can-help-you-design-better-symfony-application) :
  tutoriel, de la version naïve au correctif.
- [How to Mix Security and Form with Symfony](https://jolicode.com/blog/how-to-mix-security-and-form-with-symfony) :
  tutoriel avec de l'humour.
- [Find Segfaults in PHP like a boss](https://jolicode.com/blog/find-segfaults-in-php-like-a-boss) :
  article court et personnel.

## Corpus et limites

- **Lecture :** 65 articles signés seul (33 en anglais, 32 en français) et 16 à plusieurs.
- **Articles à plusieurs :** aucune phrase n'a pu lui être attribuée avec certitude. Ils ne servent
  pas à définir sa voix, seulement à repérer les conventions d'équipe à laisser de côté.
- **Traductions :** certains articles anglais existent aussi en français. Sur la paire comparée
  (`how-to-mix-security-and-form-with-symfony`), l'humour survit à l'anglais et les phrases y sont
  plus courtes.
- **Rupture récente :** les articles de 2025-2026 ne ressemblent pas aux précédents. En français, les
  emoji passent de 4,2 pour 1 000 mots en 2022 à 0,2 en 2026, et les points d'exclamation de 4,2 à
  0,6. En anglais, les And, But et So en début de phrase passent de 69 sur 2022-2023 à 4 puis 0 dans
  les deux articles récents, et le gras est six fois plus dense. L'article sur `beberlei/metrics` n'a plus
  d'erreur d'anglais, et celui sur MCP alterne passages fautifs et passages impeccables.
  `how-to-configure-dns-for-developing-webapp-with-dnsmaqs-once-for-all` (2023) présente le
  même profil. Ces changements viennent de l'usage d'IA sans contrainte de style. Ce guide s'appuie
  donc sur les articles de 2018 à 2023, sans celui sur dnsmasq, et garde ce style en corrigeant les
  fautes.
