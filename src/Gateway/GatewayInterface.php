<?php

declare(strict_types=1);

namespace App\Gateway;

interface GatewayInterface
{
    public function query(string $query, array $bindValues = []): \PDOStatement;
    public function createConnection(): void;
    public function isConnectedToDatabase(): bool;
    public function getLastInsertId(): int;
}