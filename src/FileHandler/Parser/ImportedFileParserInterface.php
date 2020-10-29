<?php

declare(strict_types=1);

namespace App\FileHandler\Parser;

use App\Repository\CompetitorRepository;

interface ImportedFileParserInterface
{
    public function retrieveObjects(int $contestIdentifier, CompetitorRepository $competitorRepository): array;
}
