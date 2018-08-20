<?php

namespace Dyno\DI;


use Psr\Container\ContainerInterface;

class PriorityContainer implements ContainerInterface
{
    private $containers;

    public function __construct(array $containers) {
        $this->containers = $containers;
    }

    /**
     * {@inheritdoc}
     */
    public function has($id) {
        foreach ($this->containers as $container) {
            if ($container->has($id))
                return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id) {
        foreach ($this->containers as $container) {
            if ($container->has($id))
                return $container->get($id);
        }
        throw new NotFoundException($id);
    }
}
