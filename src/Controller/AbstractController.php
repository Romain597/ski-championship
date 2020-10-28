<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\RepositoryInterface;

class AbstractController
{
    protected function getRepository(): RepositoryInterface
    {
        // utiliser __CLASS__
    }
}