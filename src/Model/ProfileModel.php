<?php

declare(strict_types=1);

namespace App\Model;

use App\Gateway\GatewayInterface;

class ContestModel implements ModelInterface
{
    private GatewayInterface $gateway;

    public function __construct(GatewayInterface $gateway)
    {
        $this->gateway = $gateway;
    }

    public function save(array $dataProfile): void
    {
        $requestData = [];
        $requestData['name'] = '"' . $dataProfile['name'] . '"';
        $requestData['description'] = $dataProfile['description'] != null ? '"' . $dataProfile['description'] . '"' : 'NULL';
        if ($this->checkDuplicate($requestData) === true) {
            throw new \Exception('There is already a profile corresponding to this data.');
        }
        if ($dataProfile['identifier'] !== null) {
            $this->insert($requestData);
        } else {
            $updateData = [];
            $resultArray = $this->checkModification((int) $dataProfile['identifier'], $requestData);
            foreach ($resultArray as $checkField => $checkValue) {
                if (intval($checkValue) === 1) {
                    $field = substr($checkField, 6);
                    $updateData[$field] = $requestData[$field];
                }
            }
            $this->update((int) $dataProfile['identifier'], $updateData);
        }
    }

    public function remove(int $id): void
    {
        $request = 'DELETE FROM profile WHERE identifier = ' . $id . ';';
        $this->gateway->query($request);
    }

    private function checkDuplicate(array $dataToCheck): bool
    {
        $request = 'SELECT COUNT(p.*) AS duplicate FROM profile p WHERE
            p.name = ' . $dataToCheck['name'];
        $result = $this->gateway->query($request);
        $duplicate = $result->fetch(\PDO::FETCH_ASSOC);
        return intval($duplicate['duplicate']) === 0 ? false : true;
    }

    private function checkModification(int $id, array $dataToCheck): array
    {
        $request = 'SELECT 
            IF(p.name=' . $dataToCheck['name'] . ',0,1) AS check_name,
            IF(p.description ';
        $request .= $dataToCheck['description'] == 'NULL' ? 'IS ' : '= ';
        $request .= $dataToCheck['description'] . ',0,1) AS check_description,
            FROM profile p WHERE p.identifier = ' . $id . ';';
        $result = $this->gateway->query($request);
        return $result->fetch(\PDO::FETCH_ASSOC);
    }

    private function update(int $id, array $dataToUpdate): void
    {
        $setUpdate = '';
        foreach ($dataToUpdate as $field => $fieldValue) {
            $setUpdate .= $field . ' = ' . $fieldValue . ', ';
        }
        $setUpdate = substr($setUpdate, 0, strlen($setUpdate) - 2);
        $request = 'UPDATE profile SET ' . $setUpdate . ' WHERE identifier = ' . $id . ';';
        $this->gateway->query($request);
    }

    private function insert(array $dataToInsert): void
    {
        $request = 'INSERT INTO profile(identifier, name, description)
            VALUES (,' . $dataToInsert['name'] . ',' . $dataToInsert['description'] . ');';
        $this->gateway->query($request);
    }

    public function search(array $conditions = [], array $filters = []): array
    {
        $request = 'SELECT p.* FROM profile p';
        if (!empty($conditions) === true) {
            $conditionString = '';
            foreach ($conditions as $field => $fieldValue) {
                if (stripos($fieldValue, 'NULL') != false) {
                    $conditionString .= $field . ' IS ' . $fieldValue . ' AND ';
                } else {
                    $conditionString .= $field . ' = "' . $fieldValue . '" AND ';
                }
            }
            if (trim($conditionString) != '') {
                $conditionString = substr($conditionString, 0, strlen($conditionString) - 4);
                $request .= ' WHERE ' . $conditionString;
            }
        }
        if (!empty($filters) === true) {
            $filterString = '';
            foreach ($filters as $field => $fieldValue) {
                $filterString .= $field . ' ' . $fieldValue . ' ';
            }
            if (trim($filterString) != '') {
                $request .= ' ' . $filterString;
            }
        } else {
            $request .= ' ORDER BY identifier ASC';
        }
        $request .= ';';
        $result = $this->gateway->query($request);
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }
}
