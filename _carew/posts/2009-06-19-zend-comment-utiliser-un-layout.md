---
title: 'Zend : Comment utiliser un layout ?'
author: Greg
layout: post
permalink: /zend/zend-comment-utiliser-un-layout/
tags:
  - Zend Framework
---
### Comment utiliser un seul layout dans le Zend Framework ?

On va continuer d’utiliser la base qu’on avait sur la gestion de compte en
collocation. On va donc reprendre le code de ce script et l’adapté a Zend. Dans
le script de base, on avait deux fichiers : un header (header.php) et un footer
(footer.php). On va donc pouvoir coller le code ou il faut dans le fichier
main.phtml.

De base ce fichier ressemble a ca :

    <?php

    /**
     * Default Layout
     *
     * @author
     * @version
     */

    echo '<?xml version="1.0" encoding="UTF-8" ?>';
    echo $this->doctype()
    ?>

    <html>

        <head>
            <meta http-equiv="Content-Type" content="text/html;
            charset=UTF-8" />
            <link rel="stylesheet" type="text/css" href="<?php echo
            $this->baseUrl();?>/styles/design.css" />
            <?php
            echo $this->headTitle();
            echo $this->headScript();
            echo $this->headStyle();
            ?>
        </head>

        <body>
            <h1><?php echo $this->placeholder('title')
            ?></h1>
            <?php echo $this->layout()->content ?>

            <br />
            <br />
        </body>

    </html>

On va donc rajouter le header entre la balise body et la balise h1 et le footer
juste avant la fin de body, en prenant soin d’enlever les balises en double.
Cependant, on ne prend que le code de la partie après la balise body, zend
s’occupant du reste. De plus on voit qu’il y a du code php dans le header, on
n’en a pas encore besoin, on ne le copie pas encore. on obtient alors ce fichier
:

    <?php

    /**
     * Default Layout
     *
     * @author
     * @version
     */

    $temps_debut = microtime(true);
    echo '<?xml version="1.0" encoding="UTF-8" ?>';
    echo $this->doctype()
    ?>

    <html>

        <head>
            <meta http-equiv="Content-Type" content="text/html;
            charset=UTF-8" />
            <link rel="stylesheet" type="text/css" href="<?php echo
            $this->baseUrl();?>/styles/design.css" />
            <?php
            echo $this->headTitle();
            echo $this->headScript();
            echo $this->headStyle();
            ?>
        </head>

        <body>
            <div id="header">
                <h1><a href="#">Gestion de
                compte</a></h1>
                <ul id="nav">
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="add_facture.php">Ajouter une facture</a></li>
                    <li><a href="list_facture.php">Listes des factures</a></li>
                    <li><a href="calcul.php">Calculer</a></li>
                    <li><a href="user.php">Utilisateurs</a></li>
                    <li><a href="stats.php">Stats</a></li>
                </ul>
            </div>

            <div id="container">
                <div id="content">
                <h1><?php echo $this->placeholder('title')
                ?></h1>
                <?php echo $this->layout()->content ?>
                </div>
            </div>

            <div id="footer">
                <p class="validate"><a href="http://validator.w3.org/check?uri=referer">XHTML</a>
                |
                <a href="http://jigsaw.w3.org/css-validator/">CSS</a>
                | <a href="#content">Top</a></p>
                <p><a href="www.lyrixx.info">© Pineau Gr&eacute;goire</a></p>
                <p>Temps de chargement : <?php $temps_fin = microtime(true); echo round($temps_fin - $temps_debut, 3);?> secondes | Nombre de requ&egrave;tes sql : <?php echo $nb_req?>
                </p>
            </div>

            <br />
            <br />
        </body>

    </html>

Voila on a notre layout tout prêt. Cependant les liens ne fonctionnent pas
encore. C’est normale, il n’y a pas encore de [controller][2] qui va s’occuper
de gerer tout ca.

[2]: {{ relativeRoot }}/zend/zend-quest-ce-quun-controller.html "Zend : Qu’est ce qu’un controller ?"
