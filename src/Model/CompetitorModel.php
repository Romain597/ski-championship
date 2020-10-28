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

    public function save(array $dataCompetitor): void
    {
        $requestData = [];
        $requestData['contest_identifier'] = $dataCompetitor['contestIdentifier'];
        $requestData['category_identifier'] = $dataCompetitor['categoryIdentifier'];
        $requestData['profile_identifier'] = $dataCompetitor['profileIdentifier'];
        $requestData['name'] = '"' . $dataCompetitor['name'] . '"';
        $requestData['first_name'] = '"' . $dataCompetitor['firstName'] . '"';
        $requestData['race_number'] = $dataCompetitor['raceNumber'];
        $requestData['email_address'] = '"' . $dataCompetitor['emailAddress'] . '"';
        $birthDate = $dataCompetitor['birthDate']->setTimezone(new \DateTimeZone('UTC'));
        $requestData['birth_date'] = '"' . $birthDate->format('Y-m-d H:i:s') . '"';
        $requestData['photo'] = $dataCompetitor['photo'] != null ? '"' . $dataCompetitor['photo'] . '"' : 'NULL';
        if ($this->checkDuplicate($requestData) === true) {
            throw new \Exception('There is already a competitor corresponding to this data.');
        }
        if ($dataCompetitor['identifier'] !== null) {
            $this->insert($requestData);
        } else {
            $updateData = [];
            $resultArray = $this->checkModification((int) $dataCompetitor['identifier'], $requestData);
            foreach ($resultArray as $checkField => $checkValue) {
                if (intval($checkValue) === 1) {
                    $field = substr($checkField, 6);
                    $updateData[$field] = $requestData[$field];
                }
            }
            $this->update((int) $dataCompetitor['identifier'], $updateData);
        }
    }

    public function remove(int $id): void
    {
        $request = 'DELETE FROM competitor WHERE identifier = ' . $id . ';';
        $this->gateway->query($request);
    }

    private function checkDuplicate(array $dataToCheck): bool
    {
        $request = 'SELECT COUNT(c.*) AS duplicate FROM competitor c WHERE
            c.name = ' . $dataToCheck['name'] . '
            AND c.first_name = ' . $dataToCheck['first_name'] . '
            AND c.race_number = ' . $dataToCheck['race_number'] . '
            AND c.email_address = ' . $dataToCheck['email_address'] . '
            AND DATE_FORMAT(c.birth_date,"%Y-%m-%d %H:%i:%s") = ' . $dataToCheck['birth_date'] . '
            AND s.contest_identifier = ' . $dataToCheck['contest_identifier'] . '
            AND s.category_identifier = ' . $dataToCheck['category_identifier'] . ';';
        $result = $this->gateway->query($request);
        $duplicate = $result->fetch(\PDO::FETCH_ASSOC);
        return intval($duplicate['duplicate']) === 0 ? false : true;
    }

    private function checkModification(int $id, array $dataToCheck): array
    {
        $request = 'SELECT 
            IF(c.name=' . $dataToCheck['name'] . ',0,1) AS check_name,
            IF(c.first_name=' . $dataToCheck['first_name'] . ',0,1) AS check_first_name,
            IF(c.race_number= ' . $dataToCheck['race_number'] . ',0,1) AS check_race_number
            IF(c.email_address= ' . $dataToCheck['email_address'] . ',0,1) AS check_email_address
            IF(DATE_FORMAT(c.birth_date,"%Y-%m-%d %H:%i:%s")=' . $dataToCheck['begin_date']
            . ',0,1) AS check_begin_date, IF(c.photo ';
        $request .= $dataToCheck['photo'] == 'NULL' ? 'IS ' : '= ';
        $request .=  $dataToCheck['photo'] . ',0,1) AS check_photo
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
        $request = 'UPDATE competitor SET ' . $setUpdate . ' WHERE identifier = ' . $id . ';';
        $this->gateway->query($request);
    }

    private function insert(array $dataToInsert): void
    {
        $request = 'INSERT INTO competitor(identifier, contest_identifier, category_identifier, 
            profile_identifier, name, first_name, race_number, birth_date, email_address, photo)
            VALUES (,' . $dataToInsert['contest_identifier'] . ','
            . $dataToInsert['category_identifier']  . ',' . $dataToInsert['profile_identifier']
            . ',' . $dataToInsert['name'] . ',' . $dataToInsert['first_name'] . ', '
            . $dataToInsert['race_number'] . ', ' . $dataToInsert['birth_date'] . ','
            . $dataToInsert['email_address'] . ', ' . $dataToInsert['photo'] . ');';
        $this->gateway->query($request);
    }

    public function search(array $conditions = [], array $filters = []): array
    {
        $request = 'SELECT c.* FROM competitor c';
        if (!empty($conditions) === true) {
            $conditionString = '';
            foreach ($conditions as $field => $fieldValue) {
                if (stripos($fieldValue, 'Date') != false) {
                    $field = 'DATE_FORMAT(' . str_replace('Date', '_date', $field) . ',"%Y-%m-%d %H:%i:%s")';
                }
                $conditionString .= $field . ' = "' . $fieldValue . '" AND ';
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
