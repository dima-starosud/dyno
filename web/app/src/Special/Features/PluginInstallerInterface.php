<?php

namespace Dyno\Special\Features;

interface PluginInstallerInterface
{
    function install(PluginInterface $plugin): void;
}
