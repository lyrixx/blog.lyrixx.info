---
title: Zend : Qu'est ce qu'un controller ?
author: Greg
layout: post
permalink: /zend/zend-quest-ce-quun-controller/
tags:
  - Zend Framework
---
## Comment utiliser les controllers dans Zend ?

En fait, le **controller** du **Zend Framework** c’est l’outil qui va
complètement contrôler l’application, c’est a dire que c’est lui qui va
**s’occuper de lancer les calculs de récupérer les résultats et de les envoyer à
la vue**.

Donc par exemple si mon application est a l’adresse suivante :
`http://www.lyrixx.info/` et que je vais à l’adresse suivante :
`http://www.lyrixx.info/controller/action/` j’exécuterais le code du controller
« `controller` » et plus précissement la méthode (on dit Action) « `action`« .
Par défaut si on ne précisse pas de controller, ni d’action c’est le controller
`index` et l’action `index` qui sera appelé. Donc l’adresse
`http://www.lyrixx.info/` est équivalente a `http://www.lyrixx.info/index/` et
est aussi équivalente à `http://www.lyrixx.info/index/index/`. Par exemple le
controller par default ressemble a ca :

    <?php

    /**
     * IndexController - The default controller class
     *
     * @author
     * @version
     */

    class IndexController extends Zend_Controller_Action
    {
        /**
         * The default action - show the home page
         */
        public function indexAction()
        {
        // TODO Auto-generated IndexController::indexAction() action
        }
    }

De plus les controllers doivent respecter une certaines terminologies :

* Ils commencent par une majuscule
* Finissent par « `Controller.php`« 

De même pour les actions :

* Elles commencent par une minuscules
* Finissent par « `Action`« 

Donc maintenant il va falloir définir les controllers. La c’est un peu une
question de gout. C’est a travers les controllers qu’on va definir les urls de
notre application (bien qu’il soit quand meme possible de les changer). Il faut
qu’il y ai un bon equilibre entre le nombre de controller et le nombre d’action
dans chaque controller.

Donc notre cas on a environ 5 types pages :

* Ajouter / Editer / Supprimer une facture
* Ajouter / Editer / Supprimer un utilisateur
* Voir la liste des Facture
* Calculer les sommes dues
* Voir les statistiques

On va donc faire 5 controllers :

* `Facture` pour Ajouter / Editer / Supprimer une facture
* `User` pour Ajouter / Editer / Supprimer un utilisateur
* `Factures` pour Voir la liste des Facture
* `Calcul` pour Calculer les sommes dues
* `Stats` pour Voir les statistiques

Et dans chaque controller on aura différente action. Par exemple pour le
controller facture on aura comme action :

* ajouter
* editer
* supprimer

** Truc et Astuces**

on peut ajouter facilement un controller dans zend studio (que je vous
recommende fortement). Il suffit de faire CTRL+N puis de choisir Zend Controller
:

<a href="{{ relativeRoot }}/wp-content/uploads/2009/06/New_Controller.png">
  <img title="New_Controller" src="{{ relativeRoot }}/wp-content/uploads/2009/06/New_Controller-274x300.png"
  alt="Zend :Ajouter un nouveau controller" width="274" height="300" />
</a>

Voila a quoi ressemble notre controller `facture`

Biensur, pour l’instant les actions sont vides. Il va falloir les « remplir » de
code, mais on verra ca par la suite ! Maintenant que notre premier controller
est fait, on peut essayé d’aller dessus. Mais il y a une exception qui est
lancé. C’est normale, c’est qu’il n’y a pas encore de vue associé a ce
controller. Il faut pour ça créer une vue.

<a href="{{ relativeRoot }}/wp-content/uploads/2009/06/Zend-pas-de-view.png" rel="lightbox[340]">
  <img class="size-medium wp-image-373" title="Zend pas de view" src="{{ relativeRoot }}/wp-content/uploads/2009/06/Zend-pas-de-view-300x175.png" alt="Zend : Erruer : pas de vue" width="300" height="175" />
</a>

On peut noter l’url pour acceder a cette action : `…/facture/ajouter/`

Voila maintenant on a plus qu’a [faire une vue pour ce controller][1].

[1]: {{ relativeRoot }}/zend/zend-comment-faire-une-vue.html "Zend : Comment faire une vue"
