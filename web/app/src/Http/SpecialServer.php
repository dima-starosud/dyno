<?php
declare(strict_types=1);

namespace Dyno\Http;

use Dyno\Special\Interfaces\ObjectFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class SpecialServer
{
    /** @var ResponseFactoryInterface */
    private $responseFactory;
    /** @var ObjectFactoryInterface */
    private $objectFactory;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        ObjectFactoryInterface $objectFactory
    ) {
        $this->responseFactory = $responseFactory;
        $this->objectFactory = $objectFactory;
    }

    public function post(ServerRequestInterface $request): ResponseInterface {
        $queryParams = $request->getQueryParams();
        if (isset($queryParams['features'])) {
            $features = explode(',', $queryParams['features']);
        } else {
            $features = [];
        }
        $object = $this->objectFactory->create();
//        return $this->responseFactory->createResponse(201);
        $response = $this->responseFactory->createResponse(201);
        $response->getBody()->write(@var_export($object, true));
        $response->getBody()->write(PHP_EOL);
        $response->getBody()->write(serialize($object));
        return $response;
    }

    public function all(): ResponseInterface {
        $response = $this->responseFactory->createResponse(200);
        $response->getBody()->write(@var_export([], true));
        return $response;
    }
}
