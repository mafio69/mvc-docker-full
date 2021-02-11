<?php
/** templates
$r->get('/users', ['App\Controller\UsersController', 'getUsers']);
$r->get('/edit/user/{id:\d}', ['App\Controller\UsersController', 'editUser']);
$r->post('/save/user/{id:\d}', ['App\Controller\UsersController', 'saveUser']);
$r->get('/error/{message}', function ($message) {
require_once BASE . '/App/views/error/error.php';
return;
});
$r->addGroup('/api', function (RouteCollector $r) {
$r->addRoute('GET', '/getusers', ['App\Controller\ApiData\ApiController', 'getUsers']);
$r->addRoute('GET', '/getuser/{id}', ['App\Controller\ApiData\ApiController', 'getUser']);
$r->addRoute('POST', '/setuser/{id}', ['App\Controller\ApiData\ApiController', 'setUser']);
});
 */

namespace App\Route;


use FastRoute\RouteCollector;

class mfRoute
{
    public static function getAddress(RouteCollector $r)
    {
        $r->get('/', ['App\Controller\startController', 'print']);
        $r->get('/?{name:.+}', ['App\Controller\startController', 'print']);
    }
}