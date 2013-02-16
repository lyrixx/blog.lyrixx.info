---
title: Sortie de la beta 1.9 de Zend Framework
author: Greg
layout: post
permalink: /zend/sortie-de-la-beta-1-9-de-zend-framework.html
tags:
  - Zend Framework
---

**Zend Framework 1.9.0 vient de sortir en beta**. Youpi. On peut la trouver
la : <http://framework.zend.com/download/latest>

Bon en gros ils nous disent quoi les gars de chez Zend ? Que c’est uneversion
**non-stable**, et qu’on ne la trouve pas encore sur le site. Sinon ils ont
beaucoup travaillé sur la stabilité, les tests etc... Et tant mieux. Sinon pour
les nouveautés : **support de la version 5.3 de php**.

**Les autres nouveautés en pagaille** (on aura l’occasion d’y revenir) :

* `Zend_Rest_Route`, `Zend_Rest_Controller`, and
`Zend_Controller_Plugin_PutHandler`, which aid in providing
RESTful resources via the MVC layer.
* `Zend_Feed_Reader`, which provides a common API to RSS and Atom feeds,
as well as extensions to each format,
caching, and a slew of other functionality.
* `Zend_Queue` and `Zend_Service_Amazon_Sqs`, which provide the ability to
use local and remote messaging and
queue services for offloading asynchronous processes.
* `Zend_Db_Table` updates to allow using `Zend_Db_Table` as a concrete class
by passing it one or more table
definitions via the constructor.
* `Zend_Test_PHPUnit_Db`, which provides `Zend_Db` support for PHPUnit’s
DBUnit support, allowing developers to
do functional and integration testing against databases using data fixtures.
* Annotation processing support for `Zend_Pdf`, as well as performance
improvements.
* `Zend_Dojo` custom build layer support.
* Numerous `Zend_Ldap` improvements.
* `Zend_Log_Writer_Syslog`, a `Zend_Log` writer for writing to your system log.
* Several new view helpers, including `Zend_View_Helper_BaseUrl`.
