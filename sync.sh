#!/bin/bash

BASE=`dirname $0`/web

rsync \
     -azCcv --delete \
     --exclude=.ssh \
     --exclude=_carew \
     $BASE blog.lyrixx.info@lyrixx.info:
