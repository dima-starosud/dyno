<?php
declare(strict_types=1);

namespace Dyno\DI;


use Psr\Container\ContainerInterface;

class PriorityContainer implements ContainerInterface
{
    private $containers;

    public function __construct(ContainerInterface ...$containers) {
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
