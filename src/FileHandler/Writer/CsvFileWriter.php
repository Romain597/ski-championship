<?php

declare(strict_types=1);

namespace App\FileHandler\Writer;

use App\FileHandler\Loader\CsvFileLoader;

class CsvFileWriter implements FileWriterInterface
{
    private CsvFileLoader $fileLoader;
    private string $filePath;

    public function __construct(CsvFileLoader $fileLoader, string $filePath)
    {
        $this->fileLoader = $fileLoader;
        $this->filePath = $filePath;
    }

    public function write(array $data, string $delimiter = ',', string $enclosure = '"', string $escape = '\\'): void
    {
        $fileTest = $this->fileLoader->load($this->filePath, 'w');
        if ($fileTest === false) {
            throw new \Exception("Le fichier n'est pas valide.");
        }
        $fileHandler = $this->fileLoader->getFileHandler();
        foreach ($data as $fields) {
            $tempData = $fileHandler->fputcsv($fields, $delimiter, $enclosure, $escape);
            if ($tempData === false) {
                throw new \Exception("Mauvaise écriture du fichier.");
            }
        }
    }
}
