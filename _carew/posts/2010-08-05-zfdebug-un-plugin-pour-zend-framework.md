---
title: 'ZFDebug : Un plugin pour Zend Framework'
author: Greg
layout: post
permalink: /zend/zfdebug-un-plugin-pour-zend-framework.html
tags:
  - Zend Framework
---

**ZFDebug** est un outils très utile lorsqu’on développe avec Zend. En effet ce
plugin, **qui ne demande presque aucunes configurations**, permet de mettre en
évidence beaucoup d’informations utilises. On va voir commment l’installer dans
un projet déjà existant (ou un nouveau, ca ne change rien).Donc ce plugin va
ajouter automatiquement une barre d’outils en bas de votre page. Sur cette
barre, on peut voire les informations suivantes :

* Cache: Information sur Zend_Cache et APC.
* Database: Liste complète des requêtes SQL ainsi que les temps
d’exécution.
* Exception: Capture des erreurs et exceptions.
* File: Nombre et poids des fichiers inclus.
* Html: Nombre de fichier JS et CSS inclus. Liens pour passer la page au
validateur W3C.
* Memory: Utilisation de la mémoire.
* Registry: Contenu du Zend_Registry
* Time: Information sur les temps d’exécution.
* Variables: Variables de vues, de requêtes, de $COOKIE et $POST

Voila à quoi ressemble cette barre  : (Note : Tous les modules ne sont pas
chargé)

<a href="{{ relativeRoot }}/wp-content/uploads/2010/08/ZFDebug-bar1.png"
rel="lightbox[1270]"><img class="aligncenter size-full wp-image-1274"
title="ZFDebug bar" src="{{ relativeRoot }}/wp-content/uploads/2010/08/ZFDebug-
bar1.png" alt="" width="626" height="25" /></a>

Alors pour l’installer il faut  commencer par télécharger la dernière version
sur <a href="http://code.google.com/p/zfdebug/" target="_blank">le site du
projet</a>. Ensuite il faut extraire l’archive et déplacer le dossier `ZFDebug`
qui se trouve dans le dossier `library` de l’archive dans votre dossier
`library` de votre projet. Puis il faut configurer le bootstrap pour qu’il
démarre le plugin. Il faut placer le code qui suit tout à la fin du bootstrap :

    protected function _initZFDebug() {
        // Setup autoloader with namespace
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('ZFDebug');

        // Ensure the front controller is initialized
        $this->bootstrap('FrontController');

        // Retrieve the front controller from the bootstrap registry
        $front = $this->getResource('FrontController');

        // Only enable zfdebug if options have been specified for it
        if ($this->hasOption('zfdebug')) {
            // Create ZFDebug instance
            $zfdebug = new
            ZFDebug_Controller_Plugin_Debug($this->getOption('zfdebug'));

            // Register ZFDebug with the front controller
            $front->registerPlugin($zfdebug);
        }
    }

Et enfin, pour qu’il démarre, il faut ajouter la configuration de la barre dans
le fichier `application.ini` et plus précisément dans la partie ` [development :
production]` pour que cette barre ne se retrouve que sur les machines de
développement.

    zfdebug.plugins.Variables = null
    zfdebug.plugins.Time = null
    zfdebug.plugins.Memory = null
    zfdebug.plugins.Exception = null
    zfdebug.plugins.Html = null
    zfdebug.plugins.Registry = null
    ; zfdebug.plugins.File = null
    ; zfdebug.plugins.Cache = null
    ; zfdebug.plugins.Database = null


Voila le fichier de configuration « minimum ». Il est possible d’activer ou
désactiver des fonctionnalités juste en commentant ou dé-commentant une ligne.
Il y a plus d’information sur le <a href="http://code.google.com/p/zfdebug/wiki/Installation" target="_blank">wiki
du plugin</a>.

Et voila, la barre de Debug se retrouve automatiquement en bas de votre page. Si
ce n’est pas le cas, pensez a vérifier que vous etes bien sur un environement de
developpement. Si vous etes en production, il faudra ajouter la ligne suivante
en haut du fichier `index.php` qui se trouve dans le répertoire `public.`

    apache_setenv("APPLICATION_ENV", "development");
