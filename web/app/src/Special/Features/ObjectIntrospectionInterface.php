<?php

namespace Dyno\Special\Features;

use Dyno\Special\Interfaces\ObjectInterface;

interface ObjectIntrospectionInterface
{
    function self(): ObjectInterface;
}
