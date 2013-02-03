---
title: 'Zend : Comment utiliser un model ?'
author: Greg
layout: post
permalink: /zend/zend-comment-utiliser-un-model/
tags:
  - Zend Framework
---

On va voir comment se servir d’un `model` dans **Zend framework**. Un `model`
est en fait un objet que vous allez pouvoir utiliser. Il est bien sur possible
d’en utiliser plusieurs, et d’appeler un `model` depuis un autre `model`. Il est
possible d’utiliser dans les `models` des `models` du framework zend comme
`Zend_Registry`, `Zend_ProgressBar`, les `Zend_Service_` etc. Il est aussi
possible d’étendre une `class` du framework, comme la class `Zend_DB`. On peut
se reférer a la <a href="http://framework.zend.com/manual/fr/index.html"
target="_blank">documentation de Zend</a>.


Cependant, les `models` doivent respecter une certaine syntaxe pour le nom du
fichier, et aussi pour le nom de la `class`. Si un `model` est directement dans
la dossier `models`, le nom de fichier devra etre :<span style="text-decoration:
underline;"> Nom.php</span> et le nom de `class` devra etre <span style="text-
decoration: underline;">Nom</span>. En gros il faut que le nom de `class` et le
nom de fichier soient les memes. Par contre, si la `class` est dans un sous
dossier du dossier `model`, la il faudra que le nom du fichier soit <span style
="text-decoration: underline;">NomDeFichier.php </span>et que le nom de la
`class` soit <span style="text-decoration:
underline;">NomDeDossier_NomDeFichier</span>. En gros, il faut mettre devant le
nom de la `class` toute l’arborescense du fichier jusqu’au dossier `models`.
Enfin il y a une convention qui veut qu’on commence tous les nom de class par
des majuscules, ce n’est pas obligatoire, mais en général cela permet de s’y
retrouver plus facilement.

Un petit exemple pour comprendre comment instanciser un nouvel objet, et
l’utiliser :

Fichier directement dans le dossier models:

    class Test1 {

        private $nom;

        public function __construct($nom){
            $this->setNom($nom);
        }

        /**
         * @param $nom the $nom to set
         */
        public function setNom($nom) {
            $this->nom = $nom;
        }

        /**
         * @return the $nom
         */
        public function getNom() {
            return $this->nom;
        }
    }


Fichier dans le dossier "dossier1" qui est fans le dossier models:

    class dossier1_Test2 {

        private $nom;

        public function __construct($nom){
            $this->setNom($nom);
        }

        /**
         * @param $nom the $nom to set
         */
        public function setNom($nom) {
            $this->nom = $nom;
        }

        /**
         * @return the $nom
         */
        public function getNom() {
            return $this->nom;
        }
    }


FactureController

    /**
     * FactureController
     *
     * @author
     * @version
     */

    require_once 'Zend/Controller/Action.php';

    class FactureController extends Zend_Controller_Action {
        /**
         * The default action - show the home page
         */
        public function indexAction() {
            // TODO Auto-generated FactureController::indexAction()
            default action
        }

        public function ajouterAction(){
            $obj1 = new Test1('http://blog.lyrixx.info');
            $this->view->obj1 = $obj1;
            $obj2 = new dossier1_Test2('http://www.lyrixx.info');
            $this->view->obj2 = $obj2->getNom();
        }

        public function editerAction(){

        }

        public function supprimerAction(){

        }
    }

vue:

    /**
     * Default home page view
     *
     * @author
     * @version
     */

    $this->headTitle('Ajouter une facure');
    $this->placeholder('title')->set('Ajouter une facture');

    echo $this->obj1->getNom();
    echo '<br />';
    echo $this->obj2;

On voit bien qu’on peut passer a la vue toute sorte d’objet : un objet, ou le
résultat d’une de ses méthodes.

La suite : on avance dans le projet [le bootstrap, la connection a la base de
donnée][1].

[1]: {{ relativeRoot }}/zend/zend-framework-1-8-et-son-bootstrap.html "Tuto : Zend Framework 1.8 et son bootstrap"
