<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Competitor;
use App\Model\CompetitorModel;
use App\Model\ModelInterface;

class CompetitorRepository extends AbstractRepository implements RepositoryInterface
{
    private CompetitorModel $competitorManager;

    public function __construct(CompetitorModel $competitorManager)
    {
        $this->competitorManager = $competitorManager;
    }

    public function getModel(): ModelInterface
    {
        return $this->competitorManager;
    }

    public function add(Competitor $competitor): void
    {
        $this->competitorManager->save($competitor->toArray());
    }

    public function remove(int $id): void
    {
        $this->competitorManager->remove($id);
    }

    public function modify(Competitor $competitor): void
    {
        $this->competitorManager->save($competitor->toArray());
    }

    public function findById(int $id): ?Competitor
    {
        $alias = $this->competitorManager->getTableAlias();
        $arrayOfState = $this->competitorManager->search(["$alias.identifier = $id"]);
        return empty($arrayOfState) === true ? null : Competitor::fromState($arrayOfState[0]);
    }

    public function findBy(array $conditions, int $offset = 0, int $limit = 0, array $group = [], array $having = [], array $order = []): ?array
    {
        $alias = $this->competitorManager->getTableAlias();
        $conditionsForModel = $this->getConditions($alias, $conditions);
        if ($this->competitorManager->isValidConditions($having) === false) {
            throw new \Exception("Il y a un problème d'opérateur dans le paramètre HAVING.");
        }
        $filters = $this->getFilters($alias, $offset, $limit, $group, $having, $order);
        $arrayOfState = $this->competitorManager->search($conditionsForModel, $filters);
        $arrayOfCompetitor = [];
        if (!empty($arrayOfState) === true) {
            foreach ($arrayOfState as $state) {
                $arrayOfCompetitor[] = Competitor::fromState($state);
            }
        }
        return empty($arrayOfCompetitor) === true ? null : $arrayOfCompetitor;
    }

    public function findAll(): ?array
    {
        $arrayOfState = $this->competitorManager->search();
        $arrayOfCompetitor = [];
        if (!empty($arrayOfState) === true) {
            foreach ($arrayOfState as $state) {
                $arrayOfCompetitor[] = Competitor::fromState($state);
            }
        }
        return empty($arrayOfCompetitor) === true ? null : $arrayOfCompetitor;
    }

    public function findByRaceNumber(int $raceNumber, int $contestIdentifier): ?Competitor
    {
        $competitor = null;
        $alias = $this->competitorManager->getTableAlias();
        $competitorArray = $this->findBy(["$alias.race_number = $raceNumber", "AND $alias.contest_identifier = $contestIdentifier"]);
        if (!empty($competitorArray) === true && count($competitorArray) === 1) {
            $competitor = $competitorArray[0];
        }
        return $competitor;
    }
}
