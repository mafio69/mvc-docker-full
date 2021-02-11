<?php

use App\Route\mfRoute;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    mfRoute::getAddress($r);
});

$routeInfo = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        $kod =  '<h1>404</h1>';
        $kod .= '<p>Na tej witrynie nie ma takiej strony</p>';
        require BASE.'/App/views/error/error.php';       break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        $kod = '<h1>405</h1>';
        $kod .= '<p>Na tej witrynie nie ma takiej strony</p>';
        require BASE.'/App/views/error/error.php';
        break;

    case Dispatcher::FOUND:
        $handler = $routeInfo[1]; //class and method
        $vars = $routeInfo[2]; // variable
        /** @var $container  DI\Container*/
        $container->call($handler, $vars);
        break;
}
