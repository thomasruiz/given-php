<?php
namespace GivenPHP;

use Commando\Command;

/**
 * Class Runner
 *
 * @package GivenPHP
 */
class Runner
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->arguments = new Command();
    }

    /**
     * Run a test file
     *
     * @return void
     */
    public function run()
    {
        $files = $this->arguments->getArgumentValues();
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
}
