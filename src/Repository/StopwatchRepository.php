<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Stopwatch;
use App\Model\StopwatchModel;
use App\Model\ModelInterface;
use App\Entity\EntityInterface;

class StopwatchRepository extends AbstractRepository implements RepositoryInterface
{
    private StopwatchModel $stopwatchManager;

    public function __construct(StopwatchModel $stopwatchManager)
    {
        $this->stopwatchManager = $stopwatchManager;
    }

    public function getModel(): ModelInterface
    {
        return $this->stopwatchManager;
    }

    public function add(EntityInterface $stopwatch): void
    {
        $this->stopwatchManager->save($stopwatch->toArray());
    }

    public function remove(int $id): void
    {
        $this->stopwatchManager->remove($id);
    }

    public function modify(EntityInterface $stopwatch): void
    {
        $this->stopwatchManager->save($stopwatch->toArray());
    }

    public function findById(int $id): ?Stopwatch
    {
        $alias = $this->stopwatchManager->getTableAlias();
        $arrayOfState = $this->stopwatchManager->search(["$alias.identifier = $id"]);
        return empty($arrayOfState) === true ? null : Stopwatch::fromState($arrayOfState[0]);
    }

    public function findBy(array $conditions, int $offset = 0, int $limit = 0, array $group = [], array $having = [], array $order = []): ?array
    {
        $alias = $this->stopwatchManager->getTableAlias();
        $conditionsForModel = $this->getConditions($alias, $conditions);
        if ($this->stopwatchManager->isValidConditions($having) === false) {
            throw new \Exception("Il y a un problème d'opérateur dans le paramètre HAVING.");
        }
        $filters = $this->getFilters($alias, $offset, $limit, $group, $having, $order);
        $arrayOfState = $this->stopwatchManager->search($conditionsForModel, $filters);
        $arrayOfStopwatch = [];
        if (!empty($arrayOfState) === true) {
            foreach ($arrayOfState as $state) {
                $arrayOfStopwatch[] = Stopwatch::fromState($state);
            }
        }
        return empty($arrayOfStopwatch) === true ? null : $arrayOfStopwatch;
    }

    public function findAll(): ?array
    {
        $arrayOfState = $this->stopwatchManager->search();
        $arrayOfStopwatch = [];
        if (!empty($arrayOfState) === true) {
            foreach ($arrayOfState as $state) {
                $arrayOfStopwatch[] = Stopwatch::fromState($state);
            }
        }
        return empty($arrayOfStopwatch) === true ? null : $arrayOfStopwatch;
    }
}
