<?php

declare(strict_types=1);

namespace App\Model;

use App\Gateway\GatewayInterface;

class ContestModel extends AbstractModel implements ModelInterface
{
    private GatewayInterface $gateway;
    private const TABLE_ALIAS = 'e';

    public function __construct(GatewayInterface $gateway)
    {
        $this->gateway = $gateway;
        $this->gateway->createConnection();
    }

    public function save(array $dataContest): int
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
        if (is_null($dataContest['identifier']) === true) {
            $id = $this->insert($requestData);
            return $id;
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
            return (int) $dataContest['identifier'];
        }
    }

    public function remove(int $id): void
    {
        $request = 'DELETE FROM contest WHERE identifier = ' . $id . ';';
        $this->gateway->query($request);
    }

    private function checkDuplicate(array $dataToCheck): bool
    {
        $request = 'SELECT COUNT(*) AS duplicate FROM contest c WHERE
            c.name = ' . $dataToCheck['name'] . '
            AND c.location = ' . $dataToCheck['location'] . '
            AND DATE_FORMAT(c.begin_date,"%Y-%m-%d %H:%i:%s") = ' . $dataToCheck['begin_date'] . '
            AND DATE_FORMAT(c.end_date,"%Y-%m-%d %H:%i:%s") = ' . $dataToCheck['end_date'] . ';';
        $result = $this->gateway->query($request);
        $duplicate = $result->fetch(\PDO::FETCH_COLUMN);
        return intval($duplicate) === 0 ? false : true;
    }

    private function checkModification(int $id, array $dataToCheck): array
    {
        $request = 'SELECT 
            IF(c.name = ' . $dataToCheck['name'] . ',0,1) AS check_name,
            IF(c.location = ' . $dataToCheck['location'] . ',0,1) AS check_location,
            IF(DATE_FORMAT(c.begin_date,"%Y-%m-%d %H:%i:%s") = ' . $dataToCheck['begin_date'] . ',0,1) AS check_begin_date,
            IF(DATE_FORMAT(c.end_date,"%Y-%m-%d %H:%i:%s") = ' . $dataToCheck['end_date'] . ',0,1) AS check_end_date
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
        if (!empty($dataToUpdate)) {
            $setUpdate = '';
            foreach ($dataToUpdate as $field => $fieldValue) {
                $setUpdate .= $field . ' = ' . $fieldValue . ', ';
            }
            $setUpdate = substr($setUpdate, 0, strlen($setUpdate) - 2);
            $request = 'UPDATE contest SET ' . $setUpdate . ' WHERE identifier = ' . $id . ';';
            $this->gateway->query($request);
        }
    }

    private function insert(array $dataToInsert): int
    {
        $request = 'INSERT INTO contest(identifier, name, location, begin_date, end_date)
            VALUES (NULL,' . $dataToInsert['name'] . ',' . $dataToInsert['location'] . ', '
            . $dataToInsert['begin_date'] . ',' . $dataToInsert['end_date'] . ');';
        $this->gateway->query($request);
        return $this->gateway->getLastInsertId();
    }

    public function search(array $conditions = [], array $filters = [], bool $distinct = false): array
    {
        $request = 'SELECT ' . self::TABLE_ALIAS . '.* FROM contest ' . self::TABLE_ALIAS;
        if ($distinct === true) {
            $request = 'SELECT DISTINCT ' . self::TABLE_ALIAS . '.* FROM contest ' . self::TABLE_ALIAS;
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
