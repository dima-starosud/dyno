<?php

namespace Dyno;


interface DataStoreInterface
{
    public function store(string $type, array $data): string;

    public function get(string $type, string $id);

    public function all(string $type, array $conditions = []): array;
}
