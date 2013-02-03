---
title: 'Application de site E-Commerce [Code-Source]'
author: Greg
layout: post
permalink: /zend/application-de-site-e-commerce/
tags:
  - Zend Framework
---

Comme vous l’avez peut être vu passer sur <a href="http://twitter.com/lyrixx"
target="_blank">twitter</a>, j’ai du faire pour les cours une **application
e-commerce très basique**. J’ai choisis de la faire avec Zend. Et du coup je
partage le code ici ;). **Cette application est très simplifié : il n’y a qu’une
page produit, avec la possibilité d’ajouter des produits dans un panier et
ensuite de gérer son panier. Il y a aussi la toute la partie gestion des
utilisateurs : connexion, enregistrement, déconnexion** etc… Et enfin il y a une
page paiement qui vérifie les informations de la carte bancaire.


Quelques points techniques abordés dans cette application :

* **Bootstrap** (Zend_Application)
* **Controller**, **View**, **Model** (MVC)
* Mapping **de base de données** (ORM) (Zend_Db_Table)
* **Formulaires**, **Validations**, **Décorateurs** (Zend_Form,
Zend_Validate)
* **Layout** et **Aides de vue** (Zend_View_Helper)
* **Authentification** (Zend_Auth)
* **Session** (Zend_Session)
* **Cookie**
* Zend_Text_Figlet (Bonus ca :))

voila quelques screenshots :

<a href="{{ relativeRoot }}/wp-content/uploads/2010/03/TP1-Ecommerce-prod.png"
rel="lightbox[1175]"><img class="alignnone size-medium wp-image-1176"
title="TP1-Ecommerce prod" src="{{ relativeRoot }}/wp-
content/uploads/2010/03/TP1-Ecommerce-prod-300x168.png" alt="" width="300"
height="168" /></a> <a href="{{ relativeRoot }}/wp-content/uploads/2010/03/TP1
-Ecommerce-register.png" rel="lightbox[1175]"><img class="alignnone size-medium
wp-image-1177" title="TP1-Ecommerce register" src="{{ relativeRoot }}/wp-
content/uploads/2010/03/TP1-Ecommerce-register-300x168.png" alt="" width="300"
height="168" /></a><a href="{{ relativeRoot }}/wp-content/uploads/2010/03/TP1
-Ecommerce-panier.png" rel="lightbox[1175]"><img class="alignnone size-medium
wp-image-1178" title="TP1-Ecommerce panier" src="{{ relativeRoot }}/wp-
content/uploads/2010/03/TP1-Ecommerce-panier-300x153.png" alt="" width="300"
height="153" /></a><a href="{{ relativeRoot }}/wp-content/uploads/2010/03/TP1
-Ecommerce-paiment.png" rel="lightbox[1175]"><img class="alignnone size-medium
wp-image-1179" title="TP1-Ecommerce paiment" src="{{ relativeRoot }}/wp-
content/uploads/2010/03/TP1-Ecommerce-paiment-300x165.png" alt="" width="300"
height="165" /></a>

Et enfin [les sources à télécharger][1]

[1]: {{ relativeRoot }}/wp-content/uploads/2010/03/TP1-Ecommerce.tar.gz
