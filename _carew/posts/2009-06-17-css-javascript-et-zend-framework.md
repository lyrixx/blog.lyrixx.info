---
title: Css, JavaScript et Zend Framework
author: Greg
layout: post
permalink: /zend/css-javascript-et-zend-framework/
tags:
  - Zend Framework
---

Comment lier une feuille  de style CSS ou un fichier javascript proprement dans
une application utilisant le Framework Zend ?

Etant donnée qu’on est dans un contexte model vue controller, et que le dossier
public (qui contient les fichier css et js) est à un endroit totalement inconnu
du reste de l’application (en MVC) on va devoir utiliser des helper. Ce sont des
scripts php qui peuvent être utilisé a plusieurs endroit. Dans notre cas, on
utilisera le helper dans le layout, qui definit notre page principale. C’est un
peu le template du site.


On va creer un `View Helper` dans le dossier `application/default/views/helpers`
:

    class Zend_View_Helper_BaseUrl
    {
        function baseUrl()
        {
            $fc = Zend_Controller_Front::getInstance();

            return $fc->getBaseUrl();
        }
    }

Grâce a ce `Helper`, on pourra avoir la base de l’url de notre application. Donc
maintenant il ne reste plus qu’a s’en servir dans le layout principale. On va
donc copier notre fichier css dans le répertoire  `./public/styles/`:

    *{outline:none;margin:0;padding:0;}
    html{border:15px solid #f6f6f6;border-left:30px solid #f6f6f6;border-right:30px  solid #f6f6f6;}
    body{background:#fff;font:16px/1.95em "Lucida Grande","Lucida Unicode",geneva,verdana,sans-serif;color:#666;padding-top:20px;border:3px solid #eee;margin:0;}
    #header{width:100%;margin:0 auto;padding:20px 0;}
    #header .right{float:right;}
    #header h1{font:290% Times New Roman;letter-spacing:-2px;margin-bottom:5px;margin-left:35px;}
    #header h1 a{color:#000;text-decoration:none;}
    #header h1 a span{color:#ccc;}
    #header h1 a:hover span{color:#A3E800;}
    #header h3{font:80% Verdana;color:#999;display:inline;margin-left:35px;}
    ul#nav{background:#8EDF53;font-size:80%;border-top:3px solid #6CCC26;border-bottom:3px solid #6CCC26;font-weight:700;margin:45px 0 0;padding:10px;}
    ul#nav li{display:inline;list-style:none;margin-right:10px;}
    ul#nav li a{color:#fff;text-decoration:none;padding:10px 14px;}
    ul#nav li a:hover{background:#7EDB39;color:#fff;}
    ul#nav li a.active{background:#fff;color:#333;border-right:2px solid #6CCC26;border-bottom:2px solid #6CCC26;}
    #footer{margin-top:50px;clear:both;border-top:2px solid #eee;font:80% Verdana;line-height:25px;padding:20px 50px;}
    #footer a{color:#999;}
    #footer .validate{float:right;}
    #container{width:95%;margin:0 auto;}
    #content{width:100%;margin-bottom:50px;}
    #content ul{margin:15px 0 15px 25px;}
    #content ul li{border-bottom:1px solid #eee;color:#444;padding:10px;}
    #content img{background:#fff;padding:1px;border:0px;}
    #content h3{margin:20px 0;}
    #content p{line-height:30px;word-spacing:2px;margin:20px 0;}
    #content p a{color:#222;text-decoration:none;border-bottom:1px solid #ccc;}
    #content p a:hover{border-bottom:1px solid #aaa;}
    #content h2{font:190% Times;margin-top:45px;}
    #content h2 a{color:#333;text-decoration:none;}
    blockquote{background:#f6f6f6;border:1px solid #eee;font:100% Georgia;padding:0 10px;}
    table{width:100%;border:1px solid #eee;padding:10px;}
    th{color:#6CCC26;border-bottom:1px solid #eee;padding:5px;}
    td{text-align:center;background:#fafafa;padding:5px;}
    .formulaire{width:500px;margin-left:auto;margin-right:auto;border:solid 1px
    #c8c8c8;background-color:#fafafa;padding:10px 10px 0;}
    .formulaire fieldset{border:solid 1px #dcdcdc;margin:0 0 20px;padding:20px 0 0 !important;}
    .formulaire fieldset legend{color:#505050;font-weight:700;font-size:130%;margin:0 0 0 5px !important;padding:0 2px;}
    .formulaire label.left{float:left;width:200px;font-size:110%;margin:0 0 0 10px;padding:2px;}
    .formulaire select.combo{width:175px;border:solid 1px #c8c8c8;font-family:verdana,arial,sans-serif;font-size:110%;padding:2px;}
    .formulaire input.field{width:275px;border:solid 1px #c8c8c8;font-family:verdana,arial,sans-serif;font-size:110%;padding:2px;}
    .formulaire textarea{width:275px;height:250px;border:solid 1px #c8c8c8;font-family:verdana,arial,sans-serif;font-size:110%;padding:2px;}
    .formulaire input.button{float:right;width:9em;margin-right:20px;background:#e6e6e6;border:solid 1px #969696;text-align:center;font-family:verdana,arial,sans-serif;color:#969696;font-size:110%;padding:1px !important;}
    .formulaire input.button:hover{cursor:pointer;border:solid 1px #505050;background:#dcdcdc;color:#505050;}
    .info_msg_class{border:1px solid #000;background:#fafafa;text-align:center;padding:10px;}
    .button1 a{margin:0 3px;padding:2px 5px;text-decoration:none;background:#eee;border:solid 1px #aaa;color:#aaa;text-align:center;}
    .button1 a:hover{cursor:pointer;border:solid 1px #555;background:#dcdcdc;color:#555;}
    input.calendar {width:150px;border:solid 1px #c8c8c8;font-family:verdana,arial,sans-serif;font-size:110%;padding:2px; float: left;}
    button.calendar {float: left;width: 24px;height: 24px;border: 0;margin-left: 10px;cursor: pointer;background: url('../img/calendar.png');}
    button.calendar:hover , button.calendar.active {background-position: left bottom;}
    div.calendar{background:#eee;}
    .fact_option{width:60px;}
    .user_option{width:40px;}
    .nowrap{white-space:nowrap}
    .barrer > td{text-decoration:line-through;}
    .change_payed {border-bottom:2px dotted #6CCC26}
    .change_payed2 {white-space:nowrap;}
    .calcul{margin:1px 5px;padding:2px 5px;background:#e6e6e6;border:outset 1px #969696;color:#969696;}
    .calcul:hover{cursor:pointer;border:inset 1px #505050;background:#dcdcdc;color:#505050;}

Puis maintenant il faut editer le fichier
`application/default/layouts/main.phtml` qui est le **template** par default et
on ajoute la notre** feuille de style** :

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

Voila maintenant il suffit de faire la meme chose pour les fichiers
**JavaScipts** On peut donc passer a la suite avec : [Comment mettre en place un
layout ou template avec Zend Framework][1]

[1]: {{ relativeRoot }}/zend/zend-comment-utiliser-un-layout.html "Zend et les layouts"
