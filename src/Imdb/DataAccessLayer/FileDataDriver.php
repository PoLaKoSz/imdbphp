<?php

namespace Imdb\DataAccessLayer;

use Imdb\Services\ICompressor;
use Psr\Log\LoggerInterface;

class FileDataDriver implements DataDriver
{
    /**
     * @var ICompressor
     */
    private $compressor;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Working directory.
     * 
     * @var string
     */
    private $rootFolder;

    /**
     * Initialize a new object with a ZIP compressor.
     */
    public function __construct(ICompressor $compressor, LoggerInterface $logger)
    {
        $this->compressor = $compressor;
        $this->logger     = $logger;
    }

    /**
     * Creates the folder.
     * 
     * @param  string     $folderPath Relative path.
     * 
     * @throws \Exception Occurs when can not create or does not have write permission.
     */
    public function setup(string $folderPath) : void
    {
        $this->rootFolder = $folderPath;

        @mkdir($folderPath, 0700, true);

        if (!is_dir($folderPath)) {
            $errorMessage = "Directory [{$folderPath}] does not exists!";

            $this->logger->critical($errorMessage);
            throw new \Exception($errorMessage);
        }

        if (!is_writable($folderPath)) {
            $errorMessage = "Directory [{$folderPath}] lacks write permission!";

            $this->logger->critical($errorMessage);
            throw new \Exception($errorMessage);
        }
    }

    /**
     * Saves the data to the disk.
     */
    public function save(string $fileName, string $value) : void
    {
        $data = $this->compressor->compress($value);

        file_put_contents($fileName, $data);
    }

    /**
     * Loads the data from the disk.
     * 
     * @throws \Exception Occurs when the file can not be open.
     */
    public function load(string $fileName) : string
    {
        $fileContent = @file_get_contents($fileName);
        
        if (!$fileContent)
        {
            throw new \Exception("Failed to open $fileName");
        }

        $data = $this->compressor->deCompress($fileContent);

        return $data;
    }

    /**
     * Returns the time the file was last modified.
     * 
     * @return int Unix timestamp.
     */
    public function editedAt(string $fileName) : int
    {
        return filemtime($fileName);
    }

    /**
     * Gets every files in this folder recursively.
     * 
     * @return array string collection only with filenames.
     */
    public function list() : array
    {
        $directory = dir($this->rootFolder);
        $files = array();
        
        while ($file = $directory->read())
        {
            if ($file != "." && $file != "..")
            {
                if (is_dir($file))
                {
                    continue;
                }
                
                $files[] = $file;
            }
        }
        $directory->close();

        return $files;
    }

    /**
     * Deletes file from the disk.
     */
    public function delete(string $fileName) : void
    {
        @unlink($fileName);
    }
}
