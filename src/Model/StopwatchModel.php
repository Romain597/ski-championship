<?php

declare(strict_types=1);

namespace App\Model;

use App\Gateway\GatewayInterface;

class StopwatchModel extends AbstractModel implements ModelInterface
{
    protected GatewayInterface $gateway;
    protected const TABLE_ALIAS = 's';

    public function __construct(GatewayInterface $gateway)
    {
        $this->gateway = $gateway;
        $this->gateway->createConnection();
    }

    public function save(array $dataStopwatch): int
    {
        $requestData = [];
        $requestData['turn'] = $dataStopwatch['turn'];
        $requestData['time'] = $dataStopwatch['time'];
        $requestData['contest_identifier'] = $dataStopwatch['contestIdentifier'];
        $requestData['competitor_identifier'] = $dataStopwatch['competitorIdentifier'];
        if ($this->checkDuplicate($requestData) === true) {
            throw new \Exception('There is already a stopwatch corresponding to this data.');
        }
        if (is_null($dataStopwatch['identifier']) === true) {
            $id = $this->insert($requestData);
            return $id;
        } else {
            $updateData = [];
            $resultArray = $this->checkModification((int) $dataStopwatch['identifier'], $requestData);
            foreach ($resultArray as $checkField => $checkValue) {
                if (intval($checkValue) === 1) {
                    $field = substr($checkField, 6);
                    $updateData[$field] = $requestData[$field];
                }
            }
            $this->update((int) $dataStopwatch['identifier'], $updateData);
            return (int) $dataStopwatch['identifier'];
        }
    }

    public function remove(int $id): void
    {
        $request = 'DELETE FROM stopwatch WHERE identifier = ' . $id . ';';
        $this->gateway->query($request);
    }

    private function checkDuplicate(array $dataToCheck): bool
    {
        $request = 'SELECT COUNT(*) AS duplicate FROM stopwatch s WHERE
            s.turn = ' . $dataToCheck['turn'] . '
            AND s.contest_identifier = ' . $dataToCheck['contest_identifier'] . '
            AND s.competitor_identifier = ' . $dataToCheck['competitor_identifier'] . ';';
            //AND s.time = ' . $dataToCheck['time'] . '
        $result = $this->gateway->query($request);
        $duplicate = $result->fetch(\PDO::FETCH_COLUMN);
        return intval($duplicate) === 0 ? false : true;
    }

    private function checkModification(int $id, array $dataToCheck): array
    {
        $request = 'SELECT 
            IF(s.turn = ' . $dataToCheck['turn'] . ',0,1) AS check_turn,
            IF(s.time = ' . $dataToCheck['time'] . ',0,1) AS check_time,
            FROM stopwatch s WHERE s.identifier = ' . $id . ';';
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
            $request = 'UPDATE stopwatch SET ' . $setUpdate . ' WHERE identifier = ' . $id . ';';
            $this->gateway->query($request);
        }
    }

    private function insert(array $dataToInsert): int
    {
        $request = 'INSERT INTO stopwatch(identifier, contest_identifier, competitor_identifier, turn, time)
            VALUES (NULL,' . $dataToInsert['contest_identifier'] . ',' . $dataToInsert['competitor_identifier'] . ',' . $dataToInsert['turn'] . ',' . $dataToInsert['time'] . ');';
        $this->gateway->query($request);
        return $this->gateway->getLastInsertId();
    }

    public function search(array $conditions = [], array $filters = [], bool $distinct = false): array
    {
        $request = 'SELECT ' . self::TABLE_ALIAS . '.* FROM stopwatch ' . self::TABLE_ALIAS;
        if ($distinct === true) {
            $request = 'SELECT DISTINCT ' . self::TABLE_ALIAS . '.* FROM stopwatch ' . self::TABLE_ALIAS;
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
