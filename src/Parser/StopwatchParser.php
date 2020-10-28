<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Stopwatch;
use App\Repository\CompetitorRepository;

class StopwatchParser implements ImportedFileParserInterface, ExportedFileParserInterface
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
    }

    public function retrieveObjects(int $contestIdentifier, CompetitorRepository $competitorRepository): array
    {
        $fieldsKey = [];
        $objectList = [];
        foreach ($this->dataToTransform as $line) {
            //if (empty($line) === true || count($line) !== count(self::FIELDS) || $this->isNotEmptyLine($line) === false) {
            if ($this->isValidFileLine($line) === false) {
                continue;
            }
            if (empty($fieldsKey) === true) {
                $fieldsKey = $this->getFieldsKeyInFile($line);
            } else {
                $raceNumber = $line[$fieldsKey['dossard']];
                $competitor = $competitorRepository->findByRaceNumber(intval($raceNumber), $contestIdentifier);
                if ($competitor == null) {
                    throw new \Exception("Le compétiteur n'a pas été trouvé.");
                }
                $stopwatchTime1 = str_replace(',', '.', $line[$fieldsKey['passage_1']]);
                if (preg_match('/\d{3,9}(\.\d{1,2})?/', $stopwatchTime1) !== 1) {
                    throw new \Exception("Le temps de passage n°1 n'est pas au bon format.");
                }
                $stopwatchTime2 = str_replace(',', '.', $line[$fieldsKey['passage_2']]);
                if (preg_match('/\d{3,9}(\.\d{1,2})?/', $stopwatchTime2) !== 1) {
                    throw new \Exception("Le temps de passage n°2 n'est pas au bon format.");
                }
                $objectList[] = Stopwatch::fromState([
                    'turn' => 1,
                    'time' => $stopwatchTime1,
                    'competitorIdentifier' => $competitor->getIdentifier(),
                    'contestIdentifier' => $contestIdentifier]);
                $objectList[] = Stopwatch::fromState([
                    'turn' => 2,
                    'time' => $stopwatchTime2,
                    'competitorIdentifier' => $competitor->getIdentifier(),
                    'contestIdentifier' => $contestIdentifier]);
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
        if ($inputDataCharset == null) {
            throw new \Exception("L'encodage en cours n'a pas été détecté.");
        }
        $result = iconv($inputDataCharset, $outputDataCharset . '//TRANSLIT//IGNORE', $dataToConvert);
        if ($result === false) {
            throw new \Exception("La conversion a échoué.");
        }
        return $result;
    }

    private function getPropertyNameByField(string $field): ?string
    {
        switch ($field) {
            case 'Dossard':
                return 'raceNumber';
            break;
            case 'Nom':
                return 'name';
            break;
            case 'Prénom':
                return 'firstName';
            break;
            case 'Passage_1':
                return 'time';
            break;
            case 'Passage_2':
                return 'time';
            break;
        }
        return null;
    }

    private function getFileLine(array $data, string $outputDataCharset): array
    {
        $line = [];
        //if ($data instanceof Stopwatch) { $objectInArray = $data->toArray(); }
        foreach (self::FIELDS as $field) {
            $propertyName = $this->getPropertyNameByField($field);
            if ($propertyName == null) {
                throw new \Exception("Le champs indiqué n'est pas reconnu.");
            }
            $value = $data[$propertyName];
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