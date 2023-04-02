<?php
if( !session_id() ) @session_start();
require '../vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use DI\ContainerBuilder;
use Delight\Auth\Auth;
use League\Plates\Engine;

if( !session_id() ) @session_start();
require '../vendor/autoload.php';
function dd($a) {
    echo "<pre>";
    var_dump($a);
    exit;
}


$containerBuilder = new ContainerBuilder(); // создаём контейнер для предварительной настройки нашего DI\Container класса.
$containerBuilder->addDefinitions([ // создаём для builder'a настройку исключений, чтобы он проверял их и выдавал уже в конструктор зависимости от сюда, если тут что то указано
    Engine::class => function() {
        return new Engine('../app/views');
    },

    PDO::class => function () {
        $driver = "mysql";
        $host = "127.0.0.1:3306";
        $database = "social";
        $username = "root";
        $password = "";

        return new PDO("$driver:host=$host; dbname=$database;", $username, $password);
    },

    Auth::class => function($container) {
        return new Auth($container->get('PDO')); //$container->get - функция которая вытаскивает из созданного собственного объекта с настройками, объект выывающую функцию PDO, как то так в общем..
    },

]);

$container = $containerBuilder->build(); // создаём объект DI\Container, это делает метод build() учитывая настройки DI\ContainerBuilder()

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r){
    $r->addRoute('GET', '/test/{selector}/{token}', ['app\HomeController', 'test']);
    $r->addRoute('GET', '/', ['app\HomeController', 'page_login']);
    $r->addRoute('GET', '/test2', ['app\HomeController', 'test2']); // проверка на маршрутизанию: ( 'правильный метод доступа?', 'верно ли указан адрес?', 'прописан ли у него обработчик get_all_users_handler?')
    $r->addRoute('GET', '/page_register', ['app\HomeController', 'Page_register']);
    $r->addRoute('GET', '/test3', ['app\HomeController', 'test3']);
    $r->addRoute('GET', '/email_verefication/{selector}/{token}', ['app\HomeController', 'email_verefication']);
    $r->addRoute('POST', '/sign_in', ['app\HomeController', 'Sign_in']);
    $r->addRoute('GET', '/users', ['app\HomeController', 'Page_users']);

    $r->addRoute('GET', '/add_role', ['app\HomeController', 'Add_role']);

//    $r->addRoute('GET', '/about/{amount:\d+}', ['app\controllers\HomeController', 'about']); // проверка на маршрутизанию: ( 'правильный метод доступа?', 'верно ли указан адрес?', 'прописан ли у него обработчик get_all_users_handler?')
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
if (false !== $pos = strpos($uri, '?')){
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]){
    case FastRoute\Dispatcher::NOT_FOUND:
        echo 'такой страницы нет, ошибка 404';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        echo 'метод не разрешен, ошибка 405';
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1]; // здесь лежит название функции get_all_users_handler
        $vars = $routeInfo[2]; // сюда падает значение из  $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');

//        dd($vars);
        $container->call($routeInfo[1], [$vars]); // вызывает метод 'app/HomeController->test' ($routeInfo[1]), если в конструкторе указаны какие то зависимости, то он их найдёт и создаст и подключит сам вот так - $someLet = new someClass, и в app/HomeControlle($someLet) подставит сам уже

//        d($container);exit;
//        d(Auth::class);exit; // вернет путь до класса, в виде строки string (17) "Delight\Auth\Auth"

//        $queryBuilder = new QueryBuilder();
//        $controller = new $handler[0]($queryBuilder); // = new app\HomeController, а в $handler[1] /page
//        call_user_func([$controller($queryBuilder), $handler[1]], $vars);
        break;
}


