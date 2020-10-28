<?php

declare(strict_types=1);

namespace App\File\Loader;

interface FileLoaderInterface
{
    public function load(string $filePath, string $mode): bool;
}
