<?php

declare(strict_types=1);

namespace App\Model;

interface ModelInterface
{
    public function save(array $data): void;
    public function remove(int $id): void;
    public function search(array $conditions = [], array $filters = []): array;
}