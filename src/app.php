<?php

use Clearcode\EHLibrarySandbox\Controller\AppController;
use Silex\Application;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;

$app = new Application();
$app->register(new UrlGeneratorServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new HttpFragmentServiceProvider());

$app['root_dir'] = $app->share(function () {
    return __DIR__ . '/..';
});

$app['app.controller'] = $app->share(function () use ($app) {
    return new AppController($app);
});

return $app;