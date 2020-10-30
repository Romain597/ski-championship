<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\RepositoryInterface;
use App\Gateway\GatewayInterface;

class AbstractController
{
    private ?RepositoryInterface $repository = null;

    public function getRepository(string $className, GatewayInterface $gateway): ?RepositoryInterface
    {
        if (($this->repository instanceof RepositoryInterface) === false) {
            //dump($className);
            //dump(stripos(trim($className), 'ErrorController'));
            if (stripos(trim($className), 'ErrorController') === false) {
                $entityName = str_replace('Controller', '?', $className);
                $this->buildRepository($entityName, $gateway);
            }
        }
        return $this->repository;
    }

    private function buildRepository(string $entityName, GatewayInterface $gateway): void
    {
        $classModel = '\\' . str_replace('?', 'Model', $entityName);
        $classRepository = '\\' . str_replace('?', 'Repository', $entityName);
        $this->repository = new $classRepository(new $classModel($gateway));
    }
}
