---
title: Comment mettre en place un systeme de traduction dans Zend Framework
author: Greg
layout: post
permalink: /zend/mettre-en-place-un-systeme-de-traduction-dans-zend-framework/
tags:
  - Zend Framework
---

Avoir un site, c’est bien, mais c’est encore mieux si on peut le **traduire
facilement dans plusieurs langues**. On va voir ensemble comment mettre en place
un **fichier de traduction **avec **Zend_Translate** dans notre `application`.
Il y a plusieurs moyens de gérer les **traductions** avec **Zend Framework**.
Chacun a ses avantages et inconvénients. La je vais utiliser un simple fichier
`php` qui va contenir un `tableau (array) `avec les traductions. L’avantage du
tableau php : on peut **facilement le mettre a jour**, il est **lisible par un
humain**. Par contre il a un inconvénient : Si l’application est énorme, on
**aura des problème de performance**. A vous de bien choisir la gestion de la
traduction.

## Le fichier de traduction :

Il faut le placer dans le dossier : `application/languages/`. Pour le nom on va
être créatif et l’appeler `fr.php` pour la traduction en français. Il ressemble
a ça :

    <?php
    return array(
        'key_1'=>'Traduction 1',
        'key_2'=>'Traduction 2',
    );

Donc quand on va utiliser la `pseudo-variable` **key_1**, l’application
**traduira automatiquement **key_1** en **Traduction 1** (bien sur si on est
en `français` dans l’application. Sinon j’ai dit `pseudo-variable` car on
utilise **key_1** d’une façon bien particulières. Tout dépend du contexte. On va
y venir...

## Le bootstrap

Il faut bien entendu « dire » à notre application qu’il faut qu’elle utilise les
traductions et il faut aussi lui donner une **langue par défaut**. On peut même
faire en sorte que l’application choisisse elle même la langue en fonction de la
position géographique de l’utilisateur final, mais la j’y reviendrais dans un
prochain tuto. Voilà, on ajoute ce code dans notre `bootstrap` :

    /**
    * Initialize Translation
    *
    * @return Zend_Translate
    */
    public function _initTranslate()
    {
        $translate = new Zend_Translate('array', APPLICATION_PATH . '/languages/fr.php', 'fr');

        return Zend_Registry::set('Zend_Translate', $translate);
    }

voilà on peut maintenant utiliser notre fichier de traduction :

## Dans un model :

Ici c’est (relativement extrêmement) simple. Par exemple si on veut mettre un
`label` a un `élément` d’un `formulaire`<span style="font-style: normal;">
(</span>`Zend_Element`, `Zend_Form`), et bien sur le traduire il n’y a rien a
faire, il suffit de donner a la méthode `setLabel` la `clé` de notre tableau qui
est dans le fichier `fr.php`. Par exemple pour un champs `password` :

    $password = new Zend_Form_Element_Password(‘password’);
    $password->setLabel(‘form_user_add_password’);

Et dans notre fichier fr.php :

    'form_user_add_password'=>'Mot de passe : ',

**Petit tips **: Si on a besoin de traduire des messages d'erreurs liés aux
validateurs (`Zend_Validate`) d'éléments du formulaire il faut ajouter dans le
fichier de traduction, le message original renvoyé par le validateur. On peu
trouvé [les messages originaux ici][1] (merci guiton)

    //Pour la validation : valeur requise
    'Value is required and can't be empty'=>'la valeur est requise',
    //Pour la validation : Le champs values est deja dans la BDD
    'A record matching %value% was found'=>'Il y a déja '%value%' dans la BDD',

Comme on peut le voir sur le deuxième exemple, on peut récupérer le
paramètre %value% directement dans le
message traduit. C'est la valeur qui a été saisi par l'utilisateur.

## Dans une vue (view) :

Si on a besoin d'une traduction dans une vue, la c'est un tout petit peu
plus compliqué :

    <?php echo $this->translate('view_user_add')?>

Et dans notre fichier fr.php :

    'view_user_add'=>'Ajouter un utilisateur',

Voilà, c'est assez simple, si on veut la traduction de '`key_1`' il suffit
de faire

    $this->translate('key_1')

Bon voilà on a fait le tours ! Enfin il me semble. Il y a des questions ou
des remarques ?

[1]: http://framework.zend.com/manual/fr/zend.validate.messages.html
