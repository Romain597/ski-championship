<?php

declare(strict_types=1);

namespace App\Entity;

interface EntityInterface
{
    public static function fromState(array $state): EntityInterface;
    public function toArray(): array;
}