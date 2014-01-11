---
layout: post
title:  Symfony2: How to mock services during functional tests
---

When you are working with web-service and you want to add some tests, you have
two options:

* Use the real service
* Mock the service

The first one has some advantages: If the web-service change, your tests will
fail. And it has some inconvenience: If the web-service is momently down, your
tests fail.

But sometime, you really want to mock these services to perform some extra
tests, such as emulate a service down, etc...

With Symfony2 you have some options to mock a web-service.

The first one is to use a mock class for `test` environment. Here, we assume you
have created a Symfony2 service (think DIC) to wrap the web-service.

The first
option is to change the service through the configuration in
`app/config/config_test.yml`. So you may have something like this in your
`src/MyBundle/Resources/config/services.xml`:

    <parameters>
        <parameter key="my_bundle.twitter.class">Twitter</parameter>
    </parameters>

    <services>
        <service id="my_bundle.twitter" class="%my_bundle.twitter.class%">
        </service>
    <services>

and so the `config_test.yml`

    parameters:
        my_bundle.twitter.class: TwitterMock

This option is not very flexible because we can emulate a service down, ...

So the second option is to replace the service by a `phpunit` mock. To do that,
we have to change during the runtime a Symfony2 service. But this is not so
easy, because symfony will shutdown and re-boot your kernel between each
request ([see here](https://github.com/symfony/symfony/blob/3196dbdf528ab62c304b72b3208f8d03f7247203/src/Symfony/Bundle/FrameworkBundle/Client.php#L94-L102)).

So the idea is to override the Kernel to allow the developer to execute some
code after the `boot` call. So we start to create a new kernel:

    <?php

    // app/AppTestKernel.php

    require_once __DIR__.'/AppKernel.php';

    class AppTestKernel extends AppKernel
    {
        private $kernelModifier = null;

        public function boot()
        {
            parent::boot();

            if ($kernelModifier = $this->kernelModifier) {
                $kernelModifier($this);
                $this->kernelModifier = null;
            };
        }

        public function setKernelModifier(\Closure $kernelModifier)
        {
            $this->kernelModifier = $kernelModifier;

            // We force the kernel to shutdown to be sure the next request will boot it
            $this->shutdown();
        }
    }

And now, we can alter the kernel during the test:

    class TwitterTest extends WebTestCase
    {
        public function testTwitter()
        {
            $twitter = $this->getMock('Twitter');
            // Configure your mock here.
            static::$kernel->setKernelModifier(function($kernel) use ($twitter) {
                $kernel->getContainer()->set('my_bundle.twitter', $twitter);
            });

            $this->client->request('GET', '/fetch/twitter'));

            $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        }
    }

And thats it ;)
