<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Contest;
use App\Model\ContestModel;

class ContestRepository implements RepositoryInterface
{
    private ContestModel $contestManager;

    public function __construct(ContestModel $contestManager)
    {
        $this->contestManager = $contestManager;
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
        $arrayOfState = $this->contestManager->search([['identifier','=',$id]]);
        return empty($arrayOfState) === true ? null : Contest::fromState($arrayOfState[0]);
    }

    public function findBy(array $conditions, int $offset = 0, int $limit = -1, array $order = [], array $having = [], array $group = []): ?array
    {
        if (!empty($order) === true) {
            $filters['order'] = $order;
        }
        if (!empty($having) === true) {
            $filters['having'] = $having;
        }
        if (!empty($group) === true) {
            $filters['group'] = $group;
        }
        if (!empty($offset) === true) {
            $filters['offset'] = $offset;
        }
        if (!empty($limit) === true) {
            $filters['limit'] = $limit;
        }
        $arrayOfState = $this->contestManager->search($conditions, $filters);
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
