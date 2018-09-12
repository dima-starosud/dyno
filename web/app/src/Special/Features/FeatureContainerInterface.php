<?php

namespace Dyno\Special\Features;

interface FeatureContainerInterface
{
    /**
     * @param string $interfaceName
     * @return null|$interfaceName
     */
    function get(string $interfaceName): ?object;
}
