<?php

namespace Clearcode\EHLibrarySandbox\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;

class AppController
{
    /** @var Application  */
    private $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function indexAction()
    {
        return new JsonResponse(['message' => 'Hello world!']);
    }
}