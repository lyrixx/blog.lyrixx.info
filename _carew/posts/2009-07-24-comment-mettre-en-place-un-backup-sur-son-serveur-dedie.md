---
title: Comment mettre en place un backup sur son serveur dedié ?
author: Greg
layout: post
permalink: /ubuntu/comment-mettre-en-place-un-backup-sur-son-serveur-dedie/
tags:
  - Backup
---

Comment mettre en place un **système de backup automatique sur son serveur
dédié** qui tourne sous linux ? Et bien rien de bien compliqué, il suffit de
lire ce qui va suivre pour **s'auvegarder les dossiers que vous voulez** :
**/home**,  les svn, **les bases de données** etc ...

Il y a un petit outils qui va quasiment faire tout le travail pour nous :
backup-manager <a href="http://doc.ubuntu-fr.org/backup-manager"
target="_blank"> Tuto sur le site d'Ubuntu-Fr</a>. Je me suis insipiré de ce
tuto pour ecrire celui que vous etes en train de lire.

Pour utiliser ce logiciel, et facon plus général, il vaut mieux etre en root
pour effectuer toute le prochaine opération. on passe donc en root :

    sudo su

## Comment sauvegarder les répertoires ?

Alors on commence par installer backup manager :

    apt-get install backup-manager

Ensuite il faut répondre aux différente questions (de tête, je ne m’en souvient
plus) :

* Emplacement du répertoire de sauvergarde
* Emplacement des repertoires a sauvegarder
* Utilisateur qui sauvergarde
* Droit des fichiers sauvegarder

Si vous ne savez pas quoi mettre laissez les options par default. Par contre si
vous voulez modifier ces options, on peut refaire la configuration, de facon
plus complète avec plus d’options :

    dpkg-reconfigure backup-manager

Allez on y va ensemble : donc on saisi :

1.  Le chemin du dossier qui recevra les archives
2.  L’utilisateur propriétaire du dossier
3.  Son groupe
4.  Le type de compression
5.  La fréquence des sauvegarde
6.  Si il faut ou pas suivre les liens symbolique. Laisser a non sauf si
vous etes vraiment sur de vous.
7.  Le format de nommage des archives
8.  La durée de vie des archives : la ça sert a rien de garder les archives
pendant plus d’une semaine…
9.  Les répertoires à sauvegarder.
10. Les répertoires à exclure (oui oui celui ou y’a des vidéos nsfw)
11. Si il faut ou non chiffre les archives, normalement y’en a pas
besoin. Sauf si la sauvegarde est expédié
sur le serveur d’un copain.
12. Si il faut envoyer les archives sur un autre serveur (ftp ssh). Je n’ai
pas d’autre serveur, donc non.
13. Si il faut graver un cd. Je suis sur un dédié qui est 300km de chez moi,
donc non

Voila, donc a ce moment la il ne nous sauvegarde que nos repertoire /home et
/etc si vous avez laisser les options par default.

## Comment sauvegarder les bases de données ?

Mais je veux qu’il me sauvegarde aussi mes bases de donnée : Pour ce faire il
suffit d’éditer un fichier :

    nano /etc/backup-manager.conf

Et de reperer les lignes suivante et les modifier :

    # If you don't want to use any backup method (you don't want to
    # build archives) then choose "none"

    # Version de base

    #export BM_ARCHIVE_METHOD="tarball mysql"

    #Pour la gestion de la base de donnée

    export BM_ARCHIVE_METHOD="tarball mysql"

    ## [...] ## => la suite est vers la fin du fichier. Il suffit de renseigner les déférents champs.

    ##############################################################
    # Backup method: MYSQl
    #############################################################

    # This method is dedicated to MySQL databases.
    # You should not use the tarball method for backing up database
    # directories or you may have corrupted archives.
    # Enter here the list of databases to backup.
    # Wildcard: __ALL__ (will dump all the databases in one archive)
    export BM_MYSQL_DATABASES="__ALL__"

    # The best way to produce MySQL dump is done by using the "--opt" switch
    # of mysqldump. This make the dump directly usable with mysql (add the drop table
    # statements), lock the tables during the dump and other things.
    # This is recommended for full-clean-safe backups, but needs a
    # privileged user (for the lock permissions).
    export BM_MYSQL_SAFEDUMPS="true"

    # The user who is allowed to read every databases filled in BM_MYSQL_DATABASES
    export BM_MYSQL_ADMINLOGIN="root"

    # its password
    export BM_MYSQL_ADMINPASS=""

    # the host where the database is
    export BM_MYSQL_HOST="localhost"

    # the port where MySQL listen to on the host
    export BM_MYSQL_PORT="3306"

    # which compression format to use? (gzip or bzip2)
    export BM_MYSQL_FILETYPE="bzip2"

Et voila, on a maintenant un **backup de notre serveur** dans le dossier
*/var/archives/`. Il faut savoir qu’on peut aussi sauvegarder des svn. et qu’on
peut exporter le tout sur un autre serveur par exemple. Les protocoles supporter
sont :

* scp
* ssh-gpg
* ftp
* rsync
* s3

## Comment je recupere toutes les archives ?

Comme je vous l’ai dit, je n’ai pas d’autre serveur sous la main. Donc je me
suis fait un petit script (.sh) que je peux lancer a la main :

    #!/bin/bash
    ssh root@SERVEUR "tar -c /var/archives/`" > /CHEMIN/VERS/LE/DOSSIER/DE/RECEPTION/archives-$(date +%d%m%Y).tar

et voila on a plus qu’a rendre ce script executable et a l’exécuter, et le tour
et jouer :

    chmod +x getBackup.sh
    ./getBackup.sh

Et voila le tour est jouer. on a notre serveur qui fait des backups tout les
jours, et vous du coté client il n’y a plus qu’a exécuter le script tous les
jours...
