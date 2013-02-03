---
title: 'Zend Framework 1.9 : Comment lier une feuille de style css ou un js au layout'
author: Greg
layout: post
permalink: /zend/zend-framework-1-9-lier-une-feuille-de-style-css-ou-un-js-au-layout/
tags:
  - Zend Framework
---

Comment lier un **css** et un **js** dans un projet **zend framework** (a partir
de la 1.9) directement dans le **layout** ?

Et bien a partir de la version 1.9, c’est vraiment très simple dans la mesure ou
l’équipe de ZF a intégré un script de vue (view helper) directement dans la
librairie. Du coup il suffit de rajouter la ligne suivante dans le fichier de
layout (<span style="text-decoration:
underline;">application/layout/scripts/main.phtml</span>), entre les balises
<head> :

    <link rel="stylesheet" type="text/css" media="screen,projection" href="<?php echo $this->baseUrl();?>/styles/design.css"/>

Dans ce cas, mon fichier css est dans le dossier : `public/styles/design.css`

Voila, maintenant pour un js c’est le même principe ;) :

    <script type="text/javascript" src="<?php echo $this->baseUrl();?>/styles/main.js"></script>
