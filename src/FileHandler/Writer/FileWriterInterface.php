<?php

declare(strict_types=1);

namespace App\FileHandler\Writer;

interface FileWriterInterface
{
    public function write(array $data, string $delimiter = ',', string $enclosure = '"', string $escape = '\\'): void;
}
