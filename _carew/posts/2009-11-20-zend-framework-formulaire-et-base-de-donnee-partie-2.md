---
title: Zend Framework, Formulaire et Base de donnée, partie 2
author: Greg
layout: post
permalink: /zend/zend-framework-formulaire-et-base-de-donnee-partie-2.html
tags:
  - Zend Framework
---

On continue avec la deuxième partie de la [gestion d’un formulaire][1], de **la
création de formulaire, et d’ajout de donnée en BDD** dans le **Zend
Framework**. Dans cette partie nous verrons **comment afficher les données, les
modifier à l’aide du même formulaire que pour l’ajout, et la suppression des
données**.

## Sommaire {#sommaire}

* Partie 1
    * [Sommaire][2]
    * [Préparation de la BDD][3]
    * [Préparation du formulaire][4]
    * [Affichage du formulaire][5]
    * [Le Controller du formulaire][6]
    * [Gérer d’autres validateurs][7]
    * <a href={{ relativeRoot
    }}/zend/zend-framework-formulaire-et-base-de-donnee-partie-1/"#conclusion">Conclusion</a>
* Partie 2
    * [Sommaire][8]
    * [Afficher des enregistrement][9]
    * [Mettre a jour des enregistrement][10]
    * [Supprimer des enregistrement][11]
    * [Conclusion][12]

## Afficher les données de la base de données. {#read}

Il faut tout d’abord toucher a notre `controller` qui gère la page `user` pour
donner à la vue une instance de la classe >`Model_DbTable_Users()`. Vu que
j’utilise souvent cette classe, j’ai fait une méthode dans la classe
`controller` : (`application/modules/Frontend/controllers/UserController.php`)

    private function getUsersAll() {
        $dbUser = new Model_DbTable_Users();
        return $dbUser->fetchAll()->toArray();
    }

Et maintenant on a juste a passer cette classe à la vue :

    public function indexAction(){
        $this->view->usersAll = $this->getUsersAll();
    }

On passe maintenant à la vue
(`application/modules/Frontend/views/scripts/user/index.phtml`).

    if (isset($this->usersAll)): ?>
        <h2>
            <?php echo $this->translate('view_user_modify')?>:
        </h2>
        <table>
            <tr>
            <th><?php echo $this->translate('view_user_firstname')?></th>
            <th><?php echo $this->translate('view_user_name')?></th>
            <th><?php echo $this->translate('view_user_email')?></th>
            <th><?php echo $this->translate('view_user_modify')?></th>

            </tr>
            <?php foreach ($this->usersAll as $user){
                if ($user['active'] == 1){
                    echo '<tr>';
                }else{
                    echo '<tr style="text-decoration:line-through">';
                }
                echo '<td>'.$user['nom'].'</td>';
                echo '<td>'.$user['prenom'].'</td>';
                echo '<td>'.$user['email'].'</td>';

                echo '<td><a
                href="'.$this->url(array('action'=>'edit','id'=>$user['idUser'])).'">Modifier</a>
                | <a
                href="'.$this->url(array('action'=>'del','id'=>$user['idUser'])).'"
                >Supprimer</a></td>';
                echo '</tr>';
            }
            ?>
        </table>
    <?php endif; ?>

<p style="text-align: center;">   <a href="{{ relativeRoot }}/wp-
content/uploads/2009/11/liste-users.png"   rel="lightbox[947]"><img
class="aligncenter   size-full wp-image-948" title="liste users" src="{{
relativeRoot   }}/wp-content/uploads/2009/11/liste-users.png"   alt="liste
users" width="675" height="220" /></a> </p>

On commence par vérifier que la variable `usersAll` est bien définit. Dans ce
cas la on construit notre tableau assez naturellement. Encore une fois j’utilise
ici la [traduction du framework][13] (mais on n’est pas forcé de le faire).

    if ($user['active'] == 1) {
        echo '<tr>';
    } else {
        echo '<tr style="text-decoration:line-through">';
    }

Ce code sert juste a vérifié que l’utilisateur est actif. Si il ne l’est pas on
barre la ligne :

    <a href="<?php echo $this->url(array('action'=>'edit','id'=>$user['idUser'])) ?>">
        Modifier
    </a>
    |
    <a href="<?php echo $this->url(array('action'=>'del','id'=>$user['idUser'])) ?>">
        Supprimer
    </a>

Enfin cette ligne est assez sympa : en effet on veut pouvoir **éditer et
supprimer des utilisateurs**. Il nous faut donc des **liens** pour le faire. Et
bien pour fabriquer ces liens on va utiliser une **aide de vues** (View Helper :
`Zend_View_Helper_Url`) qui va nous construire notre lien directement. On lui
passe comme argument un tableau, avec comme clés l'`action`, le `controller`, le
`module` et des `paramètres`. Tout ces champs ne sont pas obligatoires. Dans mon
cas, je reste sur le même `controller`, et donc le même `module`. Donc je
n’utilise que l’`action` et un `paramètre id`. Ce paramètre est passé en `GET`
donc directement dans l’`url`. De plus `zend` va gérer automatiquement l’**url
rewriting**, qui est beaucoup plus **user-friendly et seo-friendly**. Voyons ce
que donne le résultat : (il y a un peu de `css`)

## Modification d’un enregistrement {#update}

Donc notre lien pour modifier un utilisateur est déjà fait, et comme on a pu le
voir, il pointe sur l’`action edit` du `controller UserController.php`. Il va
donc falloir modifier celui-ci, ainsi que la classe qui gère le formulaire et la
vue. On commence avec le formulaire. En fait on va utiliser la même classe que
pour l’ajout, mais on va y rajouter un peu de code (à la fin de la méthode).

### Model

    $idUser = $this->getIdUser();
    if (isset ( $idUser ) && $idUser != "") {
        $user = new Model_DbTable_Users ( );
        $user = $user->fetchRow ( array ("idUser = ?" => $idUser ) );
        if ($user != null) {
            $user = $user->toArray ();
            $this->populate ( $user );
        } else {
            throw new Zend_Exception ( "Il n'y a pas de
            d'utilisateur avec l'id : " . $idUser );
        }
        $password->setDescription ( "form_user_update_password_change" );
        $password->setRequired ( false );
        $submit->setLabel ( 'form_user_update_submit' );
    }

On commence par récupérer la valeur de l’`id` de l’utilisateur. Bien entendu, il
faut déclarer une variable (`private`) et faire le `getter` et le `setter` qui
va bien. Si il y a bien une valeur et qu’elle est différente de null on récupère
une instance de la classe `Model_DbTable_Users`. On sélectionne la ligne qui
va bien en fonction de l’`id`. Si on a bien récupérer un utilisateur (c’est a
dire qu’il y a bien un enregistrement avec cet id)> on **peuple le formulaire**,
c’est a dire qu’on le rempli des données récupérer. Sinon on renvoi une erreur.
Ensuite, ce n’est pas obligatoire, mais je laisse le choix a l’utilisateur : si
il ne veut pas changer de mot de passe, il ne fait rien, sinon il en rentre un
nouveau. Enfin on change le `label` du bouton envoyer>. Mais ce n’est pas finit.
En effet il reste un problème de validateur> sur le champs `email`. Il vaut donc
le supprimer, mais que si c’est le même email (saisi) que l’email qui est déjà
dans la `BDD`. De la même marnière que dans la partie 1, on va devoir redéfinir
la méthode `isValid` :

    public function isValid($data)
    {
        $this->getElement('password')->addValidator(new App_Validate_PasswordMatch($data['password2']));
        if ($this->getElement('email')->getValue() == $data['email']){
            $this->getElement('email')->removeValidator ( "Zend_Validate_Db_NoRecordExists" );
        }
        return parent::isValid($data);
    }

Par rapport au code de la partie 1, on a rajouté le bloc `IF` : Si la valeur en
base est la même que la valeur saisi, on supprime le validateur>, sinon on ne
fait rien (et donc on le laisse). Voilà notre formulaire est enfin prêt. On
passe maintenant au `controller`.

### Controller

    public function editAction() {
        try {
            $form = new Model_Form_User_User ( );
            $form->setIdUser($this->getRequest ()->getParam ( 'id' ));
            $form->init();
            $this->view->formUserEdit = $form;
        } catch (Zend_Exception $e) {
            $this->view->formUserEdit = $e->getMessage();
        }
    }

On essaye d’instancier la classe `Model_Form_User_User ( )`. Ensuite on
récupère la valeur du paramètre `id` (passer en `GET`) et on la passe a notre
classe forme. On est obligé de refaire le formulaire puis on la passe a la vue.
Si il y a un problème (pas d’utilisateur avec id X) on passe les erreurs a la
vue.

    if ($this->_request->isPost ()) {
        $formData = $this->_request->getPost ();
        if ($form->isValid ( $formData )) {
        }
    }

Ensuite, comme dans la première partie, on vérifie qu’il y ai des données de
poster, et les recupère puis on les passes au validateur.

    $user = new Model_DbTable_Users ( );
    if ($formData ['password'] == "") {
        unset ( $formData ['password'] );
        unset ( $formData ['password2'] );
    } else {
        $formData ['password'] = md5 (
        $formData ['password'] );
        unset ( $formData ['password2'] );
    }
    unset ( $formData ['submit'] );
    $result = $user->update ( $formData, array ("idUser = ?" => $formData ['idUser'] ) );

Si elle sont valide, on instancie la classe `Model_DbTable_Users`. On vérifie
la valeur du champs `password` : Si elle est nul, on enlève du tableau de donnée
`formData` les champs `password` et `password2`, sinon on chiffre en md5 le
champ `password` et en enlève `password2`. Enfin on enlève le champ `submit`. Et
pour finir on met a jour la base de donnée.

    $this->_helper->redirector ( 'index', 'user' );

Enfin on redirige vers l’`action index` du `controller user`.

### Vue

Ici c’est le même principe que pour le formulaire d’ajout

    <?php if (isset($this->formUserEdit)) : ?>
        <h2>
            <?php echo $this->translate('view_user_edit')?>:
        </h2>
        <?php echo $this->formUserEdit; ?>
    <?php endif ?>

<p style="text-align: center;">   <a href="{{ relativeRoot }}/wp-
content/uploads/2009/11/edit-user.png"   rel="lightbox[947]"><img
class="aligncenter   size-full wp-image-949" title="edit user" src="{{
relativeRoot   }}/wp-content/uploads/2009/11/edit-user.png"   alt="edit user"
width="468" height="669" /></a> </p>

## Supprimer un Enregistrement {#delete}

La suppression d’un utilisateur **va se faire en deux étape**, la première
servant à confirmer la demande de suppression. Donc on crée une action
intermédiaire qui passe à la vue la valeur de l’`id` de l’utilisateur.


    public function delAction() {
        $id = $this->getRequest ()->getParam ( 'id' );
        $this->view->delUserId = $id;
        $this->render('index');
    }

Et on ajoute à la vue le code qui va générer notre demande de suppression.
Encore ici on réutilise l’aide de vue `url`.

    <?php if (isset($this->delUserId)): ?>
        <h2>
            <?php echo $this->translate('are_you_sure')?>:
        </h2>
        <a
            href="<?php echo $this->url(array('action'=>'delete','id'=>$this->delUserId));?>">
            <?php echo $this->translate('yes')?>
        </a>
        <a href="<?php echo $this->url(array('action'=>'index','controller'=>'user'),null,true)?>">
            <?php echo $this->translate('no')?>
        </a>
    <?php endif ?>

Enfin si l’utilisateur clique sur oui, on exécute l’action suivante :

    public function deleteAction() {
        public function deleteAction() {
        $id = $this->getRequest ()->getParam ( 'id' );
        $user = new Model_DbTable_Users ( );
        $result = $user->delete ( array ("idUser = ?" => $id ) );
        $this->_helper->redirector ( 'index', 'user' );
    }

Et voilà le tour est joué. On a bien supprimer notre utilisateur.

## Conclusion {#conclusion}

On à enfin finit la gestion d’une table de la base de donnée grâce a un seul
formulaire. Je vous donne donc maintenant le code de tous les fichiers dont j’ai
eu besoin. Il y a des petites différence dans la mesure ou j’ai ajouté des
messages de confirmation ou d’échec. De plus la structure de l’archive n’est pas
bonne du tout… Il y a bien tous les fichiers (enfin je pense, mais c’est plus a
titre indicatif). Il ne fonctionneront pas sans le bootstrap, le fichier de
config, le fichier de traduction et enfin la bdd. Je referais un gros package
plus tard. [Fichiers][14]

[1]: {{ relativeRoot }}/zend/zend-framework-formulaire-et-base-de-donnee-partie-1.html "Zend Framework, Formulaire et Base de donnée, partie 1"
[2]: {{ relativeRoot }}/zend/zend-framework-formulaire-et-base-de-donnee-partie-1.html#sommaire
[3]: {{ relativeRoot }}/zend/zend-framework-formulaire-et-base-de-donnee-partie-1.html#bdd
[4]: {{ relativeRoot }}/zend/zend-framework-formulaire-et-base-de-donnee-partie-1.html#formulaire
[5]: {{ relativeRoot }}/zend/zend-framework-formulaire-et-base-de-donnee-partie-1.html#affichage
[6]: {{ relativeRoot }}/zend/zend-framework-formulaire-et-base-de-donnee-partie-1.html#controller
[7]: {{ relativeRoot }}/zend/zend-framework-formulaire-et-base-de-donnee-partie-1.html#validateur
[8]: #sommaire
[9]: #read
[10]: #update
[11]: #delete
[12]: #conclusion
[13]: {{ relativeRoot }}/zend/mettre-en-place-un-systeme-de-traduction-dans-zend-framework.html "Comment mettre en place un systeme de traduction dans Zend Framework"
[14]: {{ relativeRoot }}/wp-content/uploads/2009/11/formulaire.zip
