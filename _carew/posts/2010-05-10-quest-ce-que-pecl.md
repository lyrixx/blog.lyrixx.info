---
title: Qu'est-ce que PECL ?
author: Greg
layout: post
permalink: /web-dev/quest-ce-que-pecl/
tags:
  - PECL
  - PHP
---

Il y a quelques semaines un rapport sur l’**utilisation de PHP sur les serveurs
debian est sorti** (<a href="http://popcon.debian.org/"
target="_blank">http://popcon.debian.org/</a> ; vraiment pas sexy ! ). <a
href="http://www.industrialisation-php.com/statistiques-dusage-de-php-sur-
debian/" target="_blank">Industrialisation PHP en a fait une bonne analyse</a>,
je vous conseil de la lire. En lisant ce rapport, j’ai été assez étonné sur
**les pourcentages d’utilisation des extensions PECL et PEAR**. On va donc
commencer par faire une petite piqure de rappel sur PECL.

**PECL** est l’anagramme de  **PHP Extension Community Library** (on le prononce
pickle). C’est une librairie d’extensions codées en C issu de la communauté PHP
pour le PHP. Ces extensions viennent directement se greffer au moteur PHP (<a
href="http://pecl.php.net/packages.php" target="_blank">Liste des
Extensions</a>). Il y a par exemple des extensions pour le débogage, le cache,
la sécurité, les gui … Ces extensions ont l’avantage d’être codées en C, donc
elles sont beaucoup plus rapide et performantes que les librairies codées en
PHP. Par contre il faut aussi savoir que plusieurs extensions ne sont plus
maintenues, en effet, il arrive souvent que des extensions soient directement
intégré directement au moteur PHP (comme domxml ou json)  ou alors elles sont
abandonnées :S.

**Quelques exemples d’extentions connu :**

* <a href="http://pecl.php.net/package/memcache" target="_blank">memcache</a>
    : offre la possibilité de mettre des objets en cache et donc de réduire la
    consommation CPU (et réduit l’utilisation de MySQL)
* <a href="http://pecl.php.net/package/imagick" target="_blank">imagick</a>
    : pour gérer les images. Offre plus d’options que GD2
* <a href="http://pecl.php.net/package/Xdebug" target="_self">XDebug</a> :
    pour améliorer le débogage

Il y a plusieurs types d’utilisation de ces extensions. En fait soit l’extension
rajoute des nouvelles fonctions à notre moteur php (comme avec imagick, <a
href="http://fr2.php.net/manual/fr/function.imagick-clone.php"
target="_blank">exemple</a>), soit l’extension redéfinit une fonction (on verra
le cas dans un projet tuto avec var_dump).

**Pour installer des extensions PECL**, il y a plusieurs écoles.

* Pour les utilisateurs de Windows, il y a souvent deux options :
    * Télécharger les binaires et les copier au bon endroit, puis éditer
        le php.ini
    * Télécharger le code source et re-compiler PHP (ou juste l'extension)
* Pour les utilisateurs de Linux (et surement Mac…) :
    * On peut aussi télécharger les sources a la main et compiler le tout (ou
        juste l'extension)
    * Ou Une ligne de commande suffit :

            pecl install <nom du paquet>

L’avantage d’être sous Linux, c’est que c’est plus simple et surtout plus à jour
sachant que PECL fonctionne comme un dépôt, d’ailleurs c’est un dépôt (comme apt
par exemple)

<a href="http://www.php.net/manual/fr/install.pecl.php"
target="_blank">Documentation sur php.net</a>
