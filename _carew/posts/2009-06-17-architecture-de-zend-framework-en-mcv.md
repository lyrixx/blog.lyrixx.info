---
title: Architecture de Zend Framework en MCV
author: Greg
layout: post
permalink: /zend/architecture-de-zend-framework-en-mcv.html
tags:
  - Zend Framework
---

Vous êtes nouveaux sur le Zend Framework, et vous êtes un peu perdu avec
l’architecture MCV de ce framework. Pas de panique, je vous explique tout !
Deja, c’est quoi le MVC :

> Le Modèle-Vue-Contrôleur (en abrégé MVC, de l’anglais Model-View-Controller)
est une architecture et une méthode de conception qui organise l’interface
homme-machine (IHM) d’une application logicielle. Ce paradigme divise l’IHM en
un modèle (modèle de données), une vue (présentation, interface utilisateur) et
un contrôleur (logique de contrôle, gestion des événements, synchronisation),
chacun ayant un rôle précis dans l’interface.

D’après <a
href="http://fr.wikipedia.org/wiki/Mod%C3%A8le-Vue-Contr%C3%B4leur"
target="_blank">Wikipedia</a>

<a href="{{ relativeRoot }}/wp-content/uploads/2009/06/Zend-Architecture.png"
rel="lightbox[310]"><img class="size-full wp-image-312" title="Zend -
Architecture" src="{{ relativeRoot }}/wp-content/uploads/2009/06/Zend-Architecture.png" alt="Zend -
Architecture" width="328" height="580" /></a>

Zend - Architecture
-------------------

Voila, l’architecture typique d’une application Zend. Donc on retrouve tout en
haut de l’achitecture, le dossier du projet : `Budget`.

* Budget
    * Application : contient toute l’application, en générale ce dossier
    ce situe a un endroit de votre serveur
    qui n’est pas accessible depuis l’extérieur (comme dans les CGI-BIN)
        * default : c’est le nom d’une application, en général on
        n’en a qu’une, mais sur de grosse
        architecture, on peut en avoir plusieurs.
            * controllers : ce sont les controlleurs (le C de MVC). ce sont
            eux qui vont creer des models pour
            excecuter des actions, des taches ou des opérations, ils
            founissent ensuite le ou les résultats à
            la vue.
            * helpers : ce sont des bouts de code utilisable dans tous
            les controllers
            * layouts : c’est le template de votre site
            * models : ce sont les models de votre application. Ce sont des
            class php qui vont faire le « gros »
            du travail
            * view : ce sont le vues de votre applications. On y retrouve :
                * filters : des filtres
                * helpers : des bouts de code commun que l’on peu aussi
                utiliser dans les layouts
                * scripts : les vrais vues. Ici il faut respecter une
                structure precise, en accord avec les
                controllers, mais on y reviendra
        * bootstrap.php et initializer.php sont les fichiers de chargement
        de l’application. c’est dans
        l’initializer qu’on fait les connections a la base de donnée
        par exemple.
    * library : ici on a toutes les class dont zend a besoin pour fonctionner,
    et on a aussi toutes les autres
    class disponible (voir la doc …)
    * public : c’est le dossier public qui est accessible depuis
    l’extérieur : on y retrouve toutes les
    feuilles de style, les fichiers javascripts, les images …
    * test : ce dossier contient toutes les class qui servent a faire des
    tests unitaire.

On passe a la suite : [Comment ajouter une feuille de style CSS et un fichier Javascript avec Zend Framework]({{ relativeRoot }}/zend/css-javascript-et-zend-framework.html)
