<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Profile;
use App\Model\ProfileModel;
use App\Model\ModelInterface;
use App\Entity\EntityInterface;

class ProfileRepository extends AbstractRepository implements RepositoryInterface
{
    private ProfileModel $profileManager;

    public function __construct(ProfileModel $profileManager)
    {
        $this->profileManager = $profileManager;
    }

    public function getModel(): ModelInterface
    {
        return $this->profileManager;
    }

    public function add(EntityInterface $profile): int
    {
        $id = $this->profileManager->save($profile->toArray());
        return $id;
    }

    public function remove(int $id): void
    {
        $this->profileManager->remove($id);
    }

    public function modify(EntityInterface $profile): void
    {
        $this->profileManager->save($profile->toArray());
    }

    public function findById(int $id): ?Profile
    {
        $alias = $this->profileManager->getTableAlias();
        $arrayOfState = $this->profileManager->search(["$alias.identifier = $id"]);
        return empty($arrayOfState) === true ? null : Profile::fromState($arrayOfState[0]);
    }

    public function findBy(array $conditions, array $orders = [], int $offset = 0, int $limit = 0, array $groups = [], array $havings = []): ?array
    {
        $alias = $this->profileManager->getTableAlias();
        $conditionsForModel = $this->getConditions($alias, $conditions);
        if ($this->profileManager->isValidConditions($havings) === false) {
            throw new \Exception("Il y a un problème d'opérateur dans le paramètre HAVING.");
        }
        $filters = $this->getFilters($alias, $offset, $limit, $groups, $havings, $orders);
        $arrayOfState = $this->profileManager->search($conditionsForModel, $filters);
        $arrayOfProfile = [];
        if (!empty($arrayOfState) === true) {
            foreach ($arrayOfState as $state) {
                $arrayOfProfile[] = Profile::fromState($state);
            }
        }
        return empty($arrayOfProfile) === true ? null : $arrayOfProfile;
    }

    public function findAll(): ?array
    {
        $arrayOfState = $this->profileManager->search();
        $arrayOfProfile = [];
        if (!empty($arrayOfState) === true) {
            foreach ($arrayOfState as $state) {
                $arrayOfProfile[] = Profile::fromState($state);
            }
        }
        return empty($arrayOfProfile) === true ? null : $arrayOfProfile;
    }
}
