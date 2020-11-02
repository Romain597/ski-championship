<?php

declare(strict_types=1);

namespace App\FileHandler\Parser;

interface ExportFileParserInterface
{
    public function translateToFile(string $outputDataCharset, string $title = ''): array;
}
