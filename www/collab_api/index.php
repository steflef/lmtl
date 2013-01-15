<?php
use \Slim\Slim;
use \Slim\Extras\Middleware\HttpBasicAuth;
\Slim\Extras\Views\Mustache::$mustacheDirectory = 'path/to/mustacheDirectory/';

$app = new \Slim\Slim(array(
    'view' => new \Slim\Extras\Views\Mustache()
));
$app->add(new HttpBasicAuth('user', 'pass'));

