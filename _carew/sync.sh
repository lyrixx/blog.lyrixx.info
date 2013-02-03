#!/bin/bash

BASE=`dirname $0`

rsync \
     -azCcv --delete \
     --exclude=.ssh \
     --exclude=_carew \
     $BASE/.. blog.lyrixx.info@lyrixx.info:
