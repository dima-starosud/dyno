<?php
declare(strict_types=1);

namespace Dyno\Special\Features;

abstract class PluginInterface
{
    /**
     * override this making it accept any number of type hinted params required for activation
     */
    function activate(): void {
    }
}
