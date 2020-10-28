<?php

declare(strict_types=1);

namespace App\Parser;

use App\Repository\CompetitorRepository;

interface ImportedFileParserInterface
{
    public function retrieveObjects(int $contestIdentifier, CompetitorRepository $competitorRepository): array;
}
