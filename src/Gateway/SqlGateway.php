<?php

declare(strict_types=1);

namespace App\Gateway;

use Symfony\Component\HttpFoundation\Request;

class SqlGateway implements GatewayInterface
{
    private ?\PDO $pdo = null;
    private string $dsn;
    private string $password;
    private string $user;
    //private const DEFAULT_RESULT_MODE = 'FETCH_ASSOC';

    /**
     * Create a new gateway between the PDO object which is linked to the database and the other class
     * 
     * @param string $dsn
     * @param string $user
     * @param string $password
     * @param Request $request
     * 
     * @throws InvalidArgumentException If the parameters are empty except for the password for localhost
     */
    public function __construct(string $dsn, string $user, string $password = '', ?Request $request = null)
    {
        $host = isset($request) ? $request->server->get('REMOTE_ADDR', '') : $_SERVER['REMOTE_ADDR'];
        if (trim($dsn) == "" || trim($user) == "" || (preg_match('/^(\:\:1)|(127\.0\.0\.1)$/', $host) !== 1 && trim($password) == "" )) {
            throw new \InvalidArgumentException("The gateway login parameters must not be empty.");
        }
        $this->dsn = $dsn;
        $this->password = $password;
        $this->user = $user;
    }

    /**
     * Create the PDO object
     */
    public function createConnection(): void
    {
        if ($this->isConnectedToDatabase() === false) {
            try {
                $this->pdo = new \PDO($this->dsn, $this->user, $this->password);
            } catch (\PDOException $e) {
                throw new \Exception('The connection has failed : ' . $e->getMessage() . ' For : ' . $this->dsn . ';user=' . $this->user, (int) $e->getCode());
            }
        }
    }

    /**
     * Check if the PDO object is instantiate and not empty
     * 
     * @return bool
     */
    public function isConnectedToDatabase(): bool
    {
        return $this->pdo instanceof \PDO;
    }

    private function checkQueryParameters(array $parameters): ?int
    {
        $parametersKey = null;
        $i = 0;
        $find = false;
        $arrayLength = count($parameters[0]);
        while ($i < count($parameters) && $find === false) {
            if (empty($parameters[$i]) === true || $arrayLength !== count($parameters[$i])) {
                throw new \Exception('The bind value parameter has not the same number of child parameters.');
            }
            $questionMarkResult = 0;
            $namedResult = 0;
            $arrayLength = count($parameters[$i]);
            foreach ($parameters[$i] as $value) {
                if (preg_match('/^\?\d+$/', $value) === 1) {
                    $questionMarkResult++;
                }
                if (preg_match('/^\:[a-z]+$/', $value) === 1) {
                    $namedResult++;
                }
            }
            if ($namedResult == $arrayLength || $questionMarkResult == $arrayLength) {
                $find = true;
                $parametersKey = $i;
            }
            $i++;
        }
        return $parametersKey;
    }

    private function queryHasNamedPlaceholders(string $query): bool
    {
        return (bool) preg_match('/(\s\:[a-z]+)/', $query);
    }

    private function queryHasQuestionMarkPlaceholders(string $query): bool
    {
        return (bool) preg_match('/(\s\?)/', $query);
    }

    private function checkQueryParametersType(string $query): bool
    {
        $questionMarkTest = $this->queryHasQuestionMarkPlaceholders($query);
        $namedTest = $this->queryHasNamedPlaceholders($query);
        return $questionMarkTest === false
            && $namedTest === false ? true : $questionMarkTest === true
            xor $namedTest === true;
    }

    private function prepareQuery(string $query): ?\PDOStatement
    {
        if ($this->checkQueryParametersType($query) === false) {
            throw new \Exception('Query error : Bad binding value(s) placeholder(s). Only one symtax accepted by query.');
        }
        try {
            //dump($query);
            $pdoStatement = $this->pdo->prepare($query);
        } catch (\PDOException $e) {
            throw new \Exception('PDO error : ' . $e->getMessage() . ' For the query : ' . $query, (int) $e->getCode());
        }
        return empty($pdoStatement) === true ? null : $pdoStatement;
    }

    private function excecuteQuery(\PDOStatement $pdoStatement, array $parameters = [], array $values = []): \PDOStatement
    {
        if (count($parameters) > 0 && count($values) > 0) {
            for ($i = 1; $i < count($values); $i++) {
                $pdoStatement->bindValue($parameters[$i], $values[$i]);
            }
        }
        $result = $pdoStatement->execute();
        if ($result === false) {
            throw new \Exception('PDO error : Query execution has failed.');
        }
        return $pdoStatement;
    }

    /**
     * Excecute a query with prepare
     * 
     * @param string $query
     * @param array $bindValues
     * @return PDOStatement
     */
    public function query(string $query, array $bindValues = []): \PDOStatement
    {
        if (trim($query) == "") {
            throw new \InvalidArgumentException('The query is empty.');
        }
        if ($this->isConnectedToDatabase() === false) {
            throw new \Exception('The PDO object is not linked to the database.');
        }
        if (!empty($bindValues) === true && count($bindValues) < 2) {
            throw new \Exception('The bind value parameter must have at least 2 array, one for the placeholder and the other for value(s).');
        }
        $parameters = [];
        $values = [];
        if (!empty($bindValues) === true) {
            $parametersKey = $this->checkQueryParameters($bindValues);
            if (is_null($parametersKey) === true) { //if ($parametersKey === null) {
                throw new \Exception('The bind value parameter has no valid placeholder(s) associated.');
            }
            $parameters = $bindValues[$parametersKey];
            if (preg_match('/^\?\d+$/', $parameters[0]) === 1) {
                $parameters = array_map(function ($value) {
                    return intval(str_ireplace('?', '', $value));
                }, $parameters);
            }
            $values = array_filter($bindValues, function ($key) {
                global $parametersKey;
                return $key != $parametersKey;
            }, ARRAY_FILTER_USE_KEY);
        }
        $queryStatement = $this->prepareQuery($query);
        if (is_null($queryStatement) === true) { //if ($queryStatement == null) {
            throw new \Exception('The query preparation has failed.');
        }
        if (!empty($bindValues) === true) {
            foreach ($values as $valuesForQuery) {
                $queryStatement = $this->excecuteQuery($queryStatement, $parameters, $valuesForQuery);
            }
        } else {
            $this->excecuteQuery($queryStatement);
        }
        return $queryStatement;
    }
}
