---
title: Zend Framework, Formulaire et Base de donnée, partie 1
author: Greg
layout: post
permalink: /zend/zend-framework-formulaire-et-base-de-donnee-partie-1/
tags:
  - Zend Framework
---

Hello, On va voir aujourd’hui **comment construire et gérer un formulaire**,
**ajouter les données récupéré dans une base de données**, **afficher ces
données**, et enfin **modifier** et **supprimer** celles-ci. En gros, on va
utiliser les propriétés **<acronym title="Create Read Update
Delete">CRUD</acronym>** de **Zend Framework** en y associant une **base de
donnée** et un **formulaire**. P.S. : Pendant la rédaction de ce **tuto**, je me
suis rendu compte qu’il allait être beaucoup trop gros, du coup j’ai décidé de
**le couper en deux parties**, une première sur la création du formulaire et
l’ajout en base, une deuxième sur la l’affichage, la modification et la
suppression.

## Sommaire {#sommaire}

* Partie 1
    * [Sommaire][1]
    * [Préparation de la BDD][2]
    * [Préparation du formulaire][3]
    * [Affichage du formulaire][4]
    * [Le Controller du formulaire][5]
    * [Gérer d’autres validateurs][6]
    * [Conclusion][7]
* Partie 2
    * [Sommaire][8]
    * [Afficher des enregistrement][9]
    * [Mettre a jour des enregistrement][10]
    * [Supprimer des enregistrement][11]
    * [Conclusion][12]

## Préparation de la base de donnée : {#bdd}

<p style="text-align: center;">   <a href="{{ relativeRoot }}/wp-
content/uploads/2009/11/database.png"   rel="lightbox[929]"><img
class="aligncenter   size-full wp-image-931" title="database user" src="{{
relativeRoot   }}/wp-content/uploads/2009/11/database.png"   alt="database user"
width="136" height="178" /></a> </p>

    CREATE TABLE IF NOT
    EXISTS `Budget`.`users` (
    *idUser` INT NOT NULL AUTO_INCREMENT ,
    *nom` VARCHAR(20) NULL ,
    *prenom` VARCHAR(20) NULL ,
    *email` VARCHAR(30) NULL ,
    *active` INT NULL ,
    *level` INT NULL ,
    *password` BIGINT NOT NULL ,
    PRIMARY KEY (`idUser`) )
    ENGINE = InnoDB
    DEFAULT CHARACTER SET = utf8
    PACK_KEYS = DEFAULT

Comme [on a vu dans un précédant tutoriel][13], il faut créer une **classe qui
va nous mapper les informations de la table** (**<acronym title="Object Relation
Mapper">ORM</acronym>** du `Zend Framework`).

    class Model_DbTable_Users extends Zend_Db_Table_Abstract {
        protected $_name = 'users';
        protected $_primary = array('idUser');
    }

## Préparation du formulaire {#formulaire}

Il existe plusieurs façons de faire un formulaire : soit en utilisant des
`tableaux php`, des `objets php`, un `fichier xml` etc… Pour ma part j’utilise
des `objets php` et j’implémente pour chaque formulaire la classe **Zend_Form**
(je vais y revenir). Pour plus de clarté dans mon arborescence de fichiers, je
place tous mes formulaires, dans le dossier : `application/models/Form/` et
comme ce formulaire ci concerne l’`ajout et/ou la modification d’un
utilisateur`, mon fichier `User.php` est dans le dossier :
`application/models/Form/User`. Donc le fichier de base doit ressembler à ça :

    class Model_Form_User_User extends Zend_Form {
        public function init() {
        }
    }

Comme on peut le voir, il faut étendre la classe `Zend_Form`, et mettre tout
notre code qui ajoute des éléments dans la méthode `init()`. Ce code sera
**directement exécuter lors de la création d’un nouveau formulaire**. Mais on va
très vite ajouter des éléments à notre formulaire, sinon il ne va pas servir à
grand chose. On peut, par exemple, **ajouter un champ input texte** grâce a
ce code (le principe reste le même pour tous les types d’éléments) :

    $champText = new Zend_Form_Element_Text('champText');
    $champText->setLabel('un champ texte')
        ->setRequired(true)
        ->addValidator('notEmpty')
        ->addFilter('StripTags')
        ->addFilter('StringTrim');

On commence par créer un nouvel élément de type input texte, puis on ajoute un
`label`, on ajoute ensuite un `validateur` qui (dans ce cas) oblige la valeur à
être rempli, puis on ajoute deux `filtres`. Il existe un grand nombre de
`validateurs` et de `filtres`. On en verra quelques un ici. Mais comme on peut
se douter, on va très souvent répéter le même code. Donc on peut se **créer une
petite classe** qui ne va servir qu’a ajouter un champ de type input texte :
(`application/models/Form/EText.php`)

    class Model_Form_EText extends Zend_Form_Element_Text {

        public function __construct($options = null,$label) {
            parent::__construct($options);
            $this->setLabel($label)
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        }
    }

**Voilà, je vous donne maintenant une partie du code du formulaire et j’explique
les points nouveaux.**

    class Model_Form_User_User extends Zend_Form {

        public function init() {
            $this->setName ( 'add_user' );

            $id = new Zend_Form_Element_Hidden ( 'idUser' );

            $nom = new Model_Form_EText ( 'nom', 'form_user_add_name' );

            $prenom = new Model_Form_EText ( 'prenom', 'form_user_add_firstname' );

            $email = new Model_Form_EText ( 'email', 'form_user_add_mail' );
            $email->addValidator ( 'EmailAddress' )->addValidator( new Zend_Validate_Db_NoRecordExists ('users', 'email' ) );

            $password = new Zend_Form_Element_Password ( 'password' );
            $password->setLabel ( 'form_user_add_password' )->addFilter ( 'StripTags' )->addFilter ( 'StringTrim' )->setRequired ( true );

            $password2 = new Zend_Form_Element_Password ( 'password2' );
            $password2->setLabel ( 'form_user_add_password2' )->addFilter ( 'StripTags' )->addFilter ( 'StringTrim' );

            $active = new Zend_Form_Element_Checkbox ( 'active' );
            $active->setLabel ( 'form_user_add_enable' )->addFilter ( 'StripTags' )->addFilter ( 'StringTrim' )->setValue ( 1 );

            $level = new Zend_Form_Element_Select ( 'level' );
            $level->setLabel ( 'form_user_add_level' )->addFilter ( 'StripTags' )->addFilter ( 'StringTrim' );

            $levelOptions = array ();
            for($i = 0; $i <= 9; $i ++) {
                $levelOptions [$i] = array ('key' => $i, 'value' => $i );
            }
            $level->addMultiOptions ( $levelOptions );

            $submit = new Zend_Form_Element_Submit ( 'submit' );
            $submit->setAttrib ( 'id', 'submitbutton' )->setLabel ( 'form_user_add_submit' );

            $elements = array ($id, $nom, $prenom, $email, $password, $password2, $active, $level, $submit );
            $this->addElements ( $elements );
        }
    }

Pour commencer je donne un `nom` a mon formulaire, ce qui peut être pratique
pour le retrouver, en effet le nom du formulaire correspond a son `id` dans le
`code html et css`. On peut aussi ajouter d’autre options, comme l’`action du
controller`, mais dans notre cas, le formulaire et sa page de destination
(`l’action`) sont les mêmes, donc on pas besoin de le définir. Ensuite, comme on
peut le voir, on crée plusieurs éléments :

* l’`id` qui est un `élément de type hidden`, qui nous servira lors des
mises à jour d’un utilisateur.
* `nom`, `prenom` qui sont des éléments de type `Etext`, les éléments
qu’on a crée un peu plus haut.
* `email`
    * Le champ `email` est aussi un champs de type `Etext`,
    * On a ajouter un `validateur d’adresse mail`, pratique, le boulot est
        déjà fait ! `->addValidator('EmailAddress')`
    * On ajoute un second `validateur` qui vérifie que l’adresse mail n’est
        pas déjà dans la base de donnée:  *addValidator (new Zend_Validate_Db_NoRecordExists('users','email'))`.
        Le premier paramètre est le `nom de la table`. Le second est
        l’attribut qui doit être unique.
* `password` et `password2` sont des `champs de type password`.
* `active` sert a savoir si l’utilisateur est actif. C’est un élément `de type Checkbox`,
    avec comme valeurpar défaut 1 (case coché)
* `level` sert a niveau d’administration du site. `De type Select` On ajoute
    dans un tableau des correspondances `key => value` qui représentent les
    *options du select`

* `submit` qui est notre bouton envoyer.

**Les **labels** ne sont pas très significatifs ou user-friendly, c’est normale,
j’utilise la traduction du zend framework**. Enfin il ne reste plus qu’a ajouter
tous ces éléments dans le formulaire lui même et le tours est joué, on a notre
formulaire. Je tiens a rappelé qu’**il existe vraiment un grand nombre de façon
de fabriquer un formulaire**, **d’ajouter des filtres**, **des validateurs**. Il
y a quelques exemples ici, mais je ne peux pas faire un exemple pour chaque cas.
Je vous recommande donc d’aller faire un tour sur sur le doc du **zf**.

## Affichage du formulaire {#affichage}

**Je ne vais pas m’occuper ici de styler le formulaire**. Juste de l’afficher
comme il vient. Par défaut **zf** utilise ce qu’on appel des **décorateurs**, il
est possible de les personnaliser, mais ce n’est pas le but ici. Vous pouvez
aller faire un tours [sur le site de dator pour avoir un exemple][14] de ce
qu’on peut faire (même si je ne suis pas fan de sa technique, mais elle reste
valide à 100% … huhu). Pour les `décorateurs`, le code a `ajouter / modifier`
doit se trouver dans la classe qui fabrique notre formulaire. Donc il va falloir
**éditer la vu qui affichera le formulaire.** Chez moi c’est
`application/modules/Frontend/views/scripts/user/index.phtml` car c’est le
**controller** `userController.php` qui va être appelé ici. Voilà a quoi doit
ressembler la vues **au minimum** :

    <?php if (isset($this->formUser)) : ?>
        <h2>
            <?php echo $this->translate('view_user_add')?> ?
        </h2>
        <?php echo $this->formUser ?>
    <?php endif ?>

On fait une simple vérification pour voir sur le formulaire a bien était envoyé
par le `controller` à la vue et on l’affiche.

## Le controller du formulaire {#controller}

Bon on arrive la **gestion du controller** de notre formulaire. Je vous **livre
le code et je l’explique ensuite** :
(`application/modules/Frontend/controllers/UserController.php`)

    class UserController extends Zend_Controller_Action
    {
        public function indexAction(){
        $form = new Model_Form_User_User();
        $this->view->formUser = $form;
        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if ($form->isValid($formData)) {
                $users = new Model_DbTable_Users();
                $row = $users->createRow();
                $row->nom = $form->getValue('nom');
                $row->prenom = $form->getValue('prenom');
                $row->email = $form->getValue('email');
                $row->password = md5($form->getValue('password'));
                $row->active = $form->getValue('active');
                $row->level = $form->getValue('level');
                $result = $row->save();
                //On gere le resultat et l'action qui s'en suit.
                $form->reset();
            }
        }
    }


Petit apercu avant de continuer

<p style="text-align: center;">   <a href="{{ relativeRoot }}/wp-
content/uploads/2009/11/Form-User-Add.png"   rel="lightbox[929]"><img
class="aligncenter size-full wp-image-938" style="border: 1px solid black;"
title="Form User Add" src="{{   relativeRoot }}/wp-content/uploads/2009/11/Form-
User-Add.png" alt="Form   User Add" width="370" height="571" /></a> </p>

    $form = new
    Model_Form_User_User();
    $this>view->formUser = $form;

On commence par `instancier` la classe du formulaire et on le donne à la vue. A
partir de ce moment on peut **déjà tester si notre formulaire s’affiche bien**.
Bien entendu ça ne sert a rien de cliquer sur envoyer, ça ne fonctionnera pas !
Comme on a pu le voir plus haut, la page de destination du formulaire est elle
même. Donc le `controller` est le même. C’est donc dans la même `méthode` du
même `controller` qu’on teste en premier si notre **formulaire est valide** et
qu’ensuite on traite le résultat.

    if ($this->_request->isPost()) {
         $formData = $this->_request->getPost();
    }

On commence avec le `IF` pour voir si il y a eu des données de type `POST`,
`POST` étant le type d’envoi par défaut pour les formulaires. On peut bien
entendu envoyer notre formulaire en `GET` en modifiant la classe `user.php`.
Ensuite on récupère nos données.

    if ($form->isValid($formData)) {
    }

Ce point est très intéressant, car il va **automatiquement voir si notre
formulaire est valide**. C’est a dire qu’il va exécuter chaque `validateurs` de
nos `élement` composant le formulaire. Si il y a **au moins une erreur la
validation ne passera pas**, mais zf va** automatiquement refaire notre
formulaire en reprenant les valeurs insérer, et en ajouter un message ou il y a
eu des erreurs** (par exemple « la valeur est requise »). Ici encore on peut
personnaliser le message. Dans mon cas j’utilise encore une fois la
`traduction`. Bon et si le formulaire est valide ?

    $users = new Model_DbTable_Users();
    $row = $users->createRow();
    $row->nom = $form->getValue('nom');
    $row->prenom = $form->getValue('prenom');
    $row->email = $form->getValue('email');
    $row->password = md5($form->getValue('password'));
    $row->active = $form->getValue('active');
    $row->level = $form->getValue('level');
    $result = $row->save();

Comme on veut (à la base) que notre formulaire nous serve à **ajouter des
utilisateurs en base**, et bien on reprend le même code que dans le tutoriel sur
la `gestion des BDD` ; On instancie la classe `Model_DbTable_Users`, **on crée
un nouvelle ligne, on ajoute tous nos champs**, et enfin **on sauvegarde**. Bien
entendu, on peut mettre la ligne `$row->save()` dans un bloque `try-catch`, mais
si on a bien fait notre boulot sur les `validateurs`, normalement, il n’y a pas
besoin.

    reset();

Enfin on **remet a zéro notre formulaire,** c’est a dire qu’on vide tout les
champs. On peut par la suite ajouter un message (pour l’ergonomie) qui
s’affichera expliquant que l’ajout s’est bien effectué. Ici c’est donc juste un
passage de variable à la vue. Voilà notre formulaire fonctionne mais il y reste
encore des choses a voir.

## Double vérification du password ; Validation avec données. {#validateur}

Du fait qu’il faille vérifier que l’utilisateur a bien rentré **deux fois le
même password**, on va avoir besoin d’au moins un des deux `password`. On va
donc redéfinir la méthode `isValid` de la classe `Model_Form_User_User` qui
étend `Zend_Form` je vous rappel. Cette classe, comme on a vu plus haut, **est
systématiquement appelé**, et elle appel à son tour tous les `validateurs` de
tous les `éléments`. On va utiliser pour vérifier que l’utilisateur a bien
rentré deux fois le même `password` grâce une classe de validation réalisé par
[l’ami dator][15] :

    class App_Validate_PasswordMatch extends Zend_Validate_Abstract
    {
        const PASSWORD_MISMATCH = 'passwordMismatch';
        protected $_compare;
        protected $_messageTemplates = array(
            self::PASSWORD_MISMATCH => "PASSWORD_MISMATCH"
        );
        public function __construct($compare){
            $this->_compare = $compare;
        }
        public function isValid($value){
            $this->_setValue((string) $value);
            if ($value !== $this->_compare) {
                $this->_error(self::PASSWORD_MISMATCH);
                return false;
            }
            return true;
        }
    }

On peut la mettre en bas de la classe `Model_Form_User_User`, ça ne pose pas
de problème. Du coup il ne nous reste plus qu’a l’utiliser :

    public function isValid($data)
    {
        $this->getElement('password')->addValidator(new
        App_Validate_PasswordMatch($data['password2']));
        if ($this->getElement('email')->getValue() == $data['email']){
            $this->getElement('email')->removeValidator ( "Zend_Validate_Db_NoRecordExists");
        }
        return parent::isValid($data);
    }

On commence par récupérer l’élément `password`, auquel on ajoute notre
`validateur personnalisé` en lui donnée comme paramètre le `password2` rentré
par l’utilisateur. Ensuite on appelle la méthode « classique ».

## Conclusion {#conclusion}

Et voilà, on a finit avec notre formulaire. On peut bien sur l’améliorer et
surtout le rendre plus joli. Je vous redonnerai tous les fichiers nécessaires
lors de la deuxième ;) et on peut continuer avec[ la 2eme partie : affichage,
modification et suppression][16].

 [1]: #sommaire
[2]: #bdd
[3]: #formulaire
[4]: #affichage
[5]: #controller
[6]: #validateur
[7]: #conclusion
[8]: {{ relativeRoot }}/zend/zend-framework-formulaire-et-base-de-donnee-partie-2.html#sommaire
[9]: {{ relativeRoot }}/zend/zend-framework-formulaire-et-base-de-donnee-partie-2.html#read
[10]: {{ relativeRoot }}/zend/zend-framework-formulaire-et-base-de-donnee-partie-2.html#update
[11]: {{ relativeRoot }}/zend/zend-framework-formulaire-et-base-de-donnee-partie-2.html#delete
[12]: {{ relativeRoot }}/zend/zend-framework-formulaire-et-base-de-donnee-partie-2.html#conclusion
[13]: {{ relativeRoot }}/zend/comment-gerer-une-base-de-donnee-avec-le-zend-framework.html "Comment gérer une base de donnée avec le Zend Framework"
[14]: http://www.dator.fr/tutorial-creer-une-application-avec-le-zend-framework-%E2%80%93-8-le-formulaire-dinscription-de-watchmydesk/
[15]: http://pastie.textmate.org/640447
[16]: {{ relativeRoot }}/zend/zend-framework-formulaire-et-base-de-donnee-partie-2.html "Zend Framework, Formulaire et Base de donnée, partie 2"
