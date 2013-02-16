---
title: Comment gérer une base de donnée avec le Zend Framework
author: Greg
layout: post
permalink: /zend/comment-gerer-une-base-de-donnee-avec-le-zend-framework.html
tags:
  - Zend Framework
---

Alors la on va aborder un chapitre très important. **Les bases de données sont
bien sur très importantes** pour un site web ou un service web. On va voir
ensemble comment **ajouter, modifier, supprimer des enregistrements (ou tuples)
dans un table, dans une base de donnée**. On va donc utiliser la gestion **CRUD
du Zend Framework 1.9**.

## Sommaire {#sommaire}

* [Sommaire][1]
* [Connexion a la base de donnée][2]
* [Schémas de la table][3]
* [Préparation de la classe métier][4]
* [Obtenir et récupérer des enregistrements][5]
* [Ajouter des enregistrements][6]
* [Modifier des enregistrements][7]
* [Supprimer des enregistrement][8]
* [Etendre la classe métier][9]
* [Conclusion][10]

## Connexion a la base de donnée. {#connection}

**La connexion à la base de donnée principale se fait toute seule**. Je
m’explique, c’est le **bootstrap qui va
initialiser notre connexion à la base de donnée.** En fait le `bootstrap`
va charger le fichier de **configuration
de l’application** et c’est dans ce fichier que l’on va définir les
paramètres de connexion a la `BDD`. Il
faut donc éditer le fichier `application/configs/application.ini`

    ; DATABASE
    resources.db.adapter = "MYSQLI"
    resources.db.params.host = "localhost"
    resources.db.params.username = "budget"
    resources.db.params.password = "budget"
    resources.db.params.dbname = "budget"
    resources.db.params.date_format = "YYYY-MM-ddTHH:mm:ss"
    resources.db.isDefaultTableAdapter = true

Si on veut que notre connexion à la `BDD` soit géré automatiquement, le schémas
(ci dessus) est très rigide. En effet **zend** va chercher automatiquement si
dans le `fichier de configuration` il y a des constantes «`ressources.db.**` »
de défini, et si oui il va essayé de se connecter à la base de donnée. Il est
bien sur possible d’avoir plusieurs connexions à différente `SGBD` (**mysql,
orable, DB2** …). Si vous utiliser **mysql**, il suffit de copier / coller le
schémas ci dessus, et de bien remplacer les lignes : `host`, `username`,
`password `et `dbname` qui signifie respectivement : hôte, nom d’utilisateur,
mot de passe, nom de la BDD. Sinon l’`adpater` est le `driver` à utiliser pour
se connecter (`mysql`, `orable`, `DB2`… ; se référer a la doc) ; `date_format`
est le format de la date ; et `isDefaultTableAdapter` est pour savoir si cette
connexion est la principale.

## Schémas de la table {#schemas}

On va utiliser un exemple pour bien mettre en place ce tuto. On va utiliser une
table d’utilisateur. Voilà le schémas :

    CREATE TABLE IF NOT
    EXISTS `users` (
      id int(50) NOT NULL auto_increment,
      *name` varchar(50) NOT NULL,
      firstname varchar(50) NOT NULL,
      email varchar(255) NOT NULL,
      *password` varchar(255) NOT NULL,
      phonenumber varchar(20) NOT NULL,
      *enable` int(1) NOT NULL,
      *level` int(1) NOT NULL,
      PRIMARY KEY (id),
      UNIQUE KEY email (email)
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

On a donc :

* Un id en auto-increment
* Un nom
* Un prénom
* Une adresse email
* Un mot de passe
* Un numéro de téléphone
* Un booléen pour savoir si l’utilisateur est activé
* Un entier pour avoir le niveau d’administration
* Une clé primaire et un index sur l’id
* Une clé unique et un index sur l’email

## Préparation de la classe métier. {#metier}

`Zend` commence à gérer de façon assez **autonome et automatique les BDD**. Il y
a maintenant un gestionnaire **CRUD** (`Create`, `Read`, `Update`, `Delete `=>
Ajouter, Lire, Mettre a jour, Supprimer des enregistrements). Pour se faire il
faut créer une `class` php. On est assez libre pour le nom des `class` et pour
leurs emplacements, mais je vous recommanderai d’être assez **logique et
rigoureux**. En fait il va falloir **faire une **class** par table de
votre base de donnée**. Pour ma part, je mets presque toujours un « s » a la fin
du nom de mes tables, car elles contiennent plusieurs enregistrements. Par
exemple la table `users` contient plusieurs utilisateurs. Il faut maintenant
créer une class qui représente cette table. On peut organiser ses `class` comme
on veut, mais en général sur le petits projets je mets toutes mes `class` dans
le dossier `application/models/DbTable/`. De plus je nomme toujours ma class
avec le même nom que la table. A quoi doit ressembler la `class` ([Voir la
convention de nommage des `models`][11]) :

    <?php
    class Model_DbTable_Users extends Zend_Db_Table {
        protected $_name = 'users';
    }

Pour le nom de la `class`, il faut se référer au model dans le Zend Framework.
Il faut étendre la **class Zend_Db_Table**. Enfin il faut ajouter l’attribut
**protected $_name** qui a pour valeur le **nom de la table dans la base de
donnée**. Et voilà, le plus dur est fait. On a maintenant une `class` qui
possède un bon nombre de méthode `CRUD`.

## Obtenir et récupérer des enregistrements {#read}

**Comment obtenir tous les tuples d’une table ?** Si il n’y a pas de close
particulières, c’est super simple , on exécute le petit bout de code suivant :

    private function getUsersAll()
    {
        $dbUser = new Model_DbTable_Users();
        return $dbUser->fetchAll()->toArray();
    }

Donc on instancie un nouveau `Model_DbTable_Users`, et on lui demande la
retourner tous les enregistrements
grâce a la méthode `fetchAll`, et enfin on convertit le résultats en
tableau. Voilà le tours est joué !
**Comment obtenir que certains enregistrements ?**

    private function getUsersByName($name = '')
    {
        $dbUser = new Model_DbTable_Users();
        return $dbUser->fetchAll()->toArray(array('name = ?'=>
        $name));
    }

Voilà, la méthode `fetchAll` possède 4 arguments:
`$where`, `$order`,`$count`,`$offset` qui représente respectivement la clause
`where`, le clause `order by` , et la clause `limit` (combien d’enregistrement
et à partir de l’enregistrement numéro n ). Pour une utilisation plus poussé, je
vous recommande de lire la doc du Zend Framework.

## Ajouter un enregistrement {#create}

**Comment ajouter un enregistrement dans la base de donnée ?** Comme dans
l’exemple précédant, on va ajouter un nouvel utilisateur.

    $users = new
    Model_DbTable_Users();
    $row = $users->createRow();
    $row->name = 'Nom';
    $row->firstname = 'Prenom';
    $row->email = 'email';
    $row->password = md5('password');
    $row->phonenumber = '0102030405';
    $row->enable = 1;
    $row->level = 9;
    $result = $row->save();

On commence par instanciée un nouveau `Model_DbTable_Users`. Ensuite on crée
une ligne, puis on ajoute a cette ligne les différents attributs, et enfin on
sauvegarde cette ligne en table. On peut aussi faire la même chose en donnant a
la méthode `createRow` un tableau en argument. :

    $users = new Model_DbTable_Users();
    $datas = array(
        'name'=>'Nom',
        'firstname'=>'Prenom',
        'email'=>'email2',
        'password'=>md5('password'),
        'phonenumber'=>'0102030405',
        'enable'=>1,
        'level'=>9,
    );
    $row = $users->createRow($datas);
    $row->save();

**Enfin on peut faire beaucoup plus simple**. Je ne connais pas bien la
différence entre les deux méthodes (la précédente et la suivante), si quelqu’un
peut m’éclairer, je suis preneur.

    $users = new Model_DbTable_Users();
    $datas = array(
        'name'=>'Nom',
        'firstname'=>'Prenom',
        'email'=>'email3',
        'password'=>md5('password'),
        'phonenumber'=>'0102030405',
        'enable'=>1,
        'level'=>9,
    );
    $row = $users->insert($datas);

Bon, c’est assez simple d’ajouter un enregistrement en table ? On va un peu plus
loin. Comment **protéger ses enregistrements en table grâce a une
**transaction SQL** :

    $users = new Model_DbTable_Users();
    $datas = array(
        'name'=>'Nom',
        'firstname'=>'Prenom',
        'email'=>'email3',
        'password'=>md5('password'),
        'phonenumber'=>'0102030405',
        'enable'=>1,
        'level'=>9,
    );
    $defaultAdaptateur = Zend_Db_Table::getDefaultAdapter();
    $defaultAdaptateur->beginTransaction();
    try {
        $defaultAdaptateur->insert('users', $datas);
        $defaultAdaptateur->commit();
    } catch (Exception $e) {
        $defaultAdaptateur->rollBack();
        echo $e->getMessage();
    }

On commence de la même façon que pour la dernière méthode. Ensuite on récupère
l’`adaptateur` de la base de donnée principale puis on commence une
`transaction`. Il faut ensuite en-capsuler notre ajout à la base dans un bloque
`try-catch`. On essaye d’insérer notre enregistrement puis si ça fonctionne on
`commit`. Si ça ne fonctionne pas on fait un `rollback `et on affiche les
messages. **Bien sur il est possible de n’utiliser qu’un bloque try-catch**.

## Mettre a jour un enregistrement {#update}

La méthode ici est très similaire à l’ajout d’un enregistrement. On peut mettre
a jour uniquement qu’une seule ligne ou tout un groupes de ligne. Dans cet
exemple on remplace tous les enregistrements ou l’attribut name est « Nom » par
« Nom2 ».

    $users = new Model_DbTable_Users();
    $datas = array('name'=>'Nom2');
    $users->update($datas,array('name = ?'=>'Nom'));
    $this->render('index');

Ici les `$datas` sont les données qu’on va mettre a jour. La méthode `updates
`va tout faire. Le premier arguments est le **tableau de donnée**, le second
argument correspond aux **conditions de la clause **where**. Dans ce cas la
on explicite que l’attribut name doit correspondre a Nom.

## Supprimer un enregistrement {#delete}

Cette dernier action est la plus simple, il suffit de **bien formuler la
clause **where**.

    $users = new Model_DbTable_Users();
    $users->delete(array('name = ?'=>'Nom2'));

Ici on supprimer tous les utilisateurs qui ont pour valeur d’attribut name :
Nom2.

## Extension de la class métier {#extend}

Bien entendu, on peut ajouter des méthodes particulières a notre `class` métier.
Comme par exemple pour avoir le nombre d’enregistrement :

    public function getUserCount()
    {
        $sql = 'select count(1) cnt from users';
        $stmt = $this->_db->query($sql);
        $results = $stmt->fetchAll();
        if ((sizeof($results) > 0) && (isset($results[0]['cnt']))) {
            return $results[0]['cnt'];
        }
        throw new Exception("Erreur : impossible d'obtenir le nombre
        d'utilisateur");
    }

## Conclusion {#conclusion}

On peut bien sur en-capsuler un update, une suppression dans un block try-catch
et de plus on est pas obligé d’utiliser les transaction sql qui sont quand même
plus lourde. On verra dans un prochain tuto comme utiliser les clés étrangères.

[1]: #sommaire
[2]: #connection
[3]: #schemas
[4]: #metier
[5]: #read
[6]: #create
[7]: #update
[8]: #delete
[9]: #extend
[10]: #conclusion
[11]: {{ relativeRoot }}/zend/zend-comment-utiliser-un-model.html "Zend : Comment utiliser un model ?"
