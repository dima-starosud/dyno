<?php

namespace Dyno;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;

class JsonSchemaRoot implements MiddlewareInterface
{
    private $store;

    public function __construct(DataStoreInterface $store) {
        $this->store = $store;
    }

    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $response = $this->store->all('entity_meta');
        foreach ($response as &$i)
            $i = "entities/{$i['name']}";
        array_unshift($response, 'meta');

        foreach ($response as &$i)
            $i = [
                'title' => $i,
                'type' => 'object',
                'properties' => [
                    'url' => ['type' => 'string', 'enum' => ["/api/$i"]],
                    'data' => ['$ref' => "/api/$i/schema"],
                ],
                'required' => ['url', 'data'],
                'additionalProperties' => false,
            ];

        $response = [
            'title' => 'Main Form',
            'type' => 'object',
            'oneOf' => $response
        ];

        return new JsonResponse($response);
    }
}
