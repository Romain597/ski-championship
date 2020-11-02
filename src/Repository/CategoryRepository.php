<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use App\Model\CategoryModel;
use App\Model\ModelInterface;
use App\Entity\EntityInterface;

class CategoryRepository extends AbstractRepository implements RepositoryInterface
{
    private CategoryModel $categoryManager;

    public function __construct(CategoryModel $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    public function getModel(): ModelInterface
    {
        return $this->categoryManager;
    }

    public function add(EntityInterface $category): int
    {
        $id = $this->categoryManager->save($category->toArray());
        return $id;
    }

    public function remove(int $id): void
    {
        $this->categoryManager->remove($id);
    }

    public function modify(EntityInterface $category): void
    {
        $this->categoryManager->save($category->toArray());
    }

    public function findById(int $id): ?Category
    {
        $alias = $this->categoryManager->getTableAlias();
        $arrayOfState = $this->categoryManager->search(["$alias.identifier = $id"]);
        return empty($arrayOfState) === true ? null : Category::fromState($arrayOfState[0]);
    }

    public function findBy(array $conditions, array $orders = [], int $offset = 0, int $limit = 0, array $groups = [], array $havings = []): ?array
    {
        $alias = $this->categoryManager->getTableAlias();
        $conditionsForModel = $this->getConditions($alias, $conditions);
        if ($this->categoryManager->isValidConditions($havings) === false) {
            throw new \Exception("Il y a un problème d'opérateur dans le paramètre HAVING.");
        }
        $filters = $this->getFilters($alias, $offset, $limit, $groups, $havings, $orders);
        $arrayOfState = $this->categoryManager->search($conditionsForModel, $filters);
        $arrayOfCategory = [];
        if (!empty($arrayOfState) === true) {
            foreach ($arrayOfState as $state) {
                $arrayOfCategory[] = Category::fromState($state);
            }
        }
        return empty($arrayOfCategory) === true ? null : $arrayOfCategory;
    }

    public function findAll(): ?array
    {
        $arrayOfState = $this->categoryManager->search();
        $arrayOfCategory = [];
        if (!empty($arrayOfState) === true) {
            foreach ($arrayOfState as $state) {
                $arrayOfCategory[] = Category::fromState($state);
            }
        }
        return empty($arrayOfCategory) === true ? null : $arrayOfCategory;
    }
}
