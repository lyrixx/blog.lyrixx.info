---
title: Mysql + export + csv + groupy en gardant les colonnes groupées
author: Greg
layout: post
permalink: /web-dev/mysql-export-csv-groupy-en-gardant-les-colonnes-groupees/
tags:
  - Mysql
---

Petit tips avec MySQL. Admettons que vous voulez faire un export en csv d’une
table (A). Dans ce cas, c’est facile, il suffit d’utiliser « INTO OUTFILE ». Par
contre, si vous avez une jointure (A -> B), c’est plus compliqué.

Il y a plusieurs solutions : soit on se retrouve avec plusieurs ligne de la
table A en double, puis à « droite » de ces lignes , les colonnes de la table B.

Soit on utilise la fonction [group_concat][1]; et un petit hack ;)

Exemple :

    SELECT c.id, c.first_name, c.last_name, GROUP_CONCAT(CONCAT(p.bar_code, '||', p.quantity, '||', p.date) SEPARATOR "||")
        FROM contact c
        LEFT JOIN product p on (c.id = p.contact_id)
        GROUP BY c.id
        INTO OUTFILE '/tmp/export.csv' FIELDS TERMINATED BY ';' OPTIONALLY ENCLOSED BY '"'
    ;


On fait notre jointure normalement, on group by sur c.id, et c’est dans le
select qu’il y a une peu de magie. On va d’abord concaténer les attributs de la
table « product » avec comme séparateur deux pipes « || ». Et ensuite, on va
concaténer toutes les valeurs résultantes de la fonction GROUP BY.

Enfin, il va falloir remplacer tous nos doubles pipes (`||`) par `;` qui
correspond a nos délimiteurs de champs.

    sed -i 's/||/";"/g' /tmp/export.csv

Et voila, vous avec un beau fichier CSV bien formaté ;) Ici le hack c’est de
remplacer un délimiteur un peu spécial « || » par un vrai délimiteur, a
posteriori.

P.S. : Il se peut que mysql exporte le fichier csv avec l’utilisateur mysql.
Vous n’aurez donc pas les droits pour le lire et/ou le modifier. Il faut donc
passer en root ;).

[1]: dev.mysql.com/doc/refman/5.0/fr/group-by-functions.html#id441997
