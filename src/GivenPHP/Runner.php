<?php
namespace GivenPHP;

use Commando\Command;
use Exception;
use GivenPHP;
use PHP_CodeCoverage;
use PHP_CodeCoverage_Exception;
use PHP_CodeCoverage_Filter;
use PHP_CodeCoverage_Report_HTML;

/**
 * Class Runner
 *
 * @package GivenPHP
 */
class Runner
{

    /**
     * The PHP_CodeCoverage instance
     *
     * @var PHP_CodeCoverage $coverage
     */
    private $coverage = null;

    /**
     * True if the code coverage is activated, false otherwise
     *
     * @var boolean $has_coverage
     */
    private $has_coverage = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cli = new Command();
        $this->initialize_options();
        $this->initialize_coverage_analysis();
    }

    /**
     * Run a test file
     *
     * @throws PHP_CodeCoverage_Exception
     * @return void
     */
    public function run()
    {
        $files = $this->cli->getArgumentValues();

        foreach ($files AS $file) {
            if ($this->has_coverage()) {
                $this->coverage->start($file);
            }

            $this->recursive_run($file);

            if ($this->has_coverage()) {
                $this->coverage->stop();
            }
        }

        if ($this->has_coverage()) {
            $writer = new PHP_CodeCoverage_Report_HTML;
            $writer->process($this->coverage, '/tmp/code-coverage-report');
        }
    }

    /**
     * Run all the tests in the file $file
     * If $file is a directory, then run this function recursively on all the files in this directory
     *
     * @param string $file
     *
     * @return void
     */
    private function recursive_run($file)
    {
        if (is_dir($file)) {
            $this->explore_directory($file);
        } else if (strpos($file, 'test_') === strrpos($file, '/') + 1) {
            include $file;
        }
    }

    /**
     * Run recursive_run to all files in $directory
     *
     * @param string $directory
     *
     * @return void
     */
    private function explore_directory($directory)
    {
        if (($dir = opendir($directory))) {
            while (($file = readdir($dir)) !== false) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $this->recursive_run($directory . '/' . $file);
            }
        }

        closedir($dir);
    }

    /**
     * Initialize the possible options of the CLI
     *
     * @return void
     */
    private function initialize_options()
    {
        $this->cli->option('coverage-html')
            ->describedAs('Generate a code coverage report in HTML.');
        $this->cli->option('coverage-clover')
            ->describedAs('Generate a code coverage report in Clover XML.');
    }

    /**
     * Return true if the code coverage is activated, false otherwise
     *
     * @throws Exception
     * @return boolean
     */
    private function has_coverage()
    {
        if (!is_bool($this->has_coverage)) {
            $this->has_coverage = $this->cli->getOption('coverage-html')->getValue() ||
                                  $this->cli->getOption('coverage-clover')->getValue();
        }

        return $this->has_coverage;
    }

    /**
     * Initialize the code coverage analysis
     *
     * @return void
     */
    private function initialize_coverage_analysis()
    {
        if (!$this->has_coverage()) {
            return;
        }

        $filter = new PHP_CodeCoverage_Filter();
        $filter->addDirectoryToBlacklist('vendor');
        $filter->addFileToBlacklist(__DIR__ . '/../utils.php');

        $this->coverage = new PHP_CodeCoverage(null, $filter);
    }
}
