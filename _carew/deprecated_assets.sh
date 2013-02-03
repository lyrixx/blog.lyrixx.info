#!/bin/bash

FILES=`find assets/ -type f -type f`

for f in $FILES ; do
    g=`basename $f`
    `grep -Riq $g posts/`
    if [ $? -ne 0 ] ; then
        echo delete: $f
        rm $f
    fi
done
