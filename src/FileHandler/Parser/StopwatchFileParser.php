<?php

declare(strict_types=1);

namespace App\FileHandler\Parser;

use App\Entity\Stopwatch;
use App\Repository\CompetitorRepository;

class StopwatchFileParser implements ImportedFileParserInterface, ExportedFileParserInterface
{
    private array $dataToTransform;
    private const FIELDS_SEARCH = ['dossard', 'passage_1', 'passage_2'];
    private const FIELDS = ['Dossard', 'Nom', 'Prénom', 'Passage_1', 'Passage_2'];

    public function __construct(array $dataToTransform)
    {
        if (empty($dataToTransform) === true) {
            throw new \Exception("Le tableau est vide.");
        }
        $this->dataToTransform = $dataToTransform;
    }

    private function isNotEmptyLine($line): bool
    {
        if (empty($line) === true) {
            return false;
        }
        if (!is_array($line) === true) {
            return false;
        }
        foreach ($line as $value) {
            if (empty($value) === true) {
                return false;
            }
        }
        return true;
    }

    private function isValidFileLine(array $line): bool
    {
        return !empty($line) === true && count($line) === count(self::FIELDS) && $this->isNotEmptyLine($line) === true;
    }

    private function getFieldsKeyInFile(array $line): array
    {
        if ($this->isValidFileLine($line) === false) {
            throw new \Exception("Les champs du fichier ne sont pas valides.");
        }
        $fieldsKey = [];
        //$inputDataCharset = $this->getDataEncoding($line[0]);
        $outputDataCharset = $this->getDataEncoding(self::FIELDS_SEARCH[0]);
        foreach ($line as $key => $field) {
            //$field = mb_strtolower($field, 'Windows-1252');
            $field = $this->convertDataCharset($field, $outputDataCharset);
            $field = mb_strtolower($field, $outputDataCharset);
            if (in_array($field, self::FIELDS_SEARCH) === true) {
                $fieldsKey[$field] = $key;
            }
        }
        if (count($fieldsKey) != count(self::FIELDS_SEARCH)) {
            throw new \Exception("Les champs du fichier ne corespondent pas aux champs attendus.");
        }
        return $fieldsKey;
    }

    private function getFormatStopwatchTime(string $time): string
    {
        $time = str_replace(',', '.', $time);
        if (preg_match('/\d{3,9}(\.\d{1,2})?/', $time) !== 1) {
            throw new \Exception("Le temps de passage $time n'est pas au bon format.");
        }
        return $time;
    }

    private function buildObject(int $turn, string $stopwatchTime, int $competitorIdentifier, int $contestIdentifier): Stopwatch
    {
        return Stopwatch::fromState([
            'turn' => $turn,
            'time' => $stopwatchTime,
            'competitorIdentifier' => $competitorIdentifier,
            'contestIdentifier' => $contestIdentifier]);
    }

    public function retrieveObjects(int $contestIdentifier, CompetitorRepository $competitorRepository): array
    {
        $fieldsKey = [];
        $objectList = [];
        foreach ($this->dataToTransform as $line) {
            if ($this->isValidFileLine($line) === false) {
                continue;
            }
            if (empty($fieldsKey) === true) {
                $fieldsKey = $this->getFieldsKeyInFile($line);
            } else {
                $raceNumber = $line[$fieldsKey['dossard']];
                $competitor = $competitorRepository->findByRaceNumber(intval($raceNumber), $contestIdentifier);
                if (is_null($competitor) === true) { //if ($competitor == null) {
                    throw new \Exception("Le compétiteur n'a pas été trouvé.");
                }
                $stopwatchTime1 = $this->getFormatStopwatchTime($line[$fieldsKey['passage_1']]);
                $stopwatchTime2 = $this->getFormatStopwatchTime($line[$fieldsKey['passage_2']]);
                $competitorIdentifier = $competitor->getIdentifier();
                $objectList[] = $this->buildObject(1, $stopwatchTime1, $competitorIdentifier, $contestIdentifier);
                $objectList[] = $this->buildObject(2, $stopwatchTime2, $competitorIdentifier, $contestIdentifier);
            }
        }
        return $objectList;
    }

    private function getDataEncoding(string $dataToAnalyse): ?string
    {
        $result = mb_detect_encoding($dataToAnalyse, mb_detect_order(), true);
        return $result === false ? null : $result;
    }

    private function convertDataCharset(string $dataToConvert, string $outputDataCharset): string
    {
        $inputDataCharset = $this->getDataEncoding($dataToConvert);
        if (is_null($inputDataCharset) === true) { //if ($inputDataCharset == null) {
            throw new \Exception("L'encodage en cours n'a pas été détecté.");
        }
        $result = iconv($inputDataCharset, $outputDataCharset . '//TRANSLIT//IGNORE', $dataToConvert);
        if ($result === false) {
            throw new \Exception("La conversion a échoué.");
        }
        return $result;
    }

    private function getColumnNameByField(string $field): ?string
    {
        switch ($field) {
            case 'Dossard':
                return 'race_number';
            break;
            case 'Nom':
                return 'name';
            break;
            case 'Prénom':
                return 'first_name';
            break;
            case 'Passage_1':
                return 'time_1';
            break;
            case 'Passage_2':
                return 'time_2';
            break;
        }
        return null;
    }

    private function getFileLine(array $data, string $outputDataCharset): array
    {
        $line = [];
        foreach (self::FIELDS as $field) {
            $columnName = $this->getColumnNameByField($field);
            if (is_null($columnName) === true) { //if ($columnName == null) {
                throw new \Exception("Le champs indiqué n'est pas reconnu.");
            }
            $value = null;
            if (isset($data[$columnName]) === true) {
                $value = $data[$columnName];
            }
            if (is_string($value) === true && !is_numeric($value) === true) {
                $value = $this->convertDataCharset($value, $outputDataCharset);
            }
            $line[] = $value;
        }
        return $line;
    }

    private function getFileFields(string $outputDataCharset): array
    {
        $fields = self::FIELDS;
        foreach ($fields as $key => $field) {
            $fields[$key] = $this->convertDataCharset($field, $outputDataCharset);
        }
        return $fields;
    }

    public function translateToFile(string $outputDataCharset, string $title = ''): array
    {
        $fileData = [];
        if (!empty($title) === true) {
            $fileData[] = [$this->convertDataCharset($title, $outputDataCharset)];
            $fileData[] = [''];
        }
        $fileData[] = $this->getFileFields($outputDataCharset);
        foreach ($this->dataToTransform as $data) {
            $fileData[] = $this->getFileLine($data, $outputDataCharset);
        }
        return $fileData;
    }
}