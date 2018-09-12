<?php
declare(strict_types=1);

namespace Dyno\Special\Features;

interface PluginFactoryInterface
{
    public function get(string $name): ?PluginInterface;
}
