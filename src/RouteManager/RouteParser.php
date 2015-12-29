<?php
namespace Nueve\RouteManager;

use Slim\Interfaces\RouterInterface;
use Nueve\Responses\Response;

class RouteParser
{
    protected $defaultKeys = [
        'route' => null,
        'name' => null,
        'controller' => null,
        'action' => null,
        'methods' => null,
        'view' => null,
    ];

    private $router;
    private $container;
    private $response;

    public function __construct(RouterInterface $router, Container $container, Response $response)
    {
        $this->router = $router;
        $this->container = $container;
        $this->response = $response;
    }

    public function addRoute($routeConfig)
    {
        $routeConfig = array_replace_recursive($this->defaultKeys, $routeConfig);

        // Handle Direct render of a view
        if (!empty($routeConfig['view'])) {
            $viewClosure = function ($req, $res) {
                return $res->getBody()->write(
                    $this->response->render($routeConfig['view'])
                );
            };

            $route = $this->create($routeConfig['methods'], $routeConfig['pattern'], $viewClosure);
            $route->setName($routeConfig['name']);
        }
    }

    private function create(array $methods, $pattern, $callable)
    {
        $route = $this->router->map($methods, $pattern, $callable);
        return $route;
    }
}