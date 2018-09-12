<?php
declare(strict_types=1);

namespace Dyno\DI;


use Psr\Container\ContainerInterface;

final class MethodContainer implements ContainerInterface
{
    private $classContainer;

    public function __construct(ContainerInterface $classContainer) {
        $this->classContainer = $classContainer;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id) {
        if (!$this->has($id)) {
            throw new NotFoundException($id);
        }
        $id = $this->split($id);
        return [$this->classContainer->get($id[0]), $id[1]];
    }

    /**
     * {@inheritdoc}
     */
    public function has($id) {
        $id = $this->split($id);
        return isset($id[1])
            && $this->classContainer->has($id[0])
            && method_exists($id[0], $id[1]);
    }

    private function split(string $string) {
        return explode('::', $string, 2);
    }
}
