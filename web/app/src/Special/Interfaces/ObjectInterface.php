<?php
declare(strict_types=1);

namespace Dyno\Special\Interfaces;

interface ObjectInterface
{
    /**
     * @param string $interfaceName
     * @return null|$interfaceName
     */
    function cast(string $interfaceName): ?object;
}
