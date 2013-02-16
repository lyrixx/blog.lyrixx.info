---
title: 'Tuto : Zend Framework 1.8 et son bootstrap'
author: Greg
layout: post
permalink: /zend/zend-framework-1-8-et-son-bootstrap.html
tags:
  - Zend Framework
---

Voila, on va voir comment fonctionne le `Boostrap` de la version 1.8 de **Zend
Framework**. Par default le fichier `Bootsrap.php` est presque vide. Il ne
contient que ces quelques lignes :

    class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
    {

    }

Mais on va voir ensemble **comment construire notre bootstrap**

## Sommaire {#sommaire}

* [Sommaire][1]
* [Comment fonctionne le bootstrap][2]
* [Mise en place du fichier de configuration][3]
* [Connexion a la base de données][4]
* [Mise en place des sessions][5]
* [Mise en place de modules][6]
* [Conclusion][7]

## Comment fonctionne le bootstrap ? {#fonctionne}

**Mais a quoi sert le `bootstrap** ? Le `bootstrap`, c’est le fichier qui va
être lancé au début de l’application et qui va s’occuper d’exécuter toutes les
petites routines, de faire les connections aux bases de données, d’instancier
toutes les constantes, les sessions, les modules, les layouts etc …

Par défaut le `bootstrap` fonctionne même si il est vide, mais on peut faire en
sorte de le customiser un peu. Comment faire ? Déjà le fichier index.php qui est
le premier a être exécuter (dans le dossier public) va lancer la méthode `run`
du `bootstrap`. On peut alors créer cette méthode. Ensuite  il faut savoir que
le `bootstrap` hérite de `Zend_Application_Bootstrap_Bootstrap` qui lui est
magique (On va y revenir). Donc si on redéfinit la méthode `run`, il ne faut pas
oublier d’appeler la methode `run` de la class :
`Zend_Application_Bootstrap_Bootstrap`.

    public function run()
    {
        parent::run();
    }

On peut ensuite créer plusieurs méthodes pour par exemple faire les connections
aux bases de données, initialiser les sessions etc. Et c’est la que le
`bootstrap` est magique : il suffit de déclarer les méthodes en `protected` et
de mettre un underscrore (_) devant le nom de la méthode, et celle-ci sera
directement exécuter.

Si on regarde le code de la méthode `run` de la class
`Zend_Application_Bootstrap_Bootstrap`, on voit que par défaut on n’utilise
pas de module. Il est possible d’utiliser les modules, mais on verra ça plus
tard. Donc par défaut il faut respecter l’architecture « normale ».

## Mise en place du fichier de configuration. {#configuration}

Comme on a vu dans l’architecture du zend framework, il y a deja un fichier de
configuration, mais par défaut il n’est pas vraiment utilisable. Je m’explique :
quand on démarre l’application, celle ci utilise bien le fichier de
configuration. Mais si a un moment ou a un autre on a besoin de ce fichier, il
sera compliqué d’utiliser les constantes définit. Par défaut le fichier de
config ressemble a celui la :

    [production]
    phpSettings.display_startup_errors = 0
    phpSettings.display_errors = 0
    includePaths.library = APPLICATION_PATH "/../library"
    bootstrap.path = APPLICATION_PATH "/Bootstrap.php"
    bootstrap.class = "Bootstrap"
    resources.frontController.controllerDirectory = APPLICATION_PATH "/controllers"

    [staging : production]

    [testing : production]
    phpSettings.display_startup_errors = 1
    phpSettings.display_errors = 1

    [development : production]
    phpSettings.display_startup_errors = 1
    phpSettings.display_errors = 1

Celui ci est basé sur un model orienté objet. Je vous conseil de lire le manuel
de Zend si vous êtes un peu perdu : <a href="http://zendframework.com/manual/fr/zend.config.adapters.ini.html"
target=" _blank">http://zendframework.com/manual/fr/zend.config.adapters.ini.html</a>.

On va donc faire en sorte de pouvoir utiliser ce fichier depuis n’importe ou
depuis l’application. On va utiliser la `Zend_Registry`. C’est quoi
`Zend_Registry` ? C’est un registre (un zone mémoire sur le serveur) ou l’on va
pouvoir stocker toutes sorte de choses : constante, pointeurs vers des bases de
données etc… On ajoute donc notre fichier de config (qui sont en faites les
options (parametres) du bootstrap) à un registre config :

    public function run()
    {
        // Cela permet d'avoir le fichier de configuration disponible depuis
        n'importe ou dans l'application.
        Zend_Registry::set('config', new Zend_Config($this->getOptions()));
        parent::run();
    }

## Connexion a la base de données {#bdd}

Pour se faire on va utiliser les données du fichier de configuration :
`application.ini`. On va le convertir en vrai fichier de configuration grâce a
`Zend_Config`, puis on va le donnée a `Zend_Db` qui va nous faire une factory de
notre base de donnée. C’est grâce a cette factory qu’on pourra excécuter des
requetes directement sur la base de donnée.

    resources.db.adapter ="MYSQLI"
    resources.db.params.host = "localhost"
    resources.db.params.username = "budget2"
    resources.db.params.password = "budget2"
    resources.db.params.dbname = "budget2"
    resources.db.params.date_format = "YYYY-MM-ddTHH:mm:ss"
    resources.db.isDefaultTableAdapter = true

Pour les variables du fichier de configuration, vous pouvez vous référer au
manuel de Zend Framework.

    /**
    * Initialize data bases
    *
    * @return Zend_Db::factory
    */
    protected function _initDb()
    {
        //on charge notre fichier de configuration
        $config = new Zend_Config($this->getOptions());
        //On essaye de faire une connection a la base de donnee.
        try{
             $db = Zend_Db::factory($config->resources->db);
             //on test si la connection se fait
             $db->getConnection();
         }catch ( Exception $e ) {
             exit( $e -> getMessage() );
         }
         // on stock notre dbAdapter dans le registre
         Zend_Registry::set( 'dba', $db );

         return $db;
    }

Enfin on met cette factory dans un registre de manière a pouvoir l’utiliser
depuis n’importe ou dans l’application.

<span style="text-decoration: underline;"><strong>Note</strong></span> : On
créer ici une connection a la base de donnée qui pourra etre utilisé depuis
n’importe quelle partie de l’application. Cependant on verra qu’en regle
général, on ne fait pas directement de requete sur la base de donnée, [on
utilise des models pour se faire ][8]…

## Initialisation des sessions : {#session}

On va mettre en place les sessions tout de suite : c’est assez simple. Je pense
que le code parle de lui meme.

    /**
     * Initialize session
     *
     * @return Zend_Session_Namespace
     */
    protected function _initSession()
    {
        // On initialise la session
        return new Zend_Session_Namespace('budget', true);
    }

## Mise en place de modules : {#module}

Une bonne partie de la déclaration d’un module par défault va se faire
directement dans le fichier de configuration :

    #initialize front
    controller resource
    resources.frontController.moduleDirectory = APPLICATION_PATH "/modules"
    resources.frontController.defaultControllerName = "index"
    resources.frontController.defaultAction = "index"
    resources.frontController.defaultModule = "Frontend"

Et une autre partie dans le bootstrap :

    /**
    * Initialize Module
    *
    * @return Zend_Application_Module_Autoloader
    */
    protected function _initAutoload()
    {
        $loader = new Zend_Application_Module_Autoloader(array(
            'namespace' => '',
            'basePath'  => APPLICATION_PATH,
        ));

        return $loader;
    }

**<span style="text-decoration: underline;">Note :</span>** Cependant, si
quelqu’un connait bien ZF, j’aimerais avoir une renseignement, car il me semble
que le code du bootsrap soit facultatif …

## Conclusion : {#conclusion}

Voila, on commence a avoir un bon bootstrap qui tient la route, mais on verra
par la suite qu’on peut rajouter beaucoup de chose dans ce bootstrap, comme par
exemple l’utilisation des layouts …

N’hesitez par a aller faire une tours sur le site de
<a href="http://framework.zend.com/manual/fr/zend.application.html"
target="_blank">ZF – page application</a>

On retrouve les deux fichiers :

    class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
    {
        public function run()
        {
            // Cela permet d'avoir le fichier de configuration disponible depuis n'importe ou dans l'application.
            Zend_Registry::set('config', new Zend_Config($this->getOptions()));

            parent::run();
        }

        /**
        * Initialize Module
        *
        * @return Zend_Application_Module_Autoloader
        */
        protected function _initAutoload()
        {
            $loader = new Zend_Application_Module_Autoloader(array(
                'namespace' => '',
                'basePath' => APPLICATION_PATH
            ));

            return $loader;
        }

        /**
        * Initialize data bases
        *
        * @return Zend_Db::factory
        */
        protected function _initDb()
        {
            //on charge notre fichier de configuration
            $config = new Zend_Config($this->getOptions());

            //On essaye de faire une connection a la base de donnee.
            try{
                 $db = Zend_Db::factory($config->resources->db);
                 //on test si la connection se fait
                 $db->getConnection();
            }catch ( Exception $e ) {
                exit( $e -> getMessage() );
            }

            // on stock notre dbAdapter dans le registre
            Zend_Registry::set( 'dba', $db );

            return $db;
        }

        /**
         * Initialize session
         *
         * @return Zend_Session_Namespace
         */
        protected function _initSession()
        {
            // On initialise la session
            $session = new Zend_Session_Namespace('budget', true);

            return $session;
        }
    }

Et:

    [production]
    phpSettings.display_startup_errors = 0
    phpSettings.display_errors = 0
    includePaths.library = APPLICATION_PATH "/../library"
    bootstrap.path = APPLICATION_PATH "/Bootstrap.php"
    bootstrap.class = "Bootstrap"
    resources.frontController.controllerDirectory = APPLICATION_PATH "/controllers"

    #initialize front controller resource
    resources.frontController.moduleDirectory = APPLICATION_PATH "/modules"
    resources.frontController.defaultControllerName = "index"
    resources.frontController.defaultAction = "index"
    resources.frontController.defaultModule = "Frontend"

    [staging : production]

    [testing : production]
    phpSettings.display_startup_errors = 1
    phpSettings.display_errors = 1

    [development : production]
    phpSettings.display_startup_errors = 1
    phpSettings.display_errors = 1

    #DATABASE
    resources.db.adapter = "pdo_mysql"
    resources.db.params.host = "localhost"
    resources.db.params.username = "budget2"
    resources.db.params.password = "budget2"
    resources.db.params.dbname = "budget2"
    resources.db.params.date_format = "YYYY-MM-ddTHH:mm:ss"
    resources.db.isDefaultTableAdapter = true

[1]: #sommaire
[2]: #fonctionne
[3]: #configuration
[4]: #bdd
[5]: #session
[6]: #module
[7]: #conclusion
[8]: {{ relativeRoot }}/zend/comment-gerer-une-base-de-donnee-avec-le-zend-framework.html "Voir le tuto sur la gestion des bases de données"
