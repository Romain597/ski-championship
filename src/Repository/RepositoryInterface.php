<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\ModelInterface;
use App\Entity\EntityInterface;

interface RepositoryInterface
{
    public function add(EntityInterface $object): int;
    public function remove(int $id): void;
    public function modify(EntityInterface $object): void;
    public function findById(int $id): ?object;
    public function findBy(array $conditions, array $orders = [], int $offset = 0, int $limit = 0, array $groups = [], array $havings = []): ?array;
    public function findAll(): ?array;
    public function getModel(): ModelInterface;
}