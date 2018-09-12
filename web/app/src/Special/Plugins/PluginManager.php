<?php

namespace Dyno\Special\Plugins;

use Dyno\Special\Features\FeatureContainerInterface;
use Dyno\Special\Features\PluginInstallerInterface;
use Dyno\Special\Features\PluginInterface;
use Exception;
use ReflectionMethod;
use ReflectionParameter;

final class PluginManager extends PluginInterface implements PluginInstallerInterface, FeatureContainerInterface
{
    /** @var object[] */
    private $registry = [];

    /**
     * @param string $interfaceName
     * @return null|$interfaceName
     */
    function get(string $interfaceName): ?object {
        return $this->registry[$interfaceName] ?? null;
    }

    /**
     * @param PluginInterface $plugin
     * @throws \Exception
     */
    function install(PluginInterface $plugin): void {
        $interfaces = class_implements($plugin);
        $existing = array_intersect_key($this->registry, $interfaces);
        if (!empty($existing)) {
            throw new Exception("Cannot override $existing");
        }
        $interfaces = array_fill_keys($interfaces, $plugin);
        $this->registry = array_merge($this->registry, $interfaces);
        $method = new ReflectionMethod($plugin, 'activate');
        $arguments = array_map(function (ReflectionParameter $p) {
            return $this->registry[$p->getName()];
        }, $method->getParameters());
        $plugin->activate(...$arguments);
    }
}
