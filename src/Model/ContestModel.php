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

    public function save(array $dataContest): void
    {
        $requestData = [];
        $requestData['name'] = '"' . $dataContest['name'] . '"';
        $requestData['location'] = '"' . $dataContest['location'] . '"';
        $beginDate = $dataContest['beginAt']->setTimezone(new \DateTimeZone('UTC'));
        $requestData['begin_date'] = '"' . $beginDate->format('Y-m-d H:i:s') . '"';
        $endDate = $dataContest['endAt']->setTimezone(new \DateTimeZone('UTC'));
        $requestData['end_date'] = '"' . $endDate->format('Y-m-d H:i:s') . '"';
        if ($this->checkDuplicate($requestData) === true) {
            throw new \Exception('There is already a contest corresponding to this data.');
        }
        if ($dataContest['identifier'] !== null) {
            $this->insert($requestData);
        } else {
            $updateData = [];
            $resultArray = $this->checkModification((int) $dataContest['identifier'], $requestData);
            foreach ($resultArray as $checkField => $checkValue) {
                if (intval($checkValue) === 1) {
                    $field = substr($checkField, 6);
                    $updateData[$field] = $requestData[$field];
                }
            }
            $this->update((int) $dataContest['identifier'], $updateData);
        }
    }

    public function remove(int $id): void
    {
        $request = 'DELETE FROM contest WHERE identifier = ' . $id . ';';
        $this->gateway->query($request);
    }

    private function checkDuplicate(array $dataToCheck): bool
    {
        $request = 'SELECT COUNT(c.*) AS duplicate FROM contest c WHERE
            c.name = ' . $dataToCheck['name'] . '
            AND c.location = ' . $dataToCheck['location'] . '
            AND DATE_FORMAT(c.begin_date,"%Y-%m-%d %H:%i:%s") = ' . $dataToCheck['begin_date'] . '
            AND DATE_FORMAT(c.end_date,"%Y-%m-%d %H:%i:%s") = ' . $dataToCheck['end_date'] . ';';
        $result = $this->gateway->query($request);
        $duplicate = $result->fetch(\PDO::FETCH_ASSOC);
        return intval($duplicate['duplicate']) === 0 ? false : true;
    }

    private function checkModification(int $id, array $dataToCheck): array
    {
        $request = 'SELECT 
            IF(c.name=' . $dataToCheck['name'] . ',0,1) AS check_name,
            IF(c.location=' . $dataToCheck['location'] . ',0,1) AS check_location,
            IF(DATE_FORMAT(c.begin_date,"%Y-%m-%d %H:%i:%s")=' . $dataToCheck['begin_date'] . ',0,1) AS check_begin_date,
            IF(DATE_FORMAT(c.end_date,"%Y-%m-%d %H:%i:%s")=' . $dataToCheck['end_date'] . ',0,1) AS check_end_date
            FROM contest c WHERE c.identifier = ' . $id . ';';
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
        $request = 'UPDATE contest SET ' . $setUpdate . ' WHERE identifier = ' . $id . ';';
        $this->gateway->query($request);
    }

    private function insert(array $dataToInsert): void
    {
        $request = 'INSERT INTO contest(identifier, name, location, begin_date, end_date)
            VALUES (,' . $dataToInsert['name'] . ',' . $dataToInsert['location'] . ', '
            . $dataToInsert['begin_date'] . ',' . $dataToInsert['end_date'] . ');';
        $this->gateway->query($request);
    }

    public function search(array $conditions = [], array $filters = []): array
    {
        $request = 'SELECT c.* FROM contest c';
        if (!empty($conditions) === true) {
            $conditionString = '';
            for ($i = 0; $i < count($conditions); $i++) {
                foreach ($conditions[$i] as $field => $fieldValue) {
                    if (stripos($fieldValue, 'At') != false) {
                        $field = 'DATE_FORMAT(' . str_replace('At', '_date', $field) . ',"%Y-%m-%d %H:%i:%s")';
                    }
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
