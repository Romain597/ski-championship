<?php

declare(strict_types=1);

namespace App\Controller;

use App\FileHandler\Parser\ExportFileParserInterface;
use App\FileHandler\Writer\CsvFileWriter;
use App\FileHandler\Loader\CsvFileLoader;
use App\FileHandler\Reader\CsvFileReader;
use App\Repository\RepositoryInterface;
use App\Gateway\GatewayInterface;
use Twig\Environment;

class AbstractController
{
    protected ?RepositoryInterface $repository = null;
    protected Environment $twig;
    protected const LIMIT_BY_PAGE = 15;
    protected const CSV_CHARSET = 'windows-1252';
    protected const CSV_DELIMITER = ';';
    protected const MAIN_DATABASE_CONF_FILE = 'mysqlMainDatabase';

    public function getGatewayConfiguration(string $databaseConfigurationFile): array
    {
        if (preg_match('/^[\w\-]+$/', $databaseConfigurationFile) !== 1) {
            throw new \Exception("Le nom du fichier de configuration de la base de données n'est pas valide. Seule les caractères alphanumériques et les tirets (tiret et tiret du bas) sont autorisés.");
        }
        if (file_exists(__DIR__ . '/../../config/database/' . $databaseConfigurationFile . '.php') === false) {
            throw new \Exception("Le fichier de configuration de la base de données n'existe pas dans le dossier /config/database/ .");
        }
        $conf = require __DIR__ . '/../../config/database/' . $databaseConfigurationFile . '.php';
        return is_array($conf) === true ? $conf : [];
    }

    public function getRepository(string $className, GatewayInterface $gateway): ?RepositoryInterface
    {
        if (($this->repository instanceof RepositoryInterface) === false) {
            //dump($className);
            //dump(stripos(trim($className), 'ErrorController'));
            if (stripos(trim($className), 'ErrorController') === false) {
                $entityName = str_replace('Controller', '?', $className);
                $this->buildRepository($entityName, $gateway);
            }
        }
        return $this->repository;
    }

    private function buildRepository(string $entityName, GatewayInterface $gateway): void
    {
        $classModel = '\\' . str_replace('?', 'Model', $entityName);
        $classRepository = '\\' . str_replace('?', 'Repository', $entityName);
        $this->repository = new $classRepository(new $classModel($gateway));
    }

    protected function exportDataToCsvFile(ExportFileParserInterface $parser, string $outputDataCharset, string $fileName, string $fileDelimiter = ',', string $fileTitle = ''): void
    {
        if (preg_match('/^[\w\-]+$/', $fileName) !== 1) {
            throw new \Exception("Le nom du fichier n'est pas valide. Seule les caractères alphanumériques et les tirets (tiret et tiret du bas) sont autorisés.");
        }
        $fileData = $parser->translateToFile($outputDataCharset, $fileTitle);
        $loader = new CsvFileLoader();
        $writer = new CsvFileWriter($loader, __DIR__ . '/../../tmp/' . $fileName . '.csv');
        $writer->write($fileData, $fileDelimiter);
    }

    protected function readDataFromCsvFile(string $fileName, string $fileDelimiter = ','): array
    {
        if (preg_match('/^[\w\-]+$/', $fileName) !== 1) {
            throw new \Exception("Le nom du fichier n'est pas valide. Seule les caractères alphanumériques et les tirets (tiret et tiret du bas) sont autorisés.");
        }
        if (file_exists(__DIR__ . '/../tmp/' . $fileName . '.csv') === false) {
            throw new \Exception("Le fichier n'existe pas dans le dossier temporaire.");
        }
        $loader = new CsvFileLoader();
        $reader = new CsvFileReader($loader, __DIR__ . '/../../tmp/' . $fileName . '.csv');
        return $reader->read($fileDelimiter);
    }
}
