---
layout: post
title:  What my sismo configuration looks like?
---

[Sismo](http://sismo.sensiolabs.org/) is a Continuous Testing Server written in
PHP. I build all projects I'm working on with it.

It's very fast to set up a newproject, but it's a little but boring. So I want
to share my `~/.sismo/config.php` and a little script to set-up a new project.


    <?php

    // ~/.sismo/config.php

    Sismo\Project::setDefaultCommand('if [ -f composer.json ]; then composer install --dev; fi && phpunit');

    $notifier = new Sismo\Notifier\DBusNotifier();

    $sfCommand = <<<'EOL'
    cp app/config/parameters.yml-dist app/config/parameters.yml

    sed -i -e "s/database_user:.*/database_user: greg/" app/config/parameters.yml
    sed -i -e "s/database_password:.*/database_password: ~/" app/config/parameters.yml

    ./bin/run-tests.sh
    EOL;

    $projects = array(
        (new Sismo\Project(ucfirst('monolog'), '/home/greg/dev/labs/monolog', $notifier)),
        (new Sismo\Project('Project FOO', '/home/greg/dev/project/foo', $notifier))->setCommand($sfCommand),
        // ...
    );

    return $projects;

And then the little script to set up the **post commit hook** and `config.php`.

1. Put this command in your `$PATH` (like `/usr/local/bin/sismo-add`)
1. Adapt the `SISMO_PATH`
1. Then call it from a git project that you want to test with sismo

The script:

    #!/bin/bash

    SISMO_PATH=$HOME/dev/labs/sismo/sismo

    #/usr/local/bin/sismo-add

    ######################################
    # Do not change code after this line #
    ######################################

    BASE_PATH=`pwd`
    SLUG=`basename $BASE_PATH`

    cat > $BASE_PATH/.git/hooks/post-commit <<EOL
    #!/bin/bash

    $SISMO_PATH build $SLUG \`git log -1 HEAD --pretty="%H"\` --force --quiet &
    EOL

    chmod +x $BASE_PATH/.git/hooks/post-commit

    SISMO_PROJECT="    (new Sismo\\\\Project(ucfirst('$SLUG'), '$BASE_PATH', \$notifier)),"

    sed -ie "s#\(\$projects = array(\)#\1\n$SISMO_PROJECT#" $HOME/.sismo/config.php
