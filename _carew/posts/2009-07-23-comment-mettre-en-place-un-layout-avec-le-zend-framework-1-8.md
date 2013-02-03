---
title: Comment mettre en place un Layout avec le Zend-Framework 1.8
author: Greg
layout: post
permalink: /zend/comment-mettre-en-place-un-layout-avec-le-zend-framework-1-8/
tags:
  - Zend Framework
---

On va voir ensemble comment utiliser les **Layouts**, **placeholder**,
**helper** dans le** Zend Framework** 1.8 (et supérieur). En effet, depuis la
version 1.8, la gestion n’est plus automatique, mais en 2 temps 3 mouvements
c’est réglé.

## Qu’est ce qu’un Layout ?

Un `layout` est un template, ou alors un squelette vide de votre site. Il n’y a
aucun contenu, il n’y a presque que du code HTML. Les layouts sont très
pratique, car ils permettent de ne pas avoir copier / coller tout le code html
identique sur chaque pages. On peut donc faire des `templates` par defaut pour
un grand nombre de page.

> Dans tous les cas, un script de **layout** est nécessaire. Les scripts de
**layout** utilisent simplement Zend_View (ou une implémentation particulière
personnalisée). Les variables de **layout** sont enregistrées dans le
**placeholder** **Layout**, et peuvent être accédées via l’aide de vue
**placeholder** ou directement en tant que propriétés de l’objet **layout**.

<p style="text-align: right;">
    D’après la
    <a href="http://framework.zend.com/manual/fr/zend.layout.quickstart.html" target="_blank">
        documentation de Zend Framework
    </a>
</p>

## Sommaire {#sommaire}

* [Sommaire][1]
* [Exemple de layout ou template général][2]
* [Mise en place du Layout][3]
* [Comprendre les placeholders et les helpers][4]
* [C’est quoi un helpers ou Script de Vue ?][5]
* [C’est quoi un placeholders ?][6]
* [Comment Utiliser un placeholders ?][7]
* [Comment faire pour changer le titre de ma page ?][8]
* [Explication : Implementation d’un placeholders dans un helper][9]

## Exemple de layout ou template général. {#exemple}

Voici un exemple de `template` :

<a href="{{ relativeRoot }}/wp-content/uploads/2009/07/Exemple-de-template.png"
rel="lightbox[562]"><img class="size-medium wp-image-566" title="Exemple de
template" src="{{ relativeRoot }}/wp-content/uploads/2009/07/Exemple-de-
template-300x178.png" alt="Exemple de template" width="300" height="178" /></a>

On voit qu’on a diviser notre gabarit de page en quatre zones (complètement
arbitrairement) :

* En haut : le Header
* En bas : le Footer
* A gauche : le menu de navigation
* A droite : le contenu

Voici ce qu’est un `layout` : **le layout c’est la partie qui ne change pas :
Header, Footer, Menu.**

## Mise en place du Layout. {#layout}

Il faut commencer par choisir un emplacement pour les fichiers. Moi j’ai choisit
`APPLICATION_PATH "/layouts/scripts"` (par défaut, cf la doc de Zend). Dans ce
dossier on va créer notre premier `layout`, qu’on appellera : layouts.phtml.
L’extension `.phtml` est l’extension standard pour les `layouts` dans zend.

Une fois ce fichier créé, il faut renseigner à zend qu’on va utiliser un
`layout`. On va donc ajouter au fichier de configuration `application.ini` ces
quelques lignes :

    LAYOUT
    resources.layout.layout = "layout"
    resources.layout.layoutPath = APPLICATION_PATH "/layouts/scripts"

Grâce a ces deux lignes, Zend va automatiquement chercher le script
`/layouts/scripts/layout.phtml` et l’utiliser. A partir de ce moment, le script
de vues devient plus leger, avec seulement un partie du code html. Donc pour
**résumer** : Dans le `layout`, il y a beaucoup de HTML (balise html, head,
title, body) et dans les vues un peu plus de php et peu moins de html. OK ? Si
vous avez un doute [sur les `view` (vue) c’est ici][10], et sur [les
`controllers` c’est la][11].

Maintenant on peut encore plus customiser (personnaliser)  notre `layout`. Comme
lui mettre une titre par défaut, choisir son encodage, choisir son doc type etc…
Tout ce fait dans le bootstrap :

    /**
     * Initialize session
     *
     * @return Zend_View
     */
    protected function _initView()
    {
        // Initialize view
        $view = new Zend_View();
        $view->doctype('XHTML1_STRICT');
        $view->headTitle('Ma premiere application avec Zend');
        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8');
        // Add it to the ViewRenderer
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);
        // Return it, so that it can be stored by the bootstrap

        return $view;
    }

Enfin il faut faire le fichier layouts.phtml

    <?php echo $this->doctype() ?>
    <html>
        <head>
            <?php echo $this->headTitle() ?>
            <?php echo $this->headLink() ?>
            <?php echo $this->headStyle() ?>
            <?php echo $this->headScript() ?>
            <?php echo $this->headMeta() ?>
        </head>
        <body>
            <?php echo $this->layout()->content ?><br />
        </body>
    </html>

Voila on a notre Layout en place. Maintenant, il s’agit de comprendre comment il
fonctionne.

## Comprendre les placeholders et les helpers. {#comprendre}

Si vous avez bien suivi le Tutos, vous avez vu que dans le bootstrap on a déjà
mis des valeurs par défault. Ces valeurs, normalement c’est la vue (view) qui
les passe aux layouts. Dans notre cas, le `bootstrap` crée en avance le `layout`
(je rappel que presque tout est objet). Néanmoins il reste bien évidemment
possible de changer ces valeurs.

### C’est quoi un helpers ou Script de Vue ? {#helper}

D’après la documentation de Zend :

> Dans vos scripts de vue, il est souvent nécessaire d’effectuer certaines
actions complexes encore et encore : par exemple, formater une date, générer des
éléments de formulaire, afficher des liens d’action. Vous pouvez utiliser des
classes d’aide pour effectuer ce genre de tâches.

Petite traduction : un `helper` va être une classe que l’on va utiliser souvent
dans les vues. Il évite les taches répétitives. Par exemple une zone pour savoir
si l’utilisateur est loggué. Par contre la je vais vous laisser voir les exemple
directement sur la <a href="http://framework.zend.com/manual/fr/zend.view.helpers.html"
target="_self">doc de Zend</a>. D’ailleur on verra prochainement comment faire
un helper pour vérifier si la personne est loggué.

### C’est quoi un placeholders ? {#placeholder}

D’après la documentation de zend :

> L’aide de vue `Placeholder` est utilisé pour faire persister le contenu entre
les scripts de vues et les instances de vues. Il offre aussi des fonctionnalités
utiles comme l’agrégation de contenu, la capture de contenu de scripts de vues
pour une utilisation ultérieure, et l’ajout de texte pré ou post contenu (et la
personnalisation des séparateurs de contenu).

Petite traduction. Les **Placeholders** sont des genres de variables. On les
utilises pour faire passer du code HTML mis en forme (la pluspart du temps)
depuis la `vue` vers le `layout`. Vu que le `layout` est générique, il faut
savoir rester général sur les noms des `placeholders`. Enfin `les placeholders`
sont persistants : c’est a dire qu’on peut les utilisers dans une première
`vue`, puis dans une autre `vue` ou dans un autre script…

### Comment Utiliser un placeholders ? {#useplaceholder}

Dans le `layout`, le `placeholder` s’utilise de cette façon  :

    <?php echo $this->placeholder('PH1');?><br />
    <?php echo $this->placeholder('PH2');?><br />
    <?php echo $this->placeholder('PH3');?><br />

Pour le layout, il n’y a pas 15 000 facon de les utiliser. Par contre dans une
vue, on peut les utilisers de plusieurs facon :

* Comme une variable :

        $this->placeholder('PH1')->set("j'utilise mon placeholder PH1");

* Comme un agrégateur de variables, avec plein d’outils mis a disposition.

        $this->placeholder('PH2')->exchangeArray(array(1,2,3));
        $this->placeholder('PH2')->setPrefix("<ul>n <li>")
             ->setSeparator("</li><li>n")
             ->setIndent(4)
             ->setPostfix("</li></ul>n");

La on decoupe notre tableau gràce a la fonction `exchangeArray`. Puis on rajoute
des balise html avant et après. Le code parle de lui même.

* Comme un agrégateur de contenu

        <?php $this->placeholder('PH3')->captureStart(); ?>
            La on utilise notre<br>
            Placeholders PH3<br>
            qui est sur plusieurs lignes <br />
            Et qui contient des balises html, ca ne dérange pas.
        <?php  $this->placeholder('PH3')->captureEnd(); ?>

La on utilise des méthodes qui vont commencer la capture puis terminer la
capture. Tout ce qui sera entre ces deux méthodes seront contenue par le
`placeholder`.

Enfin il faut savoir qu’il existe un bon nombre de configurations pour les
`placeholders`. Je vous conseil de regarder la doc. On peut y apprendre a
identer le code, ajouter des préfix, suffix etc…

### Comment faire pour changer le titre de ma page ? {#changetitle}

Voila le code, je vous l’explique après :

    $this->headTitle('Mon nouveau titre','SET');

### Explication : Implementation d’un placeholders dans un helper {#explication}

Alors la je vous est perdu sur le titre. en fait ce n’est que pour les puristes
et ceux qui veulent tout savoir que je l’ai ecrit comme ca. **Sinon la on met
met juste le titre de notre page en argument** de la méthode `headTitle`. Mais
d’ou vient ce `« SET »`‘. Et bien sans SET, par défault les méthodes head\**|
ajoute le code a la fin de ce qu’il y a deja. Ce qui est extremement pratique
pour les CSS et JS (cf la prochaine note). Mais on peut aussi écraser la valeur
du titre, ajouter le nouveau avant ou après etc… Alors pourquoi « Implementation
d’un placeholders dans un helper ». Et bien en fait dans zend il existe déjà
beaucoup de helper (comme vu plus haut), vous les utiliser mais vous ne le savez
peut etre pas. Ensuite, headtitle par exemple est un placeholders, certe
particulier, mais c’en est un. Voila vous savez tout !!

<br / ><br / >

[1]: #sommaire
[2]: #exemple
[3]: #layout
[4]: #comprendre
[5]: #helper
[6]: #placeholder
[7]: #useplaceholder
[8]: #changetitle
[9]: #explication
[10]: {{ relativeRoot }}/zend/zend-comment-faire-une-vue.html
[11]: {{ relativeRoot }}/zend/zend-quest-ce-quun-controller.html
