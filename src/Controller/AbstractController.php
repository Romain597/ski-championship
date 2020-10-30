<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\RepositoryInterface;
use App\Gateway\GatewayInterface;

class AbstractController
{
    private RepositoryInterface $repository;

    public function getRepository(GatewayInterface $gateway): ?RepositoryInterface
    {
        if (($this->repository instanceof RepositoryInterface) === false) {
            $this->buildRepository($gateway);
        }
        return $this->repository;
    }

    private function buildRepository(GatewayInterface $gateway): void
    {
        $entityName = str_replace('Controller', '', __CLASS__);
        if (trim($entityName) != 'Error') {
            $classModel = '\\App\\Model\\' . $entityName . 'Model';
            $classRepository = '\\App\\Repository\\' . $entityName . 'Repository';
            $this->repository = new $classRepository(new $classModel($gateway));
        }
    }
}
