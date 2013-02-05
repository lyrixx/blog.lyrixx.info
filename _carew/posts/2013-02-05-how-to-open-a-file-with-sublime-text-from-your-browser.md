---
layout: post
title:  How to open a file with sublime text from your browser
tags:
    - Sublime text 2
    - Xdebug
---

**Note:** This work only with ubuntu.

If you are doing some **php with sublime text 2**, this tips will enjoy your
day. You can configure your system (ubuntu, may other other distro) to handle
links like `subl:///tmp/file.txt?10` to open the file `/tmp/file.txt` with
sublime text 2 at the line `10`. To do that, you have to register a new
**protocol handler**, and to configure **xdebug**

Start by editing `~/.local/share/applications/mimeapps.list`:

    [Added Associations]
    # ...
    x-scheme-handler/subl=subl-urlhandler.desktop

Then create the file `/usr/share/applications/subl-urlhandler.desktop`. (This is
a fake application):

    [Desktop Entry]
    Version=1.0
    Name=Sublime Text 2
    Name[en_PH]=Sublime Text 2
    Exec=xdebug-urlhandler %u
    Icon=/opt/subl/Icon/48x48/sublime_text.png
    Terminal=false
    Type=Application
    Categories=Development;
    StartupNotify=true
    MimeType=x-scheme-handler/subl;

And then create the file `/usr/local/bin/xdebug-urlhandler`:

    #!/bin/bash

    url=$1
    file=${url#subl*//}
    path=`echo $file | cut -d? -f 1`
    line=`echo $file | cut -d? -f 2`
    sublime-text $path:$line

You should run:

    sudo chmod +x /usr/local/bin/xdebug-urlhandler

And now you have to configure xdebug `/etc/php5/conf.d/90-my.ini`:

    [PHP]
    xdebug.file_link_format=subl://%f?%l

Thinks to restart `apache` or `php-fpm`. **Enjoy**

[Source](https://gist.github.com/svizion/3654834)
