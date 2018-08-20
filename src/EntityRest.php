<?php

namespace Dyno;

use JsonSchema\Constraints\Constraint;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Json\Json;
use JsonSchema;


class EntityRest
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

    public function get_meta(string $type) {
        $meta = $this->store->all('entity_meta', ['name' => $type]);
        if (empty($meta)) {
            return null;
        }
        list($meta) = $meta;
        $meta['schema'] = Json::decode($meta['schema']);
        return $meta;
    }

    public function schema(ServerRequestInterface $request): ResponseInterface {
        $meta = $this->get_meta($request->getAttribute('type'));
        if (empty($meta)) {
            return $this->responseFactory->createResponse(404);
        }
        return new JsonResponse($meta['schema']);
    }

    public function post(ServerRequestInterface $request): ResponseInterface {
        $value = Json::decode($request->getBody());
        $meta = $this->get_meta($request->getAttribute('type'));
        if (empty($meta)) {
            return $this->responseFactory->createResponse(404);
        }
        $validator = new JsonSchema\Validator();
        $validator->validate($value, $meta['schema'], Constraint::CHECK_MODE_APPLY_DEFAULTS);
        if (!$validator->isValid()) {
            return new JsonResponse($validator->getErrors(), 400);
        }
        $value = Json::encode($value);
        $id = $this->store->store('entity', ['meta_id' => $meta['id'], 'value' => $value]);
        return new JsonResponse(['id' => $id], 201);
    }

    public function get(ServerRequestInterface $request): ResponseInterface {
        $type = $request->getAttribute('type');
        $meta = $this->store->all('entity_meta', ['name' => $type]);
        if (empty($meta)) {
            return $this->responseFactory->createResponse(404);
        }
        list($meta) = $meta;

        $id = $request->getAttribute('id');
        $data = $this->store->all('entity', ['meta_id' => $meta['id'], 'id' => $id]);
        if (empty($data)) {
            return $this->responseFactory->createResponse(404);
        }
        list($data) = $data;
        return new JsonResponse(Json::decode($data['value']));
    }

    public function all(ServerRequestInterface $request): ResponseInterface {
        $type = $request->getAttribute('type');
        $meta = $this->store->all('entity_meta', ['name' => $type]);
        if (empty($meta)) {
            return $this->responseFactory->createResponse(404);
        }
        list($meta) = $meta;

        $data = $this->store->all('entity', ['meta_id' => $meta['id']]);
        $data = array_map(
            function ($i) {
                return Json::decode($i['value']);
            },
            $data);
        return new JsonResponse($data);
    }

}
