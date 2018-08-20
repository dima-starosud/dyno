<?php

namespace Dyno\DI;


use Exception;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class NotFoundException extends Exception implements NotFoundExceptionInterface
{
    function __construct(string $id, int $code = 0, Throwable $previous = null) {
        parent::__construct("Error getting value for key $id", $code, $previous);
    }
}
