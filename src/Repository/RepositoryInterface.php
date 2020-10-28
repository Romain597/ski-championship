<?php

declare(strict_types=1);

namespace App\Repository;

interface RepositoryInterface
{
    public function add(object $object): void;
    public function remove(int $id): void;
    public function modify(object $object): void;
    public function findById(int $id): ?object;
    public function findBy(array $conditions, int $offset = 0, int $limit = -1, array $order = [], array $having = [], array $group = []): ?array;
    public function findAll(): ?array;
}