<?php

namespace Imdb\DataAccessLayer;

interface DataDriver
{
    /**
     * Creates the folder.
     * 
     * @param  string     $folderPath Relative path.
     * 
     * @throws \Exception Occurs when can not create or does not have write permission.
     */
    public function setup(string $folderPath) : void;

    /**
     * Saves the data to the disk.
     */
    public function save(string $fileName, string $value) : void;

    /**
     * Loads the data from the disk.
     */
    public function load(string $fileName) : string;

    /**
     * Gets every files in this folder recursively.
     * 
     * @return array string collection only with filenames.
     */
    public function list() : array;

    /**
     * Returns the time the file was last modified.
     * 
     * @return int Unix timestamp.
     */
    public function editedAt(string $fileName) : int;

    /**
     * Deletes file from the disk.
     */
    public function delete(string $fileName) : void;
}
