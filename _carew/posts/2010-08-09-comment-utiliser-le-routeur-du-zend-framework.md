---
title: Comment utiliser le Routeur du Zend Framework
author: Greg
layout: post
permalink: /zend/comment-utiliser-le-routeur-du-zend-framework/
tags:
  - Zend Framework
---

Je suppose que vous connaissez déjà tous l’**url rewriting**. Il est assez
simple de mettre en place des formats d’URL (user firendly) prédéfinis avec
**Zend**. On va voir ensemble comment faire.

Comme souvent avec le **Zend Framework**, il y a plusieurs façons de faire. Je
vais vous en montrer deux. **Une première en PHP**, puis une **deuxième avec le
fichier de configuration application.ini**. Quelque que soit la méthode, tout le
code PHP sera à mettre dans le fichier `bootstrap.php`. Pourquoi ? Car il faut
bien que le routage s’effectue avant le premier `dispatch`.

## Préparation du bootstrap

On peut commencer par préparer notre `bootstrap` : il faut ajouter une nouvelle
méthode :

    protected function _initRouter() {
        $front = $this->bootstrap('FrontController')->getResource('FrontController');
        $router = $front->getRouter();
    }

On ajoute la méthode `_initRouter()`. Pour l’instant cette méthode est un peu
vide. La première ligne de code sert à démarrer le `bootstrap `si cela n’avait
pas encore été fait puis a récupérer le `FrontController` depuis le `registre`
du `bootstrap`. La deuxième ligne sert à récupérer le `router`.

## Configuration en php depuis le bootstrap

### Syntaxe d’ajout

Il existe différent type de route possible. Des routes statiques,  dynamiques,
basés sur des expressions régulières, basés sur le domaine et sous-domaines …
Mais quelque soit le type de route choisi, la syntaxe reste la même. Il faut
commencer par créer une route, puis ensuite l’ajouter au routeur.

    $uneRoute = new Zend_Controller_Router_Route($route, $defaults, $reqs);
    $router->addRoute('uneRoute', $uneRoute);

* `$route` correspond a l’Uri de la ressource. C’est le bout de l’URL
    qui se trouve après le nom de domaine. Par
    exemple l’Uri peut être ‘a-propos’ ou alors ‘contactez-nous’.
* `$default` doit être un tableau contenant les informations de redirection
    (module, controller, action, …)
* `$reqs` est optionnels, il permet d’ajouter des contraintes.
* `$uneRoute` est la route au format Zend_Controller_Router_Route

enfin la pseudo-constante `uneRoute` est la clé identifiant la route. En effet
il est possible de retrouver une route enregistrée dans le routeur grâce et
uniquement grâce à cette clé.

<span style="text-decoration: underline;">Note :</span> il est très fortement
déconseillé de mettre des accents ou des caractères « bizarres » dans le nom de
la route ou dans la clé de la route.

### Routage Statique

On va commencer avec le routage statique. Avec le routage statique, il n’est pas
vraiment possible de transmettre des variables en GET. On utilise par exemple le
routage statique pour la page a-propos, contact etc … Voici un exemple :

    protected function _initRouter() {
        $front = $this->bootstrap('FrontController')->getResource('FrontController');
        $router = $front->getRouter();
        $route = new Zend_Controller_Router_Route('accueil', array('controller' => 'index', 'action' => 'index')));
        $router->addRoute('accueil', $route);
    }

<span style="text-decoration: underline;">Note :</span> Ici j’ai remis tous le
code à mettre dans le `bootstrap`. Dans la suite du tutoriel, je ne mettrais que
les lignes de code intéressantes (i.e. les deux dernières lignes).

Dans cette exemple, le fait de se rendre sur la page
`http://exemple.com/accueil` aura le même effet que d’aller sur la page
`http://exemple.com/index/index`. Bien entendu, ce n’est pas une redirection
(l’url ne change pas), mais bien un vrai routage ou ré-écriture d’Url. Dans cet
exemple, la route ‘accueil’ porte le même nom que la clé. C’est en général plus
simple de mettre les même valeurs, ou alors des valeurs proches.

### Routage Dynamique

Qu’est-ce que le routage dynamique ?  Par exemple vous voulez qu’une partie de
l’url soit dynamique : un id, un nom de fonction, un message … Il faut donc,
dans le routeur, nommé cette variable. Pour ce faire il faut utiliser les « : »
devant le nom de votre variable. Voici un exemple ou l’on veut faire passer une
message en paramètre (GET) de notre URL :

    $route = new Zend_Controller_Router_Route('contact/:topic', array('controller' => 'index', 'action' => 'contact','topic' => 'Topic par defaut'));
    $router->addRoute('contact', $route);

Ici nous faisons passer dans les paramètres GET un sujet (`topic`). Ainsi si
l’on se rend à l’adresse suivante : `http://exemple.com/contact/Mon Topic`, le
controller exécuter sera le controller `indexController`, et l’action exécuter
sera l’action `contactAction`. On pourra récupérer la valeur de `topic `grâce à
:

    $this->getRequest()->getParam('topic');

Il faut placer ce code dans la méthode `contactAction` du fichier
`indexController.php`. C’est transparent pour l’utilisateur, mais il y a bien eu
une ré-écriture d’url. Sans celle ci l’URL aurai pu être :
`http://exemple.com/contact/?topic=Mon Topic`.

On peut aussi attribuer une valeur par défaut à notre variable `topic`. C’est un
des arguments du tableau `$default` : `topic => Topic par defaut`. Donc  si
l’utilisateur se rend à cette adresse : `http://exemple.com/contact ` la valeur
de `topic` sera :  `Topic par defaut`.

Vous pouvez aussi remarquer qu’ici la route est différente de la clé de celle-ci
(`contact/:topic` VS `contact`).

### Routage Dynamique avec des contraintes

On peut ajouter des contraintes aux routages dynamique. Comme par exemple forcer
que la variable x ou y soit un entier ou un string. On utilise pour ca la
variable `$reqs` :

    $route = new Zend_Controller_Router_Route(
        'membres/:id/`',
        array(
            'controller' => 'index', 'action' => 'membres'
        ),
        array('id' => 'd+')
    );
    $router->addRoute('membres', $route);

Ici on force la route à utiliser un id de type nombre. Si ce n’est pas un
nombre, l’URL ne passera pas dans la route, et il y aura une erreur.

Vous pouvez remarquer aussi qu’il est possible d’utiliser des caractères joker
(wildcard). Le caractère ` symbolise tout type de caractère. Grâce a ce
caractère, il est possible de mettre a peu près tout ce que l’on veut dans
l’url. Il est donc possible de faire passer des informations dans l’URL même
avec un routages statiques.

### Routage avec les expressions régulières.

On à déjà vu le routage statique et le routage dynamique. Mais juste avec ces
deux routages, il n’est pas encore possible de réaliser toutes les routes
inimaginables. Par exemple, il n’est pas encore possible de faire une route qui
ressemblerai a ca : `http://exemple.com/team/25-aka` ou alors
`http://exemple.com/blogpost/9-Comment-faire-cuire-une-pizza.html`.

Voila le code d’une route :

    $routeRegex = new Zend_Controller_Router_Route_Regex(
        'teams/(d+)-(w+)',
        array('controller' =>'index', 'action' => 'teams'),
        array(1 => 'id',2=>'name'),
        'teams/%d-%s'
    );
    $router->addRoute('teams', $routeRegex);

Il faut noter que la route n’est plus de type `Zend_Controller_Router_Route`
mais de type `Zend_Controller_Router_Route_Regex`.

La route en elle-même est à peu près la même que pour le routage dynamique. Il
faut utiliser des expressions régulières. Ici les routes acceptées sont du type
: `http://exemple.com/teams/NOMBRE-STRING`.

Le troisième argument du constructeur est un peu différent. Il permet de mapper
(i.e. binder, associé) les ‘variables’ capturées par la regxp (grâce aux
parenthèse) à des noms de variables. Par exemple, ici le nombre `capturé` sera
mapper à la variable `id` et le `string` sera mappé à la variable `name`.

Enfin le dernier paramètre du constructeur sert au reverse. Il est possible
(comme pour tous les autres types de route) d’utiliser un ViewHelper pour
construire notre url (dans le cas où l’on veut faire un lien par exemple).
Cependant avec une expression régulière en entré, il n’est pas très facile de
pouvoir deviner le pattern en sortie. C’est pourquoi il y a ce dernier argument
qui est facultatif. En effet il est facultatif, car tant qu’on n’utilise pas le
ViewHelper, on n »en a pas besoin.

<span style="text-decoration: underline;">Note :</span> on verre plus tard
comment utiliser le ViewHelper url() avec les routes.

## Configuration avec un fichier de configuration .ini

<span style="text-decoration: underline;">Note :</span> pour cette partie, je ne
vais pas trop détaillé le code. Il reste assez similaire pour le bootstrap. Le
fichier .ini reprend le même fonctionnement qu’en php.

### Configuration du Bootstrap

Il faut commencer par créet une nouvelle méthode dans le fichier bootstrap.php

protected function _initRouter() {

    $front = $this->bootstrap('FrontController')->getResource('FrontController');
    $router = $front->getRouter();
    $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');
    $routing = new Zend_Controller_Router_Rewrite();
    $routing->addConfig($config, 'routes');
     $front->setRouter($routing);}

### Application.ini

Et maintenant, il faut coller ce code dans le fichier application.ini, dans la
section production

    ; Routage

    routes.accueil.route = "accueil"
    routes.accueil.defaults.controller = "index"
    routes.accueil.defaults.action = "index"

    routes.apropos.type = "Zend_Controller_Router_Route"
    routes.apropos.route = "a-propos"
    routes.apropos.defaults.controller = "index"
    routes.apropos.defaults.action = "apropos"

    routes.contact.type = "Zend_Controller_Router_Route"
    routes.contact.route = "contact/:topic"
    routes.contact.defaults.controller = "index"
    routes.contact.defaults.action = "contact"
    routes.contact.defaults.topic = "Topic par defaut"

    routes.membres.type = "Zend_Controller_Router_Route"
    routes.membres.route = "membres/:id/`"
    routes.membres.defaults.controller = "index"
    routes.membres.defaults.action = "membres"
    routes.membres.reqs.id = "d+"

    routes.teams.type = "Zend_Controller_Router_Route_Regex"
    routes.teams.route = "teams/(d+)-(w+)"
    routes.teams.defaults.controller = "index"
    routes.teams.defaults.action = "teams"
    routes.teams.map.1 = "id"
    routes.teams.map.2 = "name"
    routes.teams.reverse ="teams/%d-%s"

On peut voir que pour un routage statique simple, 3 lignes suffisent (cf
accueil). Cependant on peur forcer le type de la route (cf apropos).

Comme en PHP, il est aussi possible de définir une route dynamique (cf contact).
Et on peut aussi faire une validation par expressions régulières (cf membres).

Enfin pour utiliser un route de type expression régulière, il faut utilise le
type « `Zend_Controller_Router_Route_Regex` » (cf teams).

## Utilisation de l’aide de Vue url()

L’aide de vue url() est très utile. Il est possible de l’utiliser avec le
système de route du Framework. On peut l’utiliser depuis une vue ou depuis le
layout. En accord avec la définition des routes vu précédemment, on peut
l’appeler de la façon suivante :

    echo $this->url(array(),'accueil').'<br />';
    echo $this->url(array(),'apropos').'<br />';
    echo $this->url(array('topic'=>'Sujet du message'),'contact').'<br />';
    echo $this->url(array('id'=>'78'),'membres').'<br />';
    echo $this->url(array('id'=>'25','name'=>'aka'),'teams').'<br />';

Le première argument doit etre un tableau (`array`). Il représente les arguments
(ou paramètres) de la route. C’est un tableau associatif. La clé représente le
nom de la variable, et la valeur représente la valeur de cette variable. Le
deuxième argument représente la nom de la route.
