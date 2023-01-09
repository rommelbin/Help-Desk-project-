<?php

/** @var \Laravel\Lumen\Routing\Router $router */

// ВНИМАНИЕ!
// Не удалять и не изменять следующий route. Используется для проверки работоспособности приложения.
$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->group(['middleware' => ['params', 'logged']], function () use ($router) {
    $router->addRoute(
        ['GET', 'POST', 'PUT', 'DELETE'],
        '/{model}/{method}[/{id:[0-9]+}]',
        'MainController@index'
    );
});
$router->addRoute(['GET'],'/check','MainController@checkCentrifugo');
