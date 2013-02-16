---
title: Architecture du Zend Framework 1.10 en MVC
author: Greg
layout: post
permalink: /zend/architecture-du-zend-framework-1-10.html
tags:
  - Zend Framework
---

Depuis quelques versions du **Zend Framework**, l’architecture de celui-ci en
mode MVC à un petit peu changé. Je vous donne le nouveau schémas, et je
l’explique ensuite. La racine des dossiers est (chez moi) le dossier « zftuto ».
Je précise que **cette structure a été obtenu grâce aux Zend_Tool** (cf prochain
tutoriel).


<a href="{{ relativeRoot }}/wp-content/uploads/2010/03/architecture.png"
rel="lightbox[1187]"><img class="alignright size-full wp-image-1188"
title="architecture" src="{{ relativeRoot }}/wp-
content/uploads/2010/03/architecture.png" alt="" width="260" height="592" /></a>

Dans ce dossier nous avons :

* **application** : c’est le dossier où tous notre code va se situer,
il ne doit pas être accessible depuis
l’extérieur.
    * **configs** : c’est le dossier qui va contenir tous nos fichiers
    de configuration
        * **application.ini** : C’est le fichier qui permet de définir
        les réglage de l’application comme
        les paramètre de base de données, les chemins par défaut …
    * **controllers** : c’est le dossier qui va contenir tous les controllers
    * **forms** : c’est un nouveau dossier qui contient maintenant tous
    les formulaires
    * **layouts**/**scripts**/ : c’est le dossier qui contient les fichiers
    de layout
        * **layout.phtml** : layout (ou template) de base de notre application
    * **models** : c’est le dossier qui contient tous les modèles, ou
    plus exactement tous notre code métier
        * **DbTable** : c’est le dossier qui contient toutes les
        représentation de nos tables (ORM).
    * **Views** : c’est le dossier qui contient tous ce qui touchent
    a l’affichage
        * **helpers** : c’est le dossier qui contient les aides de vues
        * **scripts** : c’est le dossier qui contient les dossiers contenant
        les vues
* **docs** : c’est le dossier qui contient la documentation (oui il en faut)
* **library** : c’est le dossier qui contient les librairies
* **zend** : c’est dans ce dossier qu’il faut mettre la librairie du
Zend Framework
* **public** : c’est le seul dossier qui doit être accessible depuis
l’extérieur.
    * **index.php** : c’est le fichier qui va démarrer notre application
* **tests** : c’est le dossier qui contient tous les fichiers pour faire
des tests unitaires.
