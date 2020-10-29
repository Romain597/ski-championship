<?php

declare(strict_types=1);

namespace App\Model;

use App\Gateway\GatewayInterface;

class CategoryModel extends AbstractModel implements ModelInterface
{
    private GatewayInterface $gateway;
    private const TABLE_ALIAS = 'c';

    public function __construct(GatewayInterface $gateway)
    {
        $this->gateway = $gateway;
        $this->gateway->createConnection();
    }

    public function save(array $dataCategory): void
    {
        $requestData = [];
        $requestData['name'] = '"' . $dataCategory['name'] . '"';
        $requestData['description'] = is_null($dataCategory['description']) === true ? '"' . $dataCategory['description'] . '"' : 'NULL'; //$dataCategory['description'] != null
        if ($this->checkDuplicate($requestData) === true) {
            throw new \Exception('There is already a category corresponding to this data.');
        }
        if (is_null($dataCategory['identifier']) === true) {
            $this->insert($requestData);
        } else {
            $updateData = [];
            $resultArray = $this->checkModification((int) $dataCategory['identifier'], $requestData);
            foreach ($resultArray as $checkField => $checkValue) {
                if (intval($checkValue) === 1) {
                    $field = substr($checkField, 6);
                    $updateData[$field] = $requestData[$field];
                }
            }
            $this->update((int) $dataCategory['identifier'], $updateData);
        }
    }

    public function remove(int $id): void
    {
        $request = 'DELETE FROM category WHERE identifier = ' . $id . ';';
        $this->gateway->query($request);
    }

    private function checkDuplicate(array $dataToCheck): bool
    {
        $request = 'SELECT COUNT(*) AS duplicate FROM category c WHERE
            c.name = ' . $dataToCheck['name'];
        $result = $this->gateway->query($request);
        $duplicate = $result->fetch(\PDO::FETCH_COLUMN);
        return intval($duplicate) === 0 ? false : true;
    }

    private function checkModification(int $id, array $dataToCheck): array
    {
        $request = 'SELECT 
            IF(c.name = ' . $dataToCheck['name'] . ',0,1) AS check_name,
            IF(c.description ';
        $request .= $dataToCheck['description'] == 'NULL' ? 'IS ' : '= ';
        $request .= $dataToCheck['description'] . ',0,1) AS check_description,
            FROM category c WHERE c.identifier = ' . $id . ';';
        $result = $this->gateway->query($request);
        $data = $result->fetch(\PDO::FETCH_ASSOC);
        if (empty($data) === true) {
            throw new \Exception('The checking of data modification in the database has failed.');
        }
        return $data;
    }

    private function update(int $id, array $dataToUpdate): void
    {
        $setUpdate = '';
        foreach ($dataToUpdate as $field => $fieldValue) {
            $setUpdate .= $field . ' = ' . $fieldValue . ', ';
        }
        $setUpdate = substr($setUpdate, 0, strlen($setUpdate) - 2);
        $request = 'UPDATE category SET ' . $setUpdate . ' WHERE identifier = ' . $id . ';';
        $this->gateway->query($request);
    }

    private function insert(array $dataToInsert): void
    {
        $request = 'INSERT INTO category(identifier, name, description)
            VALUES (NULL,' . $dataToInsert['name'] . ',' . $dataToInsert['description'] . ');';
        $this->gateway->query($request);
    }

    public function search(array $conditions = [], array $filters = [], bool $distinct = false): array
    {
        $request = 'SELECT ' . self::TABLE_ALIAS . '.* FROM category ' . self::TABLE_ALIAS;
        if ($distinct === true) {
            $request = 'SELECT DISTINCT ' . self::TABLE_ALIAS . '.* FROM category ' . self::TABLE_ALIAS;
        }
        if (!empty($conditions) === true && $this->isValidConditions($conditions) === true) {
            $request .= ' WHERE ' . implode(' ', $conditions);
        }
        $filterString = '';
        if (!empty($filters) === true && $this->isValidFilters($filters) === true) {
            $filterString = implode(' ', $filters);
        } else {
            $filterString = ' ORDER BY ' . self::TABLE_ALIAS . '.identifier ASC';
        }
        $request .= $filterString;
        $request .= ';';
        $result = $this->gateway->query($request);
        $data = $result->fetchAll(\PDO::FETCH_ASSOC);
        return empty($data) === true ? [] : $data;
    }
    
    public function getTableAlias(): string
    {
        return self::TABLE_ALIAS;
    }
}
