<?php

declare(strict_types=1);

namespace App\Model;

abstract class AbstractModel
{
    protected const FILTERS_ORDER = ['GROUP BY', 'HAVING', 'ORDER BY', 'LIMIT', 'OFFSET'];
    protected const FILTERS_ORDER_KEY = ['group', 'having', 'order', 'limit'];

    protected function isValidFilters(array $filters): bool
    {
        $i = 0;
        foreach ($filters as $clause => $filter) {
            if ($clause !== self::FILTERS_ORDER_KEY[$i]) {
                throw new \Exception("L'ordre des filtres n'est pas correct. L'ordre est " . implode(', ', self::FILTERS_ORDER) . ".");
            }
            if (trim($filter) !== '') {
                $posInFilter = stripos(trim($filter), self::FILTERS_ORDER[$i]);
                if ($posInFilter === false || ($posInFilter > 0 &&  self::FILTERS_ORDER[$i] !== 'OFFSET')) {
                    throw new \Exception("La clause n'est pas correct. Elle devrait être nommé " . self::FILTERS_ORDER[$i] . " et être placé au début.");
                }
            }
            $i++;
        }
        return true;
    }

    private function getNextOperatorDirection(string $currentCondition): string
    {
        if (preg_match('/(OR|AND)\s*$/i', $currentCondition) === 1) {
            return 'right';
        }
        return 'left';
    }

    private function isValidOperatorDirection(string $operatorDirection, string $currentCondition): bool
    {
        switch ($operatorDirection) {
            case 'right':
                return preg_match('/(OR|AND)\s*$/i', $currentCondition) === 1 ? true : false;
            break;
            case 'left':
                return preg_match('/^\s*(OR|AND)/i', $currentCondition) === 1 ? true : false;
            break;
        }
        return false;
    }

    private function isValidOperator(int $index, int $conditionsLength, string $condition): bool
    {
        if ($index === 0) {
            return preg_match('/^\s*(OR|AND)/i', $condition) === 1 ? false : true;
        }
        if ($index === ($conditionsLength - 1)) {
            return preg_match('/(OR|AND)\s*$/i', $condition) === 1 ? false : true;
        }
        return true;
    }

    public function isValidConditions(array $conditions): bool
    {
        $operatorDirection = $this->getNextOperatorDirection($conditions[0]);
        $conditionsLength = count($conditions);
        foreach ($conditions as $index => $condition) {
            if ($this->isValidOperator($index, $conditionsLength, $condition) === false) {
                throw new \Exception("L'opérateur n'est pas à la bonne place. Aucun opérateur à gauche du premier et aucun opérateur à droite du dernier");
            }
            if ($index > 1 && $index < ($conditionsLength - 1)) {
                if ($this->isValidOperatorDirection($operatorDirection, $condition) === false) {
                    $translationDirection = $operatorDirection === 'right' ? 'droite' : 'gauche';
                    throw new \Exception("L'ordre des conditions n'est pas correct. Il manque un opérateur à $translationDirection.");
                }
            }
        }
        return true;
    }

    public function request(string $sql): array
    {
        $result = $this->gateway->query($sql);
        $data = $result->fetchAll(\PDO::FETCH_ASSOC);
        return empty($data) === true ? [] : $data;
    }
}
