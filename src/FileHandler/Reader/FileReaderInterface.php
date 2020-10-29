<?php

declare(strict_types=1);

namespace App\FileHandler\Reader;

interface FileReaderInterface
{
    public function read(string $delimiter = ',', string $enclosure = '"', string $escape = '\\'): array;
}
