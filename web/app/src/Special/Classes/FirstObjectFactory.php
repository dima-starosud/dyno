<?php
declare(strict_types=1);

namespace Dyno\Special\Classes;

use Dyno\Special\Features\PluginInstallerInterface;
use Dyno\Special\Interfaces\ObjectFactoryInterface;
use Dyno\Special\Interfaces\ObjectInterface;
use Dyno\Special\Plugins\ObjectIntrospection;
use Dyno\Special\Plugins\PluginManager;

final class FirstObjectFactory implements ObjectFactoryInterface
{
    public function create(): ObjectInterface {
        $manager = new PluginManager();
        /** @noinspection PhpUnhandledExceptionInspection */
        $manager->install($manager);
        $object = new FeatureContainerObject($manager);
        $object
            ->cast(PluginInstallerInterface::class)
            ->install(new ObjectIntrospection($object));
        return $object;
    }
}
