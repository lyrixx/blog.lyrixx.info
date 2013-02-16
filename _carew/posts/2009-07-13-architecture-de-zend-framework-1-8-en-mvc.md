---
title: Architecture de Zend Framework 1.8 en MVC
author: Greg
layout: post
permalink: /zend/architecture-de-zend-framework-1-8-en-mvc.html
tags:
  - Zend Framework
---

Voila, on vient de voir comment [faire un nouveau projet avec le
**Zend_Tool**][1]. Comme j’expliquai dans le précédant article, l’architecture
de **Zend Framework** a un petit changé. **Voila la nouvelle Architecture** :

<a href="{{ relativeRoot }}/wp-content/uploads/2009/07/zend-framework-Structure-.png">
  <img class="size-full wp-image-460" title="zend framework : Structure" src="{{ relativeRoot }}/wp-content/uploads/2009/07/zend-framework-Structure-.png" alt="zend framework : Structure" width="236" height="497" />
</a>

* ZfProject
    * Application : contient toute l’application, en générale ce dossier
    ce situe a un endroit de votre serveur
    qui n’est pas accessible depuis l’extérieur (comme dans les CGI-BIN)
        * config : Contient tous les fichiers de configuration de
        l’application
            * application.ini : on retrouve tous les configuration de votre
            application (BDD, php.ini etc)
        * controllers : ce sont les controlleurs (le C de MVC). ce sont eux
        qui vont creer des models pour excecuter
        des actions, des taches ou des opérations, ils founissent ensuite
        le ou les résultats à la vue.
        * models : ce sont les models de votre application. Ce sont des
        class php qui vont faire le « gros »
        du travail
        * view : ce sont le vues de votre applications. On y retrouve :
            * helpers : des bouts de code commun que l’on peu aussi utiliser
            dans les layouts
            * scripts : les vrais vues. Ici il faut respecter une structure
            precise, en accord avec les controllers,
            mais on y reviendra
        * bootstrap.php est le fichier de chargement de
        l’application. c’est dans le bootstrap qu’on fait
        les connections a la base de donnée par exemple.
    * library : ici on a toutes les class dont zend a besoin pour fonctionner,
    et on a aussi toutes les autres
    class disponible (voir la doc …)
    * public : c’est le dossier public qui est accessible depuis
    l’extérieur : on y retrouve toutes les
    feuilles de style, les fichiers javascripts, les images …
    * test : ce dossier contient toutes les class qui servent a faire des
    tests unitaire.

Ici on est dans une architecture ou l’on utilise pas de modules, avec des
modules, la structure est un tout petit peux différente. Je pense que l’image
parle d’elle même… Sinon je n’ai développer que le dossier modules, le reste
restant identique.

<a href="{{ relativeRoot }}/wp-content/uploads/2009/07/zend-framework-Structure-Modules.png" rel="lightbox[448]">
    <img class="size-full wp-image-473" title="zend framework : Structure Modules" src="{{ relativeRoot }}/wp-content/uploads/2009/07/zend-framework-Structure-Modules.png" alt="zend framework : Structure Modules" width="227" height="417" />
</a>

On peut passer au [bootstrap de Zend Framework 1.8][2]

[1]: {{ relativeRoot }}/zend/initialisation-dun-projet-zend.html
[2]: {{ relativeRoot }}/zend/zend-framework-1-8-et-son-bootstrap.html
