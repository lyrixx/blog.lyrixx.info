---
layout: post
title:  Symfony2: How to debug your functionals tests
tags:
    - Symfony2
---

When you write functional tests with **symfony2** and **phpunit**, it could be
hard to debug them if an exception happened. You can try to `var_dump` the
response, but since you ran tests from cli, the output is totally unreadable.

So there is a very usefull way to view what happening. You should add
`echo $this->client->getReponse()->getContent();die;`:

    namespace MyBundle\Tests\Controller;

    use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

    class UserControllerTest extends WebTestCase
    {
        public function testRegister()
        {
            $this->client->request('GET', '/register');

            echo $this->client->getReponse()->getContent();die;// Just add this line

            // Perform some test
            // $this->assertOk($this->client);
            // ...
        }
    }

And then run your test:

    phpunit --filter testRegister path/to/the/test/UserControllerTest.php > web/debug.html

And now, you can browse `http://yourpoject.localhost/debug.html` with your favorite browser.
