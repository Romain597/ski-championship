<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Contest;
use App\Model\ContestModel;
use App\Model\ModelInterface;

class ContestRepository extends AbstractRepository implements RepositoryInterface
{
    private ContestModel $contestManager;

    public function __construct(ContestModel $contestManager)
    {
        $this->contestManager = $contestManager;
    }

    public function getModel(): ModelInterface
    {
        return $this->contestManager;
    }

    public function add(Contest $contest): void
    {
        $this->contestManager->save($contest->toArray());
    }

    public function remove(int $id): void
    {
        $this->contestManager->remove($id);
    }

    public function modify(Contest $contest): void
    {
        $this->contestManager->save($contest->toArray());
    }

    public function findById(int $id): ?Contest
    {
        $alias = $this->contestManager->getTableAlias();
        $arrayOfState = $this->contestManager->search(["$alias.identifier = $id"]);
        return empty($arrayOfState) === true ? null : Contest::fromState($arrayOfState[0]);
    }

    public function findBy(array $conditions, int $offset = 0, int $limit = 0, array $group = [], array $having = [], array $order = []): ?array
    {
        $alias = $this->contestManager->getTableAlias();
        $conditionsForModel = $this->getConditions($alias, $conditions);
        if ($this->contestManager->isValidConditions($having) === false) {
            throw new \Exception("Il y a un problème d'opérateur dans le paramètre HAVING.");
        }
        $filters = $this->getFilters($alias, $offset, $limit, $group, $having, $order);
        $arrayOfState = $this->contestManager->search($conditionsForModel, $filters);
        $arrayOfContest = [];
        if (!empty($arrayOfState) === true) {
            foreach ($arrayOfState as $state) {
                $arrayOfContest[] = Contest::fromState($state);
            }
        }
        return empty($arrayOfContest) === true ? null : $arrayOfContest;
    }

    public function findAll(): ?array
    {
        $arrayOfState = $this->contestManager->search();
        $arrayOfContest = [];
        if (!empty($arrayOfState) === true) {
            foreach ($arrayOfState as $state) {
                $arrayOfContest[] = Contest::fromState($state);
            }
        }
        return empty($arrayOfContest) === true ? null : $arrayOfContest;
    }
}
