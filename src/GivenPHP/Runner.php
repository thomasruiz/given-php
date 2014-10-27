<?php

namespace GivenPHP;

use Commando\Command;

class Runner
{

    /**
     * The files that will be executed
     *
     * @var string[] $filesToExecute
     */
    private $filesToExecute = [];

    /**
     * The files that have been executed
     *
     * @var string[] $filesExecuted
     */
    private $filesExecuted = [];

    /**
     * The files ignored by the runner
     *
     * @var string[] $ignoredFiles
     */
    private $ignoredFiles = [];

    /**
     * Set to true when the runner included all the files
     *
     * @var bool
     */
    private $done = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cli = new Command();
        $this->parseCommandLineArguments();
    }

    /**
     * Retrieve the files to be executed from the command line arguments
     */
    private function parseCommandLineArguments()
    {
        $files = $this->cli->getArgumentValues();

        foreach ($files AS $file) {
            $this->addFileToExecute($file);
        }
    }

    /**
     * Add a file to be executed
     *
     * @param string $file
     */
    private function addFileToExecute($file)
    {
        if (is_dir($file)) {
            $this->exploreDirectory($file);
        } else if ($this->validateFileName($file)) {
            $this->filesToExecute[] = $file;
        } else {
            $this->ignoredFiles[] = $file;
        }
    }

    /**
     * Explore a directory and check for files to be included
     *
     * @param string $directory
     */
    private function exploreDirectory($directory)
    {
        if (($dir = opendir($directory))) {
            while (($file = readdir($dir)) !== false) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                $this->addFileToExecute($directory . '/' . $file);
            }
        }

        closedir($dir);
    }

    /**
     * Executes all the files contained in $filesToExecute
     */
    public function run()
    {
        foreach ($this->filesToExecute as $file) {
            $this->runFile($file);
        }

        $this->done = true;
    }

    /**
     * Execute a file
     *
     * @param string $file
     */
    private function runFile($file)
    {
        include $file;

        $this->filesExecuted[] = $file;
    }

    /**
     * Check if the file needs to be executed
     *
     * @param string $file
     *
     * @return bool
     */
    private function validateFileName($file)
    {
        return strpos($file, 'test_') === strrpos($file, '/') + 1;
    }

    /**
     * Getter for $done
     *
     * @return bool
     */
    public function isDone()
    {
        return $this->done;
    }

    /**
     * Getter for $filesExecuted
     *
     * @return string[]
     */
    public function getFilesExecuted()
    {
        return $this->filesExecuted;
    }
}
