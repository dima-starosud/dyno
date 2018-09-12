<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Dyno\DI\MethodContainer;
use Dyno\DI\PriorityContainer;
use Dyno\Http\ExceptionHandler;
use Dyno\Http\SpecialServer;
use Dyno\Special\Classes\FirstObjectFactory;
use Dyno\Special\Interfaces\ObjectFactoryInterface;
use FastRoute\RouteCollector;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use Middlewares\Utils\Factory\DiactorosFactory;
use Psr\Http\Message\ResponseFactoryInterface;
use Relay\Relay;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;
use function DI\create;
use function FastRoute\simpleDispatcher;

require_once dirname(__DIR__) . '/app/vendor/autoload.php';

/** @noinspection PhpUnhandledExceptionInspection */
$classContainer = (new ContainerBuilder())
    ->useAnnotations(false)
    ->addDefinitions([
        ResponseFactoryInterface::class => create(DiactorosFactory::class),
        ObjectFactoryInterface::class => create(FirstObjectFactory::class),
    ])
    ->build();

$methodContainer = new MethodContainer($classContainer);
$container = new PriorityContainer($classContainer, $methodContainer);

$routes = simpleDispatcher(function (RouteCollector $r) {
    $r->addGroup('/api/special', function (RouteCollector $r) {
        $class = SpecialServer::class;
        $r->post('', "$class::post");
        $r->get('', "$class::all");
    });
});

$middlewareQueue[] = new FastRoute($routes);
$middlewareQueue[] = new ExceptionHandler();
$middlewareQueue[] = new RequestHandler($container);

/** @noinspection PhpUnhandledExceptionInspection */
$requestHandler = new Relay($middlewareQueue);
$response = $requestHandler->handle(ServerRequestFactory::fromGlobals());

$emitter = new SapiEmitter();
return $emitter->emit($response);
