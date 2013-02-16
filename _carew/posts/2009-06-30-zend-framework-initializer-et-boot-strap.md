---
title: 'Zend framework : Initializer et boot strap'
author: Greg
layout: post
permalink: /zend/zend-framework-initializer-et-boot-strap.html
tags:
  - Zend Framework
---
<h2 style="text-align: center;">
    <span style="text-decoration: underline;color:#ff0000;">
        <strong>
            UPDATE du 22/07/2009 :
        </strong>
    </span>
</h2>

<p style="text-align: center;">
    <span style="text-decoration: underline;color:#ff0000;">
        <strong>
            <a href="{{ relativeRoot }}/zend/zend-framework-1-8-et-son-bootstrap.html">
                Il y a une mise a jour de ce tuto ici.
            </a>
        </strong>
    </span>
</p>

Avant d’aller plus loin dans notre série de tutos, il faut d’abord qu’on règles
les fichier `boostrap`, et l’`initializer`. Ce sont deux fichiers qui s’occupe
de démarrer notre application. C’est dans ces fichiers qu’on va définir toutes
nos constantes de l’application, comme les paramètres pas défaut de
l’application, les sessions, et surtout la connexion à la base de données.

Petite précision : l’`initializer` et le `bootstrat` ne sont plus d’actualité
dans la version 1.8. Cependant cette méthode fonctionne toujours. On va donc
voir la méthode de Zend Framework 1.7. Ca ne pose pas de problème, les deux
méthodes sont très similaire. Dans Zend 1.8, l’`initializer` et le `bootstrap`
ont été réuni. J’essayerais de faire un update de ce tutos des que possible. De
plus l’architecture de la 1.8 a aussi un peu changé. J’essayerais aussi de
mettre a jour ce tutos.

## Bootstrap

De base notre `bootstrat` ressemble a ça :

    /**
     * My new Zend Framework project
     *
     * @author
     * @version
     */
    set_include_path('.' . PATH_SEPARATOR . '../library' . PATH_SEPARATOR . '../application/default/models/' . PATH_SEPARATOR . get_include_path());

    require_once 'Initializer.php';
    require_once "Zend/Loader.php";

    // Set up autoload.
    Zend_Loader::registerAutoload();

    // Prepare the front controller.
    $frontController = Zend_Controller_Front::getInstance();

    // Change to 'production' parameter under production environemtn
    $frontController->registerPlugin(new Initializer('development'));

    // Dispatch the request using the front controller.
    $frontController->dispatch();

La seule chose importante ici est la ligne :

    $frontController->registerPlugin(new Initializer('development'));

Ici on crée un nouvel `Initializer` avec comme paramètre, le nom de
l’environnement (par exemple development, production, test etc…)

## Initializer

On peut donc passer a l’`initializer` (c’est la version final):

    /**
     * My new Zend Framework project
     *
     * @author
     * @version
     */

    require_once 'Zend/Controller/Plugin/Abstract.php';
    require_once 'Zend/Controller/Front.php';
    require_once 'Zend/Controller/Request/Abstract.php';
    require_once 'Zend/Controller/Action/HelperBroker.php';

    /**
     *
     * Initializes configuration depndeing on the type of environment
     * (test, development, production, etc.)
     *
     * This can be used to configure environment variables, databases,
     * layouts, routers, helpers and more
     *
     */
    class Initializer extends Zend_Controller_Plugin_Abstract
    {
        /**
         * @var Zend_Config
         */
        protected static $_config;

        /**
         * @var string Current environment
         */
        protected $_env;

        /**
         * @var Zend_Controller_Front
         */
        protected $_front;

        /**
         * @var string Path to application root
         */
        protected $_root;

        /**
         * Constructor
         *
         * Initialize environment, root path, and configuration.
         *
         * @param string $env
         * @param string|null $root
         * @return void
         */
        public function __construct($env, $root = null)
        {
            $this->_setEnv($env);
            if (null === $root) {
                $root = realpath(dirname(__FILE__) . '/../');
            }
            $this->_root = $root;

            $this->initPhpConfig();

            $this->_front = Zend_Controller_Front::getInstance();

            // set the test environment parameters
            if ($env == 'test') {
                    // Enable all errors so we'll know when something goes wrong.
                    error_reporting(E_ALL | E_STRICT);
                    ini_set('display_startup_errors', 1);
                    ini_set('display_errors', 1);
                    $this->_front->throwExceptions(true);
            }
        }

        /**
         * Initialize environment
         *
         * @param string $env
         * @return void
         */
        protected function _setEnv($env)
        {
            $this->_env = $env;
        }

        /**
         * Initialize Data bases
         *
         * @return void
         */
        public function initPhpConfig()
        {

        }

        /**
         * Route startup
         *
         * @return void
         */
        public function routeStartup(Zend_Controller_Request_Abstract $request)
        {
            $this->initConfig();
            $this->initDb();
            $this->initHelpers();
            $this->initView();
            $this->initPlugins();
            $this->initRoutes();
            $this->initControllers();
        }

        public function initConfig(){
            // On charge Les configs global de l'application et on les met dans un registre
            $config = new Zend_Config_Ini($this->_root . '/application/default/config/config.ini');
            // On met notre fichier de config dans un registre.
            Zend_Registry::set('config', $config);
        }

        /**
         * Initialize data bases
         *
         * @return void
         */
        public function initDb()
        {
            //on charge notre fichier de config
            $config = Zend_Registry::get('config');
            try{
                $db = Zend_Db::factory($config->database);
                //on test si la connection se fait
                $db->getConnection();
            }catch ( Exception $e ) {
                exit( $e -> getMessage() );
            }
            // on stock notre dbAdapter dans le registre
            Zend_Registry::set( 'dba', $db );
        }

        /**
         * Initialize action helpers
         *
         * @return void
         */
        public function initHelpers()
        {
            // register the default action helpers
            Zend_Controller_Action_HelperBroker::addPath('../application/default/helpers', 'Zend_Controller_Action_Helper');
        }

        /**
         * Initialize view
         *
         * @return void
         */
        public function initView()
        {
            // Bootstrap layouts
            Zend_Layout::startMvc(array(
                'layoutPath' => $this->_root .'/application/default/layouts',
                'layout' => 'main'
            ));

        }

        /**
         * Initialize plugins
         *
         * @return void
         */
        public function initPlugins()
        {

        }

        /**
         * Initialize routes
         *
         * @return void
         */
        public function initRoutes()
        {

        }

        /**
         * Initialize Controller paths
         *
         * @return void
         */
        public function initControllers()
        {
            $this->_front->addControllerDirectory($this->_root . '/application/default/controllers', 'default');
        }
    }

On a plusieurs variables qui représente des objets important pour zend :
l’environnement, la configuration, le front controller etc. On peux en ajouter a
la main, comme un gestionnaire de session par exemple. On voit que dans
l’`initalizer` on peut appeler la méthode `initPhpConfig` qui nous permet de
changer au démarrage de l’application les variables ini de php
(ini_set(‘display_startup_errors’, 1);) par exemple. Ensuite on la méthode
`routeStartup` qui s’exécute. Qui elle même se charche d’exécuter toutes les
routines de démarrage de l’application. Celle qui nous intéresse en premier va
etre `initDb`, mais avant il va falloir qu’on gère un fichier de configuration
(de la BDD entre autre). Cela va se faire grâce a `Zend_Config` et grace a
`Zend_Registry`.

## Fichier de configuration, et stockage de celui ci (Zend_Config, Zend Registry)

Tout d’abord on crée notre fichier de config dans : `Budget/application/default/config`.

    [database]
    adapter = "pdo_mysql"
    params.host = "localhost"
    params.username = "budget2"
    params.password = "budget2"
    params.dbname = "budget2"

Dans ce fichier on y met les information concernant l’accès a la base de donnée.
Dans mon cas, j’ai `budget2se connec`localhostu`budget2mot `budget2our
l’adaptateur, si vous êtes sur `mysql`, laissez `pdo_mysql`, sinon referez vous
a la doc.

Maintenant il va falloir faire récupérer les informations du fichier de config
et le mettre dans un registre. Un registre est une zone ou l’on peut stocker des
variables de façon persistante, c’est a dire d’une page a l’autre. Attention,
c’est n’est pas une session, il ne faut utiliser les registres que pour les
données de l’application et non les données des utilisateurs.

On ajoute donc dans la méthode `routeStartup` de l’`initializer`, en première
ligne, un appel la méthode `initConfig` : `$this->initConfig();` Et bien sur on
crée cette méthode :

    public function initConfig(){
        // On charge Les configs global de l'application et on les met dans un registre
        $config = new Zend_Config_Ini($this->_root . '/application/default/config/config.ini');
        // On met notre fichier de config dans un registre.
        Zend_Registry::set('config', $config);
    }

On peut maintenant coder notre méthode : `initDb`.

## Configuration de la base de données

La on récupère notre fichier de config, et on crée une nouvelle `db_factory`. On
teste au passage si la connexion est bien valide. Puis on ajoute ce connecteur a
la base de donné dans un registre qu’on nomera <span style="text-decoration:
underline;">dba</span>, comme Data Base Adaptater.


    public function initDb()
    {
        //on charge notre fichier de config
        $config = Zend_Registry::get('config');
        try{
             $db = Zend_Db::factory($config->database);
             //on test si la connection se fait
             $db->getConnection();
         }catch ( Exception $e ) {
             exit( $e -> getMessage() );
         }
         // on stock notre dbAdapter dans le registre
         Zend_Registry::set( 'dba', $db );
    }

Voila on a notre connexion a la base de donnée, que l’on pourra appeler depuis
n’importe quelle fonction.

Il est possible de configurer plein d’options dans l’initializer, je ne vais pas
m’attardé dessus, il y a la doc pour ca. Par contre dans la mise a jour de cet
article, j’essayerai d’aller plus loin.
