<?php

namespace GivenPHP;

use Commando\Command;
use GivenPHP\Reporting\IReporter;
use PHP_CodeCoverage;
use PHP_CodeCoverage_Filter;
use PHP_CodeCoverage_Report_Clover;
use PHP_CodeCoverage_Report_HTML;

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
        'null'    => 'Null'
    ];

    /**
     * The reporter chose in the command line
     *
     * @var IReporter $reporter
     */
    private $reporter;

    /**
     * The instance of GivenPHP
     *
     * @var GivenPHP $givenPHP
     */
    private $givenPHP;

    /**
     * True if the command is ran with --coverage-*
     *
     * @var bool $hasCoverage
     */
    private $hasCoverage;

    /**
     * The instance of code coverage
     *
     * @var PHP_CodeCoverage $coverage
     */
    private $coverage;

    /**
     * Constructor
     *
     * @param GivenPHP $givenPHP
     */
    public function __construct($givenPHP)
    {
        $this->givenPHP = $givenPHP;
        $this->cli      = new Command();
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
            $this->initializeCoverageAnalysis();
        }

        foreach ($this->filesToExecute as $file) {
            $this->runFile($file, $doRun);
        }

        if ($doRun) {
            $this->reporter->runnerEnded($this);

            if ($this->hasCoverage()) {
                if (($path = $this->cli->getOption('coverage-html')->getValue())) {
                    $writer = new PHP_CodeCoverage_Report_HTML;
                    $writer->process($this->coverage, $path);
                }

                if (($path = $this->cli->getOption('coverage-clover')->getValue())) {
                    $writer = new PHP_CodeCoverage_Report_Clover;
                    $writer->process($this->coverage, $path);
                }
            }
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
     * Getter for $reporter
     *
     * @return IReporter
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * Setter for $reporter
     *
     * @param IReporter $reporter
     */
    public function setReporter($reporter)
    {
        $this->reporter = $reporter;
        $this->givenPHP->setReporter($this->reporter);
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

        $reporter = $this->cli->getOption('reporter')->getValue();
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
            if ($this->hasCoverage()) {
                $this->coverage->start($file);
            }

            include $file;

            if ($this->hasCoverage()) {
                $this->coverage->stop();
            }
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

        $this->cli->option('coverage-html')
                  ->describedAs('Generate a code coverage report in HTML.');
        $this->cli->option('coverage-clover')
                  ->describedAs('Generate a code coverage report in Clover XML.');
    }

    /**
     * Return true if the code coverage is activated, false otherwise
     *
     * @return boolean
     */
    private function hasCoverage()
    {
        if (!is_bool($this->hasCoverage)) {
            $types             = ['html', 'clover'];
            $this->hasCoverage = false;

            foreach ($types AS $type) {
                $this->hasCoverage = $this->hasCoverage || isset($this->cli->getFlagValues()['coverage-' . $type]);
            }
        }

        return $this->hasCoverage;
    }

    /**
     * Initialize the code coverage analysis
     *
     * @return void
     */
    private function initializeCoverageAnalysis()
    {
        if ($this->hasCoverage()) {
            $filter = new PHP_CodeCoverage_Filter();
            $filter->addDirectoryToBlacklist('vendor');
            $filter->addFileToBlacklist(__DIR__ . '/../utils.php');

            $this->coverage = new PHP_CodeCoverage(null, $filter);
        }
    }
}
