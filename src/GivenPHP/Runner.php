<?php
namespace GivenPHP;

use Commando\Command;
use Exception;
use GivenPHP;
use PHP_CodeCoverage;
use PHP_CodeCoverage_Exception;
use PHP_CodeCoverage_Filter;
use PHP_CodeCoverage_Report_Clover;
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

        $reporter = $this->cli->getOption('reporter')->getValue();

        GivenPHP::get_instance()->setReporter(new $reporter);
    }

    /**
     * Destructor
     *
     * @throws Exception
     * @return void
     */
    public function __destruct()
    {
        if ($this->has_coverage()) {
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

    /**
     * Run a test file
     *
     * @return void
     */
    public function run()
    {
        GivenPHP::get_instance()->start();
        $files = $this->cli->getArgumentValues();

        foreach ($files AS $file) {
            $this->recursive_run($file);
        }
    }

    /**
     * Run all the tests in the file $file
     * If $file is a directory, then run this function recursively on all the files in this directory
     *
     * @param string $file
     *
     * @throws PHP_CodeCoverage_Exception
     * @return void
     */
    private function recursive_run($file)
    {
        if (is_dir($file)) {
            $this->explore_directory($file);
        } else if (strpos($file, 'test_') === strrpos($file, '/') + 1) {
            if ($this->has_coverage()) {
                $this->coverage->start($file);
            }

            include $file;

            if ($this->has_coverage()) {
                $this->coverage->stop();
            }
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
        $this->cli->option('r')->aka('reporter')->defaultsTo('GivenPHP\Reporting\DefaultReporter')
                  ->describedAs('Set the output reporter')
                  ->must(function ($reporter) {
                      $reporters = array('default', 'tap', 'list');
                      return in_array(strtolower($reporter), $reporters);
                  })
                  ->map(function ($reporter) {
                      return 'GivenPHP\\Reporting\\' . ucfirst(strtolower($reporter)) . 'Reporter';
                  });
    }

    /**
     * Return true if the code coverage is activated, false otherwise
     *
     * @return boolean
     */
    private function has_coverage()
    {
        if (!is_bool($this->has_coverage)) {
            $types = ['html', 'clover'];

            $this->has_coverage = false;

            foreach ($types AS $type) {
                $this->has_coverage = $this->has_coverage || isset($this->cli->getFlagValues()['coverage-' . $type]);
            }
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
