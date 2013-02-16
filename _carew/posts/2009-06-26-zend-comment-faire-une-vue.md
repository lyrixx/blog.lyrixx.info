---
title: 'Zend : Comment faire une vue ?'
author: Greg
layout: post
permalink: /zend/zend-comment-faire-une-vue.html
tags:
  - Zend Framework
---

Et bien on continue sur notre lancé avec les `vues` ou `View` dans le **Zend
Framework**. Après les [`controller`][1], maintenant les `vues`. **La vue c’est
la partie qui va s’occuper d’afficher vos résultats, vos variables, votre code
html**. Donc pour re-résumer les design pattern `MVC` :

1.  Le `controller` est appelé et exécuté
2.  Le `controller` crée et exécute des `models` (des objets)
3.  le `controller` passe à la `vue` les variables dont la vue a besoin
4.  la `vue` met en page ces variables et les donne au `layout`

Il faut savoir que par défaut il faut respecter une certaine syntaxe pour les
nom de `controller` et de `vue`. Si notre `controller` s’appelle <span style
="text-decoration: underline;">facture</span>, il faudra que dans le dossier
`script`, il y est un dossier <span style="text-decoration:
underline;">facture</span>. Puis si notre action s’appelle <span style="text-
decoration: underline;">ajouter</span>, il faudra que dans le dossier
`script`/<span style="text-decoration: underline;">facture</span>, il y est un
fichier <span style="text-decoration: underline;">ajouter.phtml</span>. Dans ce
cas la, l’application exécutera la `vues` <span style="text-decoration:
underline;">ajouter.phtml</span> dans le dossier <span style="text-decoration:
underline;">facture</span> quand l’action <span style="text-decoration:
underline;">ajouter</span> du controller <span style="text-decoration:
underline;">facture</span> s’exécutera.

Tips : Comment faire une vues avec Zend Studio 7:

<a href="{{ relativeRoot }}/wp-content/uploads/2009/06/Zend-New-View.png"
rel="lightbox[375]"><img class="size-medium wp-image-404" title="Zend-New View"
src="{{ relativeRoot }}/wp-content/uploads/2009/06/Zend-New-View-300x273.png"
alt="Comment faire une vue avec Zend Studio" width="300" height="273" /></a>

<a href="{{ relativeRoot }}/wp-content/uploads/2009/06/Capture-New-Zend-
View2.png" rel="lightbox[375]"><img class="size-medium wp-image-405" title="New
Zend View (2)" src="{{ relativeRoot }}/wp-content/uploads/2009/06/Capture-New-
Zend-View2-300x241.png" alt="Comment faire une vue avec Zend Studio" width="300"
height="241" /></a>

Enfin dans la `vue`, on aura pas besoin de mettre tout le code html de la page.
Le [`layout`][3] le fait deja. Donc on a juste besoin de mettre le code html
spécifique a cet `action` (l’`action` de ce `controller`).

## Comment on fait pour récupérer nos variable du controller dans la vue?

et bien c’est vraiment pas compliqué : dans le `controller` il suffit de
faire :

    /**
     * FactureController
     *
     * @author
     * @version
     */

    class FactureController extends Zend_Controller_Action {
        /**
         * The default action - show the home page
         */
        public function indexAction() {
            // TODO Auto-generated FactureController::indexAction()
            default action
        }

        public function ajouterAction(){
            $this->view->VariableString = "une varible";
            $this->view->VariableInt = 10;
            $this->view->VariableArray =
            array("n1"=>"a","n2"=>"b");
        }

        public function editerAction(){

        }

        public function supprimerAction(){

        }
    }

Donc on passe une variable du `controller` a la `vue` grâce a la syntaxe
suivante :

    $this->view->Un_Nom_De_Varible

Ensuite on récupère les variables dans la vue grave à la syntaxe suivante :

    $this->Un_Nom_De_Varible

Ex:

    /**
     * Default home page view
     *
     * @author
     * @version
     */

    $this->headTitle('Ajouter une facure');
    $this->placeholder('title')->set('Ajouter une facture');

    echo $this->VariableString;
    echo '<br />';
    echo $this->VariableInt;
    echo '<br />';
    echo print_r($this->VariableArray,true);

Voila. Il faut aussi savoir que les vues et les `layouts` sont étroitements
liés. Qu’on peut faire passer des variables et toutes sortes de choses entre la
`vue` et le `layout`.C’est le cas des `placeholders` que l’on peut voir au début
du fichier `ajouter.phtml`

Et il faut aussi savoir qu’on peut changer dans le `controller` la `vue` qu’on
veut appelé. C’est très pratique lorsqu’on a plein d’actions, mais qu’on a pas
besoin de `vues` différentes a chaque `action`.

<a href="{{ relativeRoot }}/wp-content/uploads/2009/06/Zend-vues.png"
rel="lightbox[375]"><img class="size-medium wp-image-406" title="Zend : vues"
src="{{ relativeRoot }}/wp-content/uploads/2009/06/Zend-vues-300x187.png"
alt="Zend : vues" width="300" height="187" /></a>

La suite : [Comment utiliser un model avec Zend framework][4]

[1]: {{ relativeRoot }}/zend/zend-quest-ce-quun-controller.html
[3]: {{ relativeRoot }}/zend/zend-comment-utiliser-un-layout.html
[4]: {{ relativeRoot }}/zend/zend-comment-utiliser-un-model.html
