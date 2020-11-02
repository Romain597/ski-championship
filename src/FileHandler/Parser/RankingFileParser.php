<?php

declare(strict_types=1);

namespace App\FileHandler\Parser;

class RankingFileParser implements ExportFileParserInterface
{
    private int $exportType;
    private array $dataToTransform;
    private const EXPORT_TYPES = [1, 2, 3];
    private const FIELDS_GLOBAL = ['Classement', 'Temps Moyen', 'Dossard', 'Nom', 'Prénom'];
    private const FIELDS_CATEGORY = ['Catégorie', 'Classement', 'Temps Moyen', 'Dossard', 'Nom', 'Prénom'];
    private const FIELDS_AGE_RANGE = ['Tranche d\'âge', 'Classement', 'Temps Moyen', 'Dossard', 'Nom', 'Prénom'];
    public const EXPORT_TYPE_GLOBAL = 1;
    public const EXPORT_TYPE_CATEGORY = 2;
    public const EXPORT_TYPE_AGE_RANGE = 3;

    public function __construct(int $exportType, array $dataToTransform)
    {
        if (empty($dataToTransform) === true) {
            throw new \Exception("Le tableau est vide.");
        }
        if (in_array($exportType, self::EXPORT_TYPES, true) === false) {
            throw new \Exception("Le type de fichier d'export n'est pas valide.");
        }
        $this->exportType = $exportType;
        $this->dataToTransform = $dataToTransform;
    }

    private function getFieldsByExportType(): ?array
    {
        switch ($this->exportType) {
            case self::EXPORT_TYPE_GLOBAL:
                return self::FIELDS_GLOBAL;
            break;
            case self::EXPORT_TYPE_CATEGORY:
                return self::FIELDS_CATEGORY;
            break;
            case self::EXPORT_TYPE_AGE_RANGE:
                return self::FIELDS_AGE_RANGE;
            break;
        }
        return null;
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
            case 'Catégorie':
                return 'category';
            break;
            case 'Tranche d\'âge':
                return 'age_range';
            break;
            case 'Classement':
                return 'rank';
            break;
            case 'Temps Moyen': //median_time
                return 'average_time';
            break;
            case 'Dossard':
                return 'race_number';
            break;
            case 'Nom':
                return 'name';
            break;
            case 'Prénom':
                return 'first_name';
            break;
        }
        return null;
    }

    private function getFileLine(array $data, string $outputDataCharset): array
    {
        $line = [];
        $fields = $this->getFieldsByExportType();
        foreach ($fields as $field) {
            $columnName = $this->getColumnNameByField($field);
            if (is_null($columnName) === true) { //if ($columnName == null) {
                throw new \Exception("Le champs indiqué n'est pas reconnu.");
            }
            if (isset($data[$columnName]) === false) {
                throw new \Exception("L'index $columnName n'existe pas.");
            }
            $value = $data[$columnName];
            if (is_string($value) === true && !is_numeric($value) === true) {
                $value = $this->convertDataCharset($value, $outputDataCharset);
            }
            $line[] = $value;
        }
        return $line;
    }

    private function getFileFields(string $outputDataCharset): array
    {
        $fields = $this->getFieldsByExportType();
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