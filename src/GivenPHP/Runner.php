<?php

namespace GivenPHP;

use Commando\Command;
use GivenPHP\Reporting\IReporter;

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
     * Valid reporters for the -r option
     *
     * @var string[] $reporters
     */
    private $reporters = [
        'default' => 'Default',
        'null' => 'Null'
    ];

    /**
     * The reporter chose in the command line
     *
     * @var IReporter $reporter
     */
    private $reporter;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cli = new Command();
        $this->initializeCommandLineOptions();
        $this->parseCommandLineArguments();
    }

    /**
     * Executes all the files contained in $filesToExecute
     *
     * @param bool $doRun
     */
    public function run($doRun = true)
    {
        if ($doRun) {
            $this->reporter->runnerStarted($this);
        }

        foreach ($this->filesToExecute as $file) {
            $this->runFile($file, $doRun);
        }

        if ($doRun) {
            $this->reporter->runnerEnded($this);
        }

        $this->done = true;
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

    /**
     * Setter for $reporter
     *
     * @param IReporter $reporter
     */
    public function setReporter($reporter)
    {
        $this->reporter = $reporter;
        GivenPHP::getInstance()->setReporter($this->reporter);
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

        $reporter       = $this->cli->getOption('reporter')->getValue();
        $this->setReporter(new $reporter);
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
     * Execute a file
     *
     * @param string $file
     * @param bool   $doRun
     */
    private function runFile($file, $doRun)
    {
        if ($doRun) {
            include $file;
        }

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
     * Initialize the command line options
     */
    private function initializeCommandLineOptions()
    {
        $this->cli->option('r')
                  ->aka('reporter')
                  ->defaultsTo('GivenPHP\Reporting\DefaultReporter')
                  ->describedAs('Set the output reporter')
                  ->must(function ($reporter) {
                      return isset($this->reporters[strtolower($reporter)]);
                  })
                  ->map(function ($reporter) {
                      return "GivenPHP\\Reporting\\{$this->reporters[strtolower($reporter)]}Reporter";
                  });
    }
}
