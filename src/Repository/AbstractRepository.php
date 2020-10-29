<?php

declare(strict_types=1);

namespace App\Repository;

/*use App\Model\ModelInterface;
use App\Gateway\SqlGateway;*/

abstract class AbstractRepository
{
    /*protected function getModel(): ModelInterface
    {
        $gateway = new SqlGateway('mysql:dbname=ski_cup_logitud;host=127.0.0.1', 'root');
        $entity = str_replace('Repository', '', __CLASS__);
        $class = '\\App\\Model\\' . $entity . 'Model';
        return new $class($gateway);
    }*/

    protected function getHavingFilter(string $alias, array $having): string
    {
        /*$havingFilter = implode(' ', $having);
        $havingFilter = preg_replace('/^\s*(OR|AND)/i', ' ', $havingFilter);
        $havingFilter = preg_replace('/(OR|AND)\s*$/i', ' ', $havingFilter);
        return 'HAVING ' . $havingFilter;*/
        $this->checkAlias($alias, $having);
        return 'HAVING ' . implode(' ', $having);
    }

    protected function getGroupFilter(string $alias, array $group): string
    {
        $this->checkAlias($alias, $group);
        return 'GROUP BY ' . implode(', ', $group);
    }

    protected function getOrderFilter(string $alias, array $order): string
    {
        $this->checkAlias($alias, $order);
        return 'ORDER BY ' . implode(', ', $order);
    }

    protected function getLimitFilter(int $limit, int $offset): string
    {
        $limitFilter = "LIMIT $limit";
        if ($offset > 0) {
            $limitFilter .= " OFFSET $offset";
        }
        return $limitFilter;
    }

    protected function getFilters(string $alias, int $offset, int $limit, array $group, array $having, array $order): array
    {
        $filters = ['group' => '',
            'having' => '',
            'order' => '',
            'limit' => ''];
        if (!empty($group) === true) {
            $filters['group'] = $this->getGroupFilter($alias, $group);
        }
        if (!empty($having) === true) {
            $filters['having'] = $this->getHavingFilter($alias, $having);
        }
        if (!empty($order) === true) {
            $filters['order'] = $this->getOrderFilter($alias, $order);
        }
        if ($limit > 0) {
            $filters['limit'] = $this->getLimitFilter($limit, $offset);
        }
        return $filters;
    }

    protected function checkAlias(string $alias, array $datas): void
    {
        foreach ($datas as $data) {
            if (stripos(trim($alias) . '.', $data) === false && preg_match('#\s*[a-z]*\.#i', $data) === 1) {
                throw new \Exception("L'alias de la table n'est pas le bon.");
            }
        }
    }

    protected function getConditions($alias, $conditions): array
    {
        $this->checkAlias($alias, $conditions);
        return $conditions;
    }
}
