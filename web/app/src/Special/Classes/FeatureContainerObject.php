<?php

namespace Dyno\Special\Classes;

use Dyno\Special\Features\FeatureContainerInterface;
use Dyno\Special\Interfaces\ObjectInterface;

final class FeatureContainerObject implements ObjectInterface
{
    private $container;

    function __construct(FeatureContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * @param string $interfaceName
     * @return null|$interfaceName
     */
    function cast(string $interfaceName): ?object {
        return $this->container->get($interfaceName);
    }
}
