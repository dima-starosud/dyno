<?php

namespace Dyno;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;

final class Database implements DataStoreInterface
{
    private $adapter;

    public function __construct() {
        $this->adapter = new Adapter([
            'driver' => 'Mysqli',
            'hostname' => 'mysql',
            'port' => '3306',
            'database' => 'dyno',
            'username' => 'dyno',
            'password' => 'dyno',
        ]);
    }

    public function store(string $type, array $data): string {
        $type = new TableGateway($type, $this->adapter);
        $type->insert($data);
        return $type->getLastInsertValue();
    }

    public function get(string $type, string $id) {
        $type = new TableGateway($type, $this->adapter);
        return $type->select(['id' => $id])->current();
    }

    public function all(string $type, array $conditions = []): array {
        $type = new TableGateway($type, $this->adapter);
        return iterator_to_array($type->select($conditions));
    }
}
