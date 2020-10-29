<?php

declare(strict_types=1);

namespace App\Model;

use App\Gateway\GatewayInterface;

class CompetitorModel extends AbstractModel implements ModelInterface
{
    private GatewayInterface $gateway;
    private const TABLE_ALIAS = 'p';

    public function __construct(GatewayInterface $gateway)
    {
        $this->gateway = $gateway;
        $this->gateway->createConnection();
    }

    public function save(array $dataCompetitor): void
    {
        $requestData = [];
        $requestData['contest_identifier'] = $dataCompetitor['contestIdentifier'];
        $requestData['category_identifier'] = $dataCompetitor['categoryIdentifier'];
        $requestData['profile_identifier'] = is_null($dataCompetitor['profileIdentifier']) === true ? $dataCompetitor['profileIdentifier'] : 'NULL';
        $requestData['name'] = '"' . $dataCompetitor['name'] . '"';
        $requestData['first_name'] = '"' . $dataCompetitor['firstName'] . '"';
        $requestData['race_number'] = $dataCompetitor['raceNumber'];
        $requestData['email_address'] = '"' . $dataCompetitor['emailAddress'] . '"';
        $birthDate = $dataCompetitor['birthDate']->setTimezone(new \DateTimeZone('UTC'));
        $requestData['birth_date'] = '"' . $birthDate->format('Y-m-d H:i:s') . '"';
        $requestData['photo'] = is_null($dataCompetitor['photo']) === true ? '"' . $dataCompetitor['photo'] . '"' : 'NULL'; //$dataCompetitor['photo'] != null
        if ($this->checkDuplicate($requestData) === true) {
            throw new \Exception('There is already a competitor corresponding to this data.');
        }
        if (is_null($dataCompetitor['identifier']) === true) {
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
        $request = 'SELECT COUNT(*) AS duplicate FROM competitor c WHERE
            c.name = ' . $dataToCheck['name'] . '
            AND c.first_name = ' . $dataToCheck['first_name'] . '
            AND c.email_address = ' . $dataToCheck['email_address'] . '
            AND DATE_FORMAT(c.birth_date,"%Y-%m-%d %H:%i:%s") = ' . $dataToCheck['birth_date'] . '
            AND c.contest_identifier = ' . $dataToCheck['contest_identifier'] . ';';
            //AND c.category_identifier = ' . $dataToCheck['category_identifier'] . ';';
            //AND c.race_number = ' . $dataToCheck['race_number'] . '
        $result = $this->gateway->query($request);
        $duplicate = $result->fetch(\PDO::FETCH_COLUMN);
        return intval($duplicate) === 0 ? false : true;
    }

    private function checkModification(int $id, array $dataToCheck): array
    {
        $request = 'SELECT 
            IF(c.name = ' . $dataToCheck['name'] . ',0,1) AS check_name,
            IF(c.first_name = ' . $dataToCheck['first_name'] . ',0,1) AS check_first_name,
            IF(c.race_number = ' . $dataToCheck['race_number'] . ',0,1) AS check_race_number
            IF(c.email_address = ' . $dataToCheck['email_address'] . ',0,1) AS check_email_address
            IF(DATE_FORMAT(c.birth_date,"%Y-%m-%d %H:%i:%s") = ' . $dataToCheck['birth_date']
            . ',0,1) AS check_begin_date, 
            IF(c.photo ';
        $request .= $dataToCheck['photo'] == 'NULL' ? 'IS ' : '= ';
        $request .=  $dataToCheck['photo'] . ',0,1) AS check_photo
            IF(c.category_identifier = ' . $dataToCheck['category_identifier'] . ',0,1) AS check_category_identifier
            IF(c.profile_identifier ';
        $request .= $dataToCheck['profile_identifier'] == 'NULL' ? 'IS ' : '= ';
        $request .=  $dataToCheck['profile_identifier'] . ',0,1) AS check_profile_identifier
            FROM contest c WHERE c.identifier = ' . $id . ';';
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
        $request = 'UPDATE competitor SET ' . $setUpdate . ' WHERE identifier = ' . $id . ';';
        $this->gateway->query($request);
    }

    private function insert(array $dataToInsert): void
    {
        $request = 'INSERT INTO competitor(identifier, contest_identifier, category_identifier, 
            profile_identifier, name, first_name, race_number, birth_date, email_address, photo)
            VALUES (NULL,' . $dataToInsert['contest_identifier'] . ','
            . $dataToInsert['category_identifier']  . ',' . $dataToInsert['profile_identifier']
            . ',' . $dataToInsert['name'] . ',' . $dataToInsert['first_name'] . ', '
            . $dataToInsert['race_number'] . ', ' . $dataToInsert['birth_date'] . ','
            . $dataToInsert['email_address'] . ', ' . $dataToInsert['photo'] . ');';
        $this->gateway->query($request);
    }

    public function search(array $conditions = [], array $filters = [], bool $distinct = false): array
    {
        $request = 'SELECT ' . self::TABLE_ALIAS . '.* FROM competitor ' . self::TABLE_ALIAS;
        if ($distinct === true) {
            $request = 'SELECT DISTINCT ' . self::TABLE_ALIAS . '.* FROM competitor ' . self::TABLE_ALIAS;
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
