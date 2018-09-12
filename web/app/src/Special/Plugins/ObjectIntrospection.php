<?php

namespace Dyno\Special\Plugins;

use Dyno\Special\Features\ObjectIntrospectionInterface;
use Dyno\Special\Features\PluginInterface;
use Dyno\Special\Interfaces\ObjectInterface;

final class ObjectIntrospection extends PluginInterface implements ObjectIntrospectionInterface
{
    private $object;

    function __construct(ObjectInterface $object) {
        $this->object = $object;
    }

    function self(): ObjectInterface {
        return $this->object;
    }
}
