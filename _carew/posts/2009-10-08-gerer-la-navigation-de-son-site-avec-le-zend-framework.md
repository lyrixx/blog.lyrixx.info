---
title: Comment gérer la navigation de son site avec le Zend Framework
author: Greg
layout: post
permalink: /zend/gerer-la-navigation-de-son-site-avec-le-zend-framework.html
tags:
   - Zend Framework
---
Dans ce tuto on va voir comment
mettre en place un système automatique de **navigation** dans le **Zend
Framework**. C’est a dire que notre **menu de navigation soit dynamique et
flexible**. On pourra par exemple avoir notre **chemin de navigation ou fil de
navigation**. On va donc utiliser le composant **Zend_Navigation**. On a
plusieurs possibilité pour la gestion et le stockage de l’arbre (ou graphe) de
navigation. Moi j’ai choisit le `XML` pour le stockage, et la gestion par
`module`, `controller` et `action`. Enfin on utilise aussi la traduction
automatique ;)

## Sommaire {#sommaire}

* [Sommaire][1]
* [Introduction][2]
* [Bootstrap][3]
* [Layout][4]
* [Hiérarchie de pages][5]
* [Classe css de la page en cours][6]
* [Fil de Navigation ou breadcrumbs][7]

## Introduction {#intro}

On commence par un aperçu de notre fichier de navigation :

    <?xml version="1.0" encoding="UTF-8"?>
    <configdata>
    <nav>
        <home>
            <label>nav_home</label>
            <controller>index</controller>
        </home>
    </nav>
    </configdata>

Petite explication : la balise `configdata` ne sert que de nœud racine, la
balise `nav` sert a repérer notre section de la gestion de la <span style="font-
style: normal;">navigation</span>. Dans mon cas je n’utilise le fichier
`navigation.xml` que pour gérer la navigation, pour on pourrait envisager de
n’avoir qu’un seul gros fichier xml pour gérer toutes nos données et variables
de l’application. Ensuite on a une balise `home`, elle représente un lien du
**menu**, on lui donne le nom qu’on veut, elle va représenter dans notre cas la
page `d’accueil`. Elle a pour classe fille une balise `label` qui définit le nom
du lien. J’utilise `nav_home` car dans mon fichier de traduction j’ai une clé
`nav_home` qui fait référence a `home`. Enfin il y a une balise `controller` qui
définit sur quel `controller` pointe le futur lien. Il est bien sur possible de
sélectionner une `action` avec la balise `action` et un `module` avec la balise
`module`. C’est bien fait non ? Sinon il est possible aussi d’avoir un liens
direct grâce a la balise `uri`. Un petit exemple qui regroupe tout les cas :

    <?xml version="1.0" encoding="UTF-8"?>
    <configdata>
        <nav>
            <home>
                <label>nav_home</label>
                <controller>index</controller>
            </home>
            <page2>
                <label>Page 2</label>
                <module>mon_module</module>
                <controller>mon_controller</controller>
                <action>mon_action</action>
            </home>
            <home>
                <label>nav_home</label>
                <uri>/foo/bar/</uri>
            </home>
        </nav>
    </configdata>

## Bootstrap {#bootstrap}

Il faut rajouter une méthode a notre **bootstrap** :

    /**
     * @return Zend_Navigation
     */
    protected function _initNavigation() {
        $view =
        $this->bootstrap('layout')->getResource('layout')->getView();
        $config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav');
        $view->navigation(new Zend_Navigation($config));
    }


La seule chose a retenir ici est le premier argument de la méthode
**Zend_Config_Xml** : c’est le chemin de notre **fichier de navigation** et le
deuxième argument représente la balise mère de notre `section de navigation`
dans le fichier `xml`. Voilà, on a notre fonction de navigation disponible dans
notre application. Il suffit maintenant de l’utiliser.

## Layout {#layout}

Histoire de faire le plus simple possible, on va ajouter notre barre de
navigation directement dans le `layout`, histoire de le retrouver sur toutes les
pages. Il suffit de rajouter ce bout de code dans le fichier `layout.phtml` à
l’endroit ou vous voulez que le menu s’affiche :

    <?php echo $this->navigation()->menu(); ?>

## Hiérarchie de pages {#hierarchie}

On va voir maintenant un exemple de fichier xml ou il y a plusieurs pages et
sous pages <img class="alignright size-full wp-image-735" title="navigation-
Menu" src="{{ relativeRoot }}/wp-content/uploads/2009/10/navigation-Menu.png"
alt="navigation-Menu" width="208" height="156" />

    <?xml version="1.0" encoding="UTF-8"?>
    <configdata>
    <nav>
        <home>
            <label>nav_home</label>
            <controller>index</controller>
            <pages>
                <add>
                    <label>nav_fact_add</label>
                    <controller>fact</controller>
                    <action>add</action>
                </add>
                <list>
                    <label>nav_fact_list</label>
                    <controller>fact</controller>
                </list>
                <calc>
                    <label>nav_calc</label>
                    <controller>calc</controller>
                </calc>
                <user>
                    <label>nav_user</label>
                    <controller>user</controller>
                </user>
                <stats>
                    <label>nav_sats</label>
                    <controller>stats</controller>
                </stats>
            </pages>
        </home>
    </nav>
    </configdata>

Voilà, **la balise a retenir **est la balise `pages` qui permet de définir des
sous pages ou sous menu. Et bien sur on peut en imbriquer autant qu’on veut.
Enfin il faut savoir que le code `html` des liens sur la page suit une
hiérarchie de balise `ul/li`.

## Classe css de la page en cours {#css}

Comment ajouter une classe au `lien` qui pointe vers **la page en cours** ? Et
bien il suffit de rajouter ce bout de code en haut de chaque `controller` (dans
la méthode `init`) :

    public function init()
    {
        /` Initialize action controller here `/
        $activeNav =
        $this->view->navigation()->findByController('index');
        $activeNav->active = true;
        $activeNav->setClass("active");
    }

Et voilà maintenant quand on est sur une page du `controller` `index` (dans ce
cas la) et bien le lien aura (entre autre) la classe `active`. Il suffit après
de toucher au `css` pour ajouter un petit effet ;)

Vous en voulez encore plus ?

## Fil de Navigation ou **breadcrumbs** {#fil}

<img class="alignright size-full wp-image-736" title="navigation-fil" src="{{
relativeRoot }}/wp-content/uploads/2009/10/navigation-fil.png" alt="navigation-
fil" width="296" height="41" />

Et bien avec `Zend_Navigation `on peut retracer notre **chemin de navigation**,
qui soit dit en passant est très bon en terme d’ergonomie, mais aussi en terme
de référencement. Donc je vous invite vraiment à le mettre en place. Encore une
fois, il suffit de rajouter une ligne de `php` dans le fichier `layout.phtml` :

    <?php echo $this->navigation()->breadcrumbs()->setMinDepth(0)->setLinkLast(true)->setSeparator(" >> "); ?>

Alors on décortique le bousin :

* `$this->navigation()->breadcrumbs()` va afficher le **fil de navigation**.
* `->setMinDepth(0)` va définir si il faut ou pas afficher ce fil ; il est
en accord direct avec la profondeur du `xml`.
* `->setLinkLast(true)` définit si il faut ou pas mettre un liens sur le
dernier fils du fil de navigation qui correspond à la page en cours
* `->setSeparator( » >> « )` définit par quelle chaine de caractère
est séparer notre liste de liens.

Voilà, comme on a pu voir, le plus dur est de faire le fichier `xml` ainsi que
le `css` des menus. Après c’est un jeux d’enfant pour avoir de beau menu ;)

[1]: #sommaire
[2]: #intro
[3]: #bootstrap
[4]: #layout
[5]: #hierarchie
[6]: #css
[7]: #fil
