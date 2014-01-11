---
title: Symfony2 en vidéo
author: Greg
layout: post
permalink: /symfony/symfony2-en-video.html
tags:
  - gource
  - Symfony2
---

Voici l’évolution du code source de <a href="http://symfony.com/"
target="_blank">symfony2</a> réalisé grâce à <a
href="http://code.google.com/p/gource/" target="_blank">gource</a>, ffmpef,
pitivi et ubuntu ;)

<iframe width="853" height="480" src="http://www.youtube.com/embed/164Z1gyqk6M" frameborder="0" allowfullscreen></iframe>

(Il est possible de voir la vidéo en HD)

La ligne de commande qui a permit le résultat:

    gource --title "Symfony2" -s .25 --file-idle-time 600  --auto-skip-seconds 1 -1920x1080 --output-framerate 30 --stop-at-end --max-files 1000000 --bloom- multiplier 1.1 --bloom-intensity .25 --background 101010  --highlight-dirs --highlight-users --hide filenames,date,progress --output-ppm-stream - | nice -n19 ffmpeg -y -b 3000k -r 30 -f image2pipe -vcodec ppm -i - -vcodec libx264 -vpre hq -crf 22 -threads 0 symfony.avi
