<?php

declare(strict_types=1);

namespace App\File\Loader;

class CsvFileLoader implements FileLoaderInterface
{
    private \SplFileObject $fileHandler;

    public function load(string $filePath, string $mode): bool
    {
        if (in_array($mode, ['r','r+','w','w+','a','a+','x','x+','c','c+','e'], true) === false) {
            throw new \Exception('The file open mode is not valid.');
        }
        try {
            $this->fileHandler = new \SplFileObject($filePath, $mode);
        } catch (\RuntimeException $e) {
            throw new \Exception("Le fichier n'a pu être ouvert.");
        } catch (\LogicException $e) {
            throw new \Exception("Le fichier est un dossier.");
        }
        return $this->fileHandler->isReadable() && $this->fileHandler->isFile() && $this->fileHandler->isWritable();
    }

    public function getFileHandler()
    {
        return $this->fileHandler;
    }
}
