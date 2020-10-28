<?php

declare(strict_types=1);

namespace App\Parser;

interface ExportedFileParserInterface
{
    public function translateToFile(string $outputDataCharset, string $title = ''): array;
}
