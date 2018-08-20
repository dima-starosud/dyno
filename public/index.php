<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use function DI\create;
use Dyno\Database;
use Dyno\DataStoreInterface;
use Dyno\DI\MethodContainer;
use Dyno\DI\PriorityContainer;
use Dyno\EntityRest;
use Dyno\ExceptionHandler;
use Dyno\JsonSchemaRoot;
use Dyno\MetaRest;
use FastRoute\RouteCollector;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use Middlewares\Utils\Factory\DiactorosFactory;
use Psr\Http\Message\ResponseFactoryInterface;
use Relay\Relay;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;
use function DI\autowire;
use function FastRoute\simpleDispatcher;

require_once dirname(__DIR__) . '/vendor/autoload.php';


/** @noinspection PhpUnhandledExceptionInspection */
$classContainer = (new ContainerBuilder())
    ->useAnnotations(false)
    ->addDefinitions([
        MetaRest::class => autowire(),
        EntityRest::class => autowire(),
        DataStoreInterface::class => create(Database::class),
        ResponseFactoryInterface::class => create(DiactorosFactory::class),
    ])
    ->build();

$methodContainer = new MethodContainer($classContainer);
$container = new PriorityContainer([$classContainer, $methodContainer]);

function rest_group(string $class) {
    return function (RouteCollector $r) use ($class) {
        $r->get('/schema', "$class::schema");
        $r->post('', "$class::post");
        $r->get('/{id:\d+}', "$class::get");
        $r->get('', "$class::all");
    };
}

$routes = simpleDispatcher(function (RouteCollector $r) {
    $r->get('/api/jss', JsonSchemaRoot::class);
    $r->addGroup('/api/meta', rest_group(MetaRest::class));
    $r->addGroup('/api/entities/{type}', rest_group(EntityRest::class));
});

$middlewareQueue[] = new FastRoute($routes);
$middlewareQueue[] = new ExceptionHandler();
$middlewareQueue[] = new RequestHandler($container);

/** @noinspection PhpUnhandledExceptionInspection */
$requestHandler = new Relay($middlewareQueue);
$response = $requestHandler->handle(ServerRequestFactory::fromGlobals());

$emitter = new SapiEmitter();
return $emitter->emit($response);
