---
title: Gerer l'authentification avec Zend_Auth du Zend Framework
author: Greg
layout: post
permalink: /zend/gerer-lauthentification-avec-zend_auth-du-zend-framework.html
tags:
  - Zend Framework
---

**Comment faire un système de login sur son application** ? On va voir comment
mettre en place un système de connexion d’utilisateur dans son application avec
le **Zend Framework**. Pour ce faire on va utiliser le composant **Zend_Auth**.
Il va falloir commencer par faire le **formulaire de login** qui n’est pas très
compliqué. Faire la validation de ce formulaire puis si il est valide s’occuper
de la partie **Zend_Auth ou authentification**. Enfin nous verrons comment faire
un petit **Zend_View_Helper** (aide de vue) pour créer automatiquement des liens
(**« login» ou « logout »**)

## Sommaire : {#sommaire}

* [Sommaire][1]
* [Mise en place du formulaire][2]
* [Le controller][3]
* [Déconnexion][4]
* [Vérification][5]
* [Le controller de vue][6]

## Création du formulaire de login : {#formulaire}

On va allé assez vite sur la création du formulaire dans la mesure ou j’explique
dans un autre tutoriel [**comment faire un formulaire**][7].

    class Model_Form_User_Login extends Zend_Form {

    public function init(){
        $this->setName('add_user');

        $email = new Model_Form_EText('email','Adresse Email : ');

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Mot de pase : ')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton')
            ->setLabel('Se connecter');

        $elements = array($email,$password, $submit);
        $this->addElements($elements);

        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            array('Errors', array('placement' => 'apend')),
            'Form'
        ));
    }

}

On voit qu’on utilise un champ `email` et un champ `password` et bien sur un
bouton valider. Si la classe de l’email vous parait bizarre je vous conseil de
lire le tutoriel sur les formulaires. Enfin les dernières lignes concernent la
mise en forme du formulaire ainsi que la position des messages d’erreurs qui
seront envoyés (ajoutés) par **le modèle pour la vérification du couple Email /
Password**.

## Le controller {#controller}

Le `controller` est dans la classe `LoginController` et l’action que nous allons
utiliser est `loginAction`. On commence par donner une instance de notre
formulaire à la vue pour qu’elle puisse l’afficher.

    $form = new
    Model_Form_User_Login();
    $this->view->formLogin = $form;

Ensuite on vérifie qu’il y ai des données postées, on les récupère
et on les valide.

    if ($this->_request->isPost ()) {
        $formData = $this->_request->getPost ();
        if ($form->isValid ( $formData )) {
        }
    }

Si elles sont valides, on récupère les données :

    $email = $form->getValue ( 'email' );
    $password = $form->getValue ( 'password' );

Enfin on arrive à la **partie qui concerne Zend_Auth**. Il faut commencer par
créer ce qu’on appel un `Zend_Auth_Adapter_DbTable`, c’est un composant de
`Zend_Auth` qui va pouvoir se connecter à la `BDD`. Cet nouvel objet va prendre
en paramètre un `Zend_Db_Adapter_Abstract`, c’est à dire un connecteur à la base
de donnée. Dans notre cas nous allons prendre le connecteur principal, celui qui
est définit dans les paramètres globaux de Zend (application.ini)

    $authAdapter = new Zend_Auth_Adapter_DbTable ( Zend_Db_Table::getDefaultAdapter () );

Enfin il faut donner quelques informations à cet adaptateur :

* Le nom de la table qui contient les utilisateurs (ici users)
* Le nom de la colonne qui contient les identifiants (ici email)
* Le nom de la colonne qui contient les mots de passe (ici password)
* Le type de hashage dans la base (ici MD5)
* La valeur de l’email
* La valeur du mot de passe

&nbsp;

    $authAdapter->setTableName ( 'users' )
        ->setIdentityColumn ( 'email' )
        ->setCredentialColumn ( 'password' )
        ->setCredentialTreatment ( 'MD5(?)' )
        ->setIdentity ( $email )
        ->setCredential ( $password );

Enfin on essaye d’identifier l’utilisateur :

    $authAuthenticate = $authAdapter->authenticate ();

Maintenant il faut regarder si cette authentification à réussi ou échoué.

    if ($authAuthenticate->isValid ()) {
    }

Si elle est valide, on va mettre en **variable de session les informations de
l’utilisateur**. À ce moment vous pouvez mettre toutes les informations
relatives à l’utilisateur. On peu même envisager de créer une classe pour
stocker toutes ces informations. Dans le cas présent, seule les informations
issu de la table users sont utiles. On commence donc par **récupérer l’espace de
stockage (storage) par défaut de l’application**

    $storage = Zend_Auth::getInstance ()->getStorage ();

Puis on y ajoute les informations de l’utilisateur, on y enlève bien sur le mot
de passe :

    write ( $authAdapter->getResultRowObject ( null, 'password' ) );

Et enfin on redirige l’utilisateur sur la page principale de l’application

    $this->_helper->redirector ( 'index', 'index' );

Et pour finir si le couple login / password n’était pas bon, on ajoute
une description au formulaire :

    } else {
        $form->addError ( 'Il n'existe pas d'utilisateur avec ce mot de passe' );
    }

Voilà l’action du controller est faite on peu passer à la suite.

## Déconnexion {#deconnexion}

Si l’utilisateur veut se déconnecter, il doit cliquer sur un lien qui map
l’action suivante :

    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity ();
        $this->_helper->redirector ( 'index', 'index' );
    }


Grâce à la ligne `Zend_Auth::getInstance ()->clearIdentity ();` on supprime
l’identification de l’utilisateur.

## Vérification de connexion {#verif}

Il faut bien entendu vérifier (si l’on code proprement) si l’utilisateur est
déjà connecté si il veut se déconnecter, et il faut aussi vérifier que
l’utilisateur connecté puisse seulement se déconnecter dans **ce** controller.
On va utiliser la méthode `preDispatch` du controller, cette méthode est
systématiquement appelé à l’exécution d’une action du controller. Je vous donne
le code et je l’explique après :

    public function preDispatch() {
        if (Zend_Auth::getInstance ()->hasIdentity ()) {
            if ('logout' != $this->getRequest ()->getActionName ()) {
                $this->_helper->redirector ( 'index', 'index' );
            }
        } else {
            if ('logout' == $this->getRequest ()->getActionName ()) {
                $this->_helper->redirector ( 'index' );
            }
        }
    }

On commence par vérifier si l’utilisateur est connecté, si il l’est et que
l’action sur laquelle il veut aller est différente de `‘logout’` on le renvoi
sur la page d’accueil du site. Sinon, si il n’est pas connecté et qu’il veut se
déconnecter, on l’envoi sur l’action qui permet de se connecter.

## Zend_View_Helper {#zend-view-helper}

**Comment faire automatiquement un lien qui permet de se connecter si on ne
l’est pas, ou alors de se deconnecter si on l’est ?** Et bien on utilise ce
qu’on appel un **Zend_View_Helper**, c’est un bout de code qui va être
utilisable dans le layout.

    class Zend_View_Helper_ProfileLink extends Zend_View_Helper_Abstract {
        public function profileLink() {
            $helperUrl = new Zend_View_Helper_Url ( );
            $auth = Zend_Auth::getInstance ();
            if ($auth->hasIdentity ()) {
                $username = $auth->getIdentity ()->prenom . ' ' . strtoupper ( substr ( $auth->getIdentity ()->nom, 0, 1 ) ) . '.';
                $logoutLink = $helperUrl->url ( array ('action' => 'logout', 'controller' => 'login' ) );

                return 'Salut ' . $username . ' (<a href="' . $logoutLink . '">Logout</a>)';
            }
            $loginLink = $helperUrl->url ( array ('action' => 'login', 'controller' => 'login' ) );

            return '<a href="' . $loginLink . '">Login</a>';
        }
    }

On commence par créer un nouveau `Zend_View_Helper_Url` pour nous aider à
construire les URL, puis on récupère une instance de Zend_Auth. Donc si il y a
un utilisateur de connecté, on récupère son identité. Puis on construit l’URL de
déconnexion, et enfin on retourne la chaine de caractère (message + lien). Si
l’utilisateur n’est pas connecté, on construit le lien de connexion, et on
retourne la chaine de caractère. Si on veut utiliser ce script dans la vue, il
va falloir faire :

    echo $this->profileLink();

Et voila le code du controller en entier :

    class LoginController extends Zend_Controller_Action {

        public function init() {
            /** Initialize action controller here */
            Zend_Auth::getInstance ()->clearIdentity ();

        }

        public function preDispatch() {
            if (Zend_Auth::getInstance ()->hasIdentity ()) {
                if ('logout' != $this->getRequest ()->getActionName ()) {
                    $this->_helper->redirector ( 'index','index' );
                }
            } else {
                if ('logout' == $this->getRequest()->getActionName ()) {
                    $this->_helper->redirector ( 'index' );
                }
            }
        }

        public function indexAction() {
            $this->_forward ( 'login' );
        }

        public function loginAction() {

            $form = new Model_Form_User_Login ( );
            $this->view->formLogin = $form;

            if ($this->_request->isPost ()) {
                $formData = $this->_request->getPost ();
                if ($form->isValid ( $formData )) {
                    $email = $form->getValue ( 'email' );
                    $password = $form->getValue ( 'password' );
                    $authAdapter = new Zend_Auth_Adapter_DbTable ( Zend_Db_Table::getDefaultAdapter () );
                    $authAdapter->setTableName ( 'users' )
                        ->setIdentityColumn ( 'email' )
                        ->setCredentialColumn ( 'password' )
                        ->setCredentialTreatment ( 'MD5(?)' )
                        ->setIdentity ( $email )
                        ->setCredential ( $password );
                    $authAuthenticate = $authAdapter->authenticate ();
                    if ($authAuthenticate->isValid ()) {
                        $storage = Zend_Auth::getInstance ()->getStorage ();
                        $storage->write ( $authAdapter->getResultRowObject ( null, 'password' ) );
                        $this->_helper->redirector ( 'index', 'index' );
                    } else {
                        $form->addError ( 'Il n'existe pas d'utilisateur avec ce mot de passe' );
                    }
                }
            }
            $this->render ( 'index' );
        }

        public function logoutAction() {
            Zend_Auth::getInstance ()->clearIdentity ();
            $this->_helper->redirector ( 'index', 'index' );
        }

    }

 [1]: #sommaire
 [2]: #formulaire
 [3]: #controller
 [4]: #deconnexion
 [5]: #verif
 [6]: #zend-view-helper
 [7]: {{ relativeRoot }}/zend/zend-framework-formulaire-et-base-de-donnee-partie-1.html#formulaire "Zend Framework, Formulaire et Base de donnée, partie 1"
