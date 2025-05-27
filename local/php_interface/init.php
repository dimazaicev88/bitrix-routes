<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use FastRoute\RouteCollector;
use FastRoute\Dispatcher;
use Routes\Handlers\About\Guaranty;

$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) {
    // Пример маршрутов
    $r->get('/about/guaranty/', 'Guaranty@getHtml');
    $r->addRoute('GET', '/api/users/{id:\d+}', 'UserController@get');
    $r->addRoute('POST', '/api/users', 'UserController@create');
});

// Получаем метод и URI
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Убираем query-параметры (?foo=bar)
if (($pos = strpos($uri, '?')) !== false) {
    $uri = substr($uri, 0, $pos);
}

// Декодируем URI (на случай %20 и т.д.)
$uri = rawurldecode($uri);

// Диспетчеризация
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        header("HTTP/1.0 404 Not Found");
        echo json_encode(['error' => 'Route not found']);
        break;
    case Dispatcher::METHOD_NOT_ALLOWED:
        header("HTTP/1.0 405 Method Not Allowed");
        echo json_encode(['error' => 'Method not allowed']);
        break;
    case Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        // Обработка контроллера (например, "UserController@list")
        list($controller, $method) = explode('@', $handler);

        // Подключаем класс (если он в /local/classes/)
        $controllerClass = "\\MyProject\\Api\\$controller";
        if (class_exists($controllerClass)) {
            call_user_func_array([new $controllerClass(), $method], $vars);
        } else {
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode(['error' => 'Controller not found']);
        }
        break;
}