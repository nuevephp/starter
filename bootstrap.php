<?php

require_once 'vendor/autoload.php';

$app = new Slim\App();
$containter = $app->getContainer();
$routeParser = new Nueve\RouteManager\RouteParser($container->get('router'), $container);

$routeConfig = require_once 'config/routes.php';
$routes = [];
foreach ($routeConfig as $name => $route) {
    if (!is_array($route) && !isset($route['pattern'])) {
        continue;
    }
    $group = null;
    $route['name'] = $name;

    array_walk($routes, function ($value) use (&$group, $name) {
        $length = strlen($value['name']);
        if (substr($name, 0, $length) === $value['name']) {
            $group = $value;
        }
    });

    if (!empty($group)) {
        $route['pattern'] = $group['pattern'] . $route['pattern'];
        $route = array_merge($group, $route);
    }

    $routeParser->addRoute($route);
}


return $app;