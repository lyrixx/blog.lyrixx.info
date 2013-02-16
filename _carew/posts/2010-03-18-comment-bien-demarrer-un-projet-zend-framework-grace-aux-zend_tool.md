---
title: Comment bien démarrer un projet Zend Framework grâce aux Zend_Tool
author: Greg
layout: post
permalink: /zend/comment-bien-demarrer-un-projet-zend-framework-grace-aux-zend_tool.html
tags:
  - Zend Framework
---

Cet article est une mise à jour du tutoriel sur **l’outil Zend_Tool**. Donc
depuis quelque temps dans le Zend Framework, il y a de nouvelles
fonctionnalités. Mais pour bien faire les choses, on va reprendre Zend_Tool
depuis le début.

## Qu’est-ce que Zend_Tool ?

**Zend Tool est un outils qui s’utilise en ligne de commande** (depuis une
console) et qui **permet de mettre en place la structure de son projet**,
d’ajouter des controllers, actions, vues, modules… D’après la roadmap de Zend,
cet outils à pour objectif de devenir très puissant.

## Introduction

Alors il va falloir commencer par <a
href="http://framework.zend.com/download/latest" target="_blank">télécharger le
Zend Framework</a>. On l’extrait, chez moi c’est dans le répertoire : «
/home/lyrix/prog/php/Lib/Zend_librairie/ZendFramework-1.10.2/ ». On va donc
créer un alias pour nous faciliter la vie.  Si vous avez la chance d’être sur un
système linux (ou mac) une ligne de commande suffit :

    alias zf="/home/lyrix/prog/php/Lib/Zend_librairie/ZendFramework-1.10.2/bin/zf.sh"

Vous pouvez aussi ajouter cette commande a votre fichier de configuration du
bash (~/.bashrc), pour que l’alias soit toujours actif. On va ensuite aller dans
le dossier où l’on veut que notre projet soit crée.

    cd /home/lyrix/prog/php/2010/

On nommera ce projet « zftuto ». Création de projet avec Zend_Tool on commence
par créer notre projet :

    zf create project
    ./zftuto/ zftuto

./zftuto/ représente le PATH de la racine de l’application.
zftuto représente le nom de l’application.
Il faut maintenant se déplacer dans le dossier contenant l’application.

    cd zftuto/

## Base de donnée

Zend_Tool peut se connecter à une base de donnée, pour ensuite nous créer les
classes métiers représentant les tables de notre bases de données (ORM). On
commence par configurer notre application pour qu’elle se connecte à la BDD.
Puis ensuite on génère nos classes :

    zf configure db-adapter "adapter=pdo_mysql&username=ecommerce&password=ecommerce&dbname=ecommerce"
    zf create db-table.fromdatabase

Le nom des variables est assez explicite pour que je ne les détail pas.

## Layout

On peut maintenant activer la gestion d’un layout depuis zend tool.

    zf enable layout

## Formulaire

On peut créer un formulaire en ligne de commande :

    zf create form UserCU

## Model

Pour créer un nouveau model (ou classe métier) :

    zf create model TestModel

## Controller, Action, Vue :

Et bien sur on peut toujours ajouter un controller (avec la vue associé)

    zf create controller Users
    zf create action read Users
    zf create view read2 Users

## Module :

On peut aussi créer des modules :

    zf create module Moduletest

## Aide :

Pour de l’aide, vous pouvez directement depuis votre console exécuter la
commande suivante :

    zf ?

## Conclusion

Si on fait attention, le nommage des classes a changé. **Il y a maintenant
un namespace « application »**. Ce
qui a mon goût est plus propre, et anticipe peut être les changements
qu’il va y avoir avec php 5.3 puis php 6
