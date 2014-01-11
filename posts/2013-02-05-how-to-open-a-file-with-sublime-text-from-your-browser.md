---
layout: post
title:  How to open a file with sublime text from your browser
tags:
    - Sublime text 2
    - Xdebug
---

**Note:** This work only with ubuntu.

Why?
----

If you are doing some **php with sublime text 2**, this tips will enjoy your
day. You can configure your system (ubuntu, may be other other distro too) to
handle links like `subl:///tmp/file.txt?10` to open the file `/tmp/file.txt`
with sublime text 2 at the line `10`. To do that, you have to register a new
**protocol handler**, and to configure **xdebug**.

When did it happend ? When a fatal error or an exception is throwed by php:

![Xdebug]({{ carew.relativeRoot }}/images/xdebug.png)

With this tip `/opt/dotefiles/www/fail.php` is clickable.

**Note:** If you are using symfony2 all files in an exception page, or the web
developper toolbar will be clickable!!!

How?
----

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

    #!/usr/bin/env bash
    request=${1:7}
    sublime-text $request

You should run:

    sudo chmod +x /usr/local/bin/xdebug-urlhandler

And now you have to configure xdebug `/etc/php5/conf.d/90-my.ini`:

    [PHP]
    xdebug.file_link_format=subl://%f:%l

Thinks to restart `apache` or `php-fpm`. **Enjoy**

[Source](https://gist.github.com/svizion/3654834)
