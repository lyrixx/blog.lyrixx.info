---
title: 'Sublime text 2 et xdebug : liens cliquables sur les fichiers'
author: Greg
layout: post
permalink: /web-dev/sublime-text-2-et-xdebug-liens-cliquables-sur-les-fichiers.html
categories:
  - Web Dev
tags:
  - Sublime text 2
  - Xdebug
---

Salut,

Si comme moi vous utilisez sublime text 2 pour développer, vous allez apprécier
ce petit tips. Par exemple quand vous avez une erreur 500 et / ou une fatal
erreur, xdebug vous affiche la backtrace avec le nom des fichiers. Grâce a ce
hacks, **il est possible d’ouvrir directement dans sublimetext le fichier
correspondant**.

## Configuration de Xdebug :

dans le fichier xdebug.ini (ou directement dans php.ini) il faut ajouter la
ligne suivante :

    xdebug.file_link_format=sblm://%f?%l

Puis redémarrer apache ou php-fpm …

## Configuration de sublime text

Il faut ajouter [ce
package](https://bitbucket.org/sublimator/sublimeprotocol/src/) dans la liste
des packages. Il suffit de dézipper le contenue de l’archive dans le dossier
package de sublime text 2, disponssible via le menu « Preference > Browse
Package »

Et voila le tours est joué ;)

## Attention

Cela ne marche pour l’instant que sur windows. Je pense que c’est assez
simple de faire la meme choses pour linux.

## Source

* [Topic du forum](http://www.sublimetext.com/forum/viewtopic.php?f=4&t=116&start=10)
* [Astuce pour VIM](http://www.koch.ro/blog/index.php?/archives/77-Firefox,-VIM,-Xdebug-Jumping-to-the-error-line.html)
