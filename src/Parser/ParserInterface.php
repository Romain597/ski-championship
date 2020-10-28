<?php

declare(strict_types=1);

namespace App\Parser;

use App\Repository\RepositoryInterface;
use App\Repository\CompetitorRepository;

interface ParserInterface
{
    public function retrieveObjects(int $contestIdentifier, CompetitorRepository $competitorRepository): array;
    public function translateToFile(string $outputDataCharset, string $title = ''): array;
}
