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

    public function add(EntityInterface $category): void
    {
        $this->categoryManager->save($category->toArray());
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

    public function findBy(array $conditions, int $offset = 0, int $limit = 0, array $group = [], array $having = [], array $order = []): ?array
    {
        $alias = $this->categoryManager->getTableAlias();
        $conditionsForModel = $this->getConditions($alias, $conditions);
        if ($this->categoryManager->isValidConditions($having) === false) {
            throw new \Exception("Il y a un problème d'opérateur dans le paramètre HAVING.");
        }
        $filters = $this->getFilters($alias, $offset, $limit, $group, $having, $order);
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
