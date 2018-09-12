<?php
declare(strict_types=1);

namespace Dyno\Special\Interfaces;

interface ObjectFactoryInterface
{
    public function create(): ObjectInterface;
}
