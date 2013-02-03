---
title: Télécharger automatiquement des .torrent sur un PC Ubuntu distant
author: Arnaud
layout: post
permalink: /ubuntu/telecharger-automatiquement-des-torrent-sur-un-serveur-distant/
tags:
  - Download
  - Torrent
---

Il m’arrive souvent de vouloir télécharger quelque chose alors que je suis en
cours ou au boulot, mais c’est souvent difficile pour diverses raisons. Une
solution est d’utiliser un serveur @home et de le configurer pour qu’il
télécharge automatiquement des fichiers .torrent. Voici une façon de bidouiller
ça ;)

Pour commencez nous allons utiliser le client BitTorrent
<a title="Deluge" href="http://deluge-torrent.org/" target="_blank">Deluge</a>.

Pour l’installer :

* vistez le <a title="doc ubuntu fr sur deluge" href="http://doc.ubuntu-
fr.org/deluge" target="_blank">site de la doc ubuntu fr</a>
* ou si vous avez accès au dépot Universe : <a title="Installer Déluge"
href="apt://deluge-torrent" target="_blank">deluge-torrent</a>

Une fois installé, il faut lancer deluge

**Application > Internet > Deluge BitTorrent Client**

Aller dans

**Editer > Préférences**

Puis cocher la checkox correspondant à :

**Ajouter automatiquement les .torrent provenant de**

Enfin, choisir le dossier dans lequel seront ajoutés les fichiers .torrent que
l’on souhaite  télécharger (par exemple :  `/home/user/torrent`)

A partir de là il ne reste plus qu’à trouver un moyen d’envoyer  à distance nos
fichiers torrents dans le bon répertoire.  les moyens sont nombreux….On peut par
exemple  :

utilisez un **serveur FTP**

**Ou** utilisez le script suivant, puis éditer afin de changer la variable
**$destination_folder** en lui donnant le chemin absolu vers le répertoire de
deluge que l’on a choisi précedemment.

    <?php
        ini_set('display_errors', 1);
        error_reporting(-1);

        $destination_folder = '/home/arnaud/Torrent'; // A CHANGER

        $destination_folder = trim($destination_folder);
        while (strcasecmp(substr($destination_folder, strlen($destination_folder)-1,1),'/') == 0) {
            $destination_folder = substr($destination_folder,0,strlen($destination_folder)-1);
        }
        $extension = '.torrent';
        $file_tmp_name = 'tmp';

        $url = isset($_GET['url']) ? $_GET['url'] : null;

        $form = <<<EOL
    <div id="form">
        <form action="index.php" method="GET">
            <label>Entrez une URL : </label>
            <input type="text" name="url" value="'.$_['url'].'"/><br/>
            <input type="submit" name ="submit" value="Get it ! "/>
        </form>
    </div>
    EOL;
    ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
        <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <title>Remote Download Section</title>
                <style>
                    .spacer {width:100%; height:30px;}
                    #form {width:600px; border:1px solid black;}
                    form {padding-top:10px}
                </style>
            </head>
        <body>
            <center>
                <?php if (isset($url)) : ?>
                    <?php
                        while (file_exists($destination_folder.'/'.$file_tmp_name.$extension)) {
                            $file_tmp_name .= ''.rand(0,9);
                        }
                        $file_name = $file_tmp_name.$extension;
                        $result = copy($post,$destination_folder.'/'.$file_name);
                        if ($result === true) {
                            echo '<h1>Téléchargement du fichier réussi</h1>';
                        } else {
                             echo '<h1>Téléchargement du fichier échoué</h1><p>L\'url fournie n\'est peut être pas correcte</p>';
                             <?php echo $form ?>
                             echo '<p>Vous pouvez également utiliser une URL de la forme : http://monserveur/index.php?trt=lien_vers_le_fichier</p>';
                        }
                    ?>
                <?php else: ?>
                    <h1>Remote Download Section</h1>
                        <div class="spacer"></div>
                        <?php echo $form ?>
                <?php endif ?>
            </center>
        </body>
    </html>

Il faut que le script est le droit d’écrire dans le répertoire :

    chmod a+w mon_repertoire

Puis copier dans **/var/www/mondossier/**.

Et enfin ce script vous permettra de copier  les .torrent dans le bon répertoire
en tapant l’url dans votre navigateur favori :
`http://localhost/index.php?trt=url_du_.torrent` et de lancer ainsi le
téléchargement du torrent dans deluge !

La meilleur solution aurait été à mon avis un petit plugin qui permettrai
d’envoyer directement le .torrent depuis son navigateur internet.

Pensez également à  lancer automatiquement Déluge au démarrage du PC. Pour ce
faire allez dans **Système > Préférences > Applications au démarrage** , cliquez
sur ajouter et saisissez **/usr/bin/deluge** dans le champs « Commande » .

Voila, on peut maintenant télécharger son film du soir quand on y pense au
boulot :p
