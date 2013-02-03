---
title: Lister ses films
author: Greg
layout: post
permalink: /ubuntu/lister-ses-films/
tags:
  - Bash

---

Un petit script bash (donc pour linux/mac) pour lister tous les films qui
peuvent trainer dans plusieurs répertoires et sous répertoires…

    wget https://gist.github.com/raw/1008082/dc6cdb8ae50bd981ae9458cc270f96de41583718/movieList.sh
    chmod +x movieList.sh
    # Configure VIDEO_FOLDER
    ./movieList.sh
    cat movie_list.txt

Script :

    #!/bin/bash

    VIDEO_FOLDER=(
      /PATH/TO/MOVIE1
      /PATH/TO/MOVIE2
    )
    VIDEO_EXTENTION=(avi mkv mpg mpeg)

    OUTPUT='movie_list.txt'
    OUTPUT_TEMP=${OUTPUT}.temp

    `>$OUTPUT_TEMP`

    for folder in "${VIDEO_FOLDER[@]}"
    do
      if [ -d $folder ]; then
          for ext in "${VIDEO_EXTENTION[@]}"
          do
            find $folder -iname "*.$ext" >> $OUTPUT_TEMP
          done
      fi
    done

    `cat $OUTPUT_TEMP | awk -F "/" '{print $NF;}' | sort | uniq > $OUTPUT`
    `rm $OUTPUT_TEMP`
