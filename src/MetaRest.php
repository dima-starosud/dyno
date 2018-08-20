<?php


namespace Dyno;


use JsonSchema\Constraints\Constraint;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Json\Json;
use JsonSchema;


class MetaRest
{
    private $store;
    private $responseFactory;

    public function __construct(
        DataStoreInterface $store,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->store = $store;
        $this->responseFactory = $responseFactory;
    }

    public function json_schema() {
        return [
            'title' => 'Meta Schema',
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string'],
                'schema' => ['$ref' => 'http://json-schema.org/draft-07/schema'],
            ],
            'required' => ['name', 'schema'],
            'additionalProperties' => false,
        ];
    }

    public function schema() {
        return new JsonResponse($this->json_schema());
    }

    public function post(ServerRequestInterface $request): ResponseInterface {
        $type = 'entity_meta';
        $value = Json::decode($request->getBody(), Json::TYPE_ARRAY);
        $validator = new JsonSchema\Validator();
        $validator->validate($value, $this->json_schema(),
            Constraint::CHECK_MODE_APPLY_DEFAULTS | Constraint::CHECK_MODE_TYPE_CAST);
        if (!$validator->isValid()) {
            return new JsonResponse($validator->getErrors(), 400);
        }
        $value['schema'] = Json::encode($value['schema']);
        $id = $this->store->store($type, $value);
        return new JsonResponse(['id' => $id], 201);
    }

    public function get(ServerRequestInterface $request): ResponseInterface {
        $type = 'entity_meta';
        $id = $request->getAttribute('id');
        $result = $this->store->get($type, $id);
        return $result ? new JsonResponse($result) : $this->responseFactory->createResponse(404);
    }

    public function all(): ResponseInterface {
        $type = 'entity_meta';
        $result = $this->store->all($type);
        foreach ($result as $value) {
            $value['schema'] = Json::decode($value['schema']);
        };
        return new JsonResponse($result);
    }

}
