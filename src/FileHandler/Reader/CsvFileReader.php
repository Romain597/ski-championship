<?php

declare(strict_types=1);

namespace App\FileHandler\Reader;

use App\FileHandler\Loader\CsvFileLoader;

class CsvFileReader implements FileReaderInterface
{
    private CsvFileLoader $fileLoader;
    private string $filePath;

    public function __construct(CsvFileLoader $fileLoader, string $filePath)
    {
        $this->fileLoader = $fileLoader;
        $this->filePath = $filePath;
    }

    public function read(string $delimiter = ',', string $enclosure = '"', string $escape = '\\'): array
    {
        $fileTest = $this->fileLoader->load($this->filePath, 'r');
        if ($fileTest === false) {
            throw new \Exception("Le fichier n'est pas valide.");
        }
        $returnData = [];
        $fileHandler = $this->fileLoader->getFileHandler();
        $fileHandler->setCsvControl($delimiter, $enclosure, $escape);
        $fileHandler->setFlags(
            \SplFileObject::READ_CSV |
            \SplFileObject::READ_AHEAD |
            \SplFileObject::SKIP_EMPTY |
            \SplFileObject::DROP_NEW_LINE
        );
        foreach ($fileHandler as $line) {
            if ($line[0] === '' || is_null($line[0]) === true) { //$line[0] === null
                continue;
            }
            $returnData[] = $line;
        }
        return $returnData;
    }
}
