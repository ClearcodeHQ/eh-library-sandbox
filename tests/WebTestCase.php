<?php

namespace tests\Clearcode\EHLibrarySandbox;

use Silex\WebTestCase as BaseWebTestCase;
use Symfony\Component\HttpKernel\Client;

abstract class WebTestCase extends BaseWebTestCase
{
    /** @var Client */
    protected $client;
    /** @var array */
    protected $jsonResponseData;

    /** {@inheritdoc} */
    public function setUp()
    {
        parent::setUp();

        $this->client = $this->createClient();
    }

    /** {@inheritdoc} */
    public function createApplication()
    {
        $app = require __DIR__.'/../src/app.php';

        require __DIR__.'/../config/dev.php';
        require __DIR__.'/../config/routing.php';

        return $app;
    }

    /** {@inheritdoc} */
    protected function tearDown()
    {
        $this->client = null;
        $this->jsonResponseData = null;

        parent::tearDown();
    }

    protected function visitRoute($method, $routeName, array $routeParameters = array(), array $requestParameters = array())
    {
        $this->visit($method, $this->app['url_generator']->generate($routeName, $routeParameters), $requestParameters);
    }

    protected function visit($method, $url, $requestParameters = array())
    {
        $this->client->request($method, $url, $requestParameters);

        $this->jsonResponseData = json_decode((string) $this->client->getResponse()->getContent(), true);
    }

    protected function assertThatResponseHasStatus($status)
    {
        $this->assertEquals($status, $this->client->getResponse()->getStatusCode());
    }
}
