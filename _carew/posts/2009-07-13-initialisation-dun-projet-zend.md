---
title: Initialisation d'un projet Zend Framework Avec Zend_Tool
author: Greg
layout: post
permalink: /zend/initialisation-dun-projet-zend/
tags:
  - Zend Framework
---

On a deja vu pas mal de chose sur le **Zend Framework**, mais depuis la version
1.8.`, il y a quelques petites choses qui ont changées, comme l’architecture, et
l’arrivée de **Zend_Tool**.

C’est quoi **Zend_Tool** ? c’est un script `php` qui s’exécute en mode console
(`CLI`) et qui nous **permet de créer une base pour notre application** :

* L’architecture du projet
* Un nouveau controller
* Une nouvelle action
* Une nouvelle vue
* Un nouveau module
* etc …

On va donc recréer notre application de gestion de budget grâce aux
**Zend_Tools**. Pour faire les choses proprement, on va commencer par
télécharger la <a href="http://framework.zend.com/download"
target="_blank">dernière version de Zend Framework </a>(1.8.4). Vous la dé-
zipper ou vous voulez sur votre disque dur. Dans mon cas, c’est <span style
="text-decoration:
underline;">/home/lyrix/Prog/php/2009/ZendFramework-1.8.4</span>. Maintenant il
va falloir inclure ce path dans notre include path. c’est a dire qu’il nous sera
possible d’appeler zf.sh (ou zf.bat pour les windowsiens) depuis n’importe quels
répertoires depuis notre console favorites. **Sinon sous Linux on peut faire
plus simple**, on va tout simplement faire un alias :

    alias zf.sh=/home/lyrix/Prog/php/2009/ZendFramework-1.8.4/bin/zf.sh

Note : Si vous faites ce code directement depuis la console, l’alias sera
temporaire (juste pour cette console), si vous le faite dans le fichier
~/.bashrc, l’alias sera permanent.

Voila maintenant il nous faut créer ce projet dans le dossier qui va bien. Moi
je veux le mettre la : /home/lyrix/Prog/php/2009/Budget/ donc je me place dans
ma console dans le dossier /home/lyrix/Prog/php/2009/ puis je crée mon projet :

    cd /home/lyrix/Prog/php/2009/ zf.sh create project Budget

Voila notre projet est crée. On peut aussi faire des modules, pour ce faire il
faut aller se placer dans le projet en question, et créer un module :

    cd /home/lyrix/Prog/php/2009/Budget zf.sh create module Frontend

Voila, notre structure du projet est crée. Il faut maintenant coller la
librairie librairie dans /home/lyrix/Prog/php/2009/Budget/library. (On doit
copier tout le dossier Zend). Sous linux on peut aussi faire un lien symbolique
directement vers un autre dossier de son disque dur. :

    cd /home/lyrix/Prog/php/2009/Budget/library
    ln -s /home/lyrix/Prog/php/2009/ZendFramework-1.8.4/library/Zend/ Zend

Voila notre projet fonctionne, on peut se rendre a la page suivante pour voir le
résultat :

    http://127.0.0.1/monwww/2009/Budget/public/

<a href="{{ relativeRoot }}/wp-content/uploads/2009/07/Installation-de-Zend-Framework-1.8-finit.png" rel="lightbox[449]"><img class="size-medium wp-image-455" title="Installation de Zend Framework 1.8 - finit" src="{{ relativeRoot }}/wp-content/uploads/2009/07/Installation-de-Zend-Framework-1.8-finit-300x187.png" alt="Installation de Zend Framework 1.8 - finit" width="300" height="187" /></a>

Enfin, je rappel qu’il est quand meme possible de creer l’architecture d’un
projet directement dans Zend Studio. Il suffit de faire un nouveau projet et se
laisser guider.

On peut passer a [l’architecture de Zend Framework][2]

[2]: {{ relativeRoot }}/zend/architecture-du-zend-framework-1-10.html "Architecture du Zend Framework 1.10 en MVC"
