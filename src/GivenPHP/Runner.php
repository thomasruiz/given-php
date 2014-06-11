<?php
namespace GivenPHP;

use Commando\Command;

class Runner
{

    public function __construct()
    {
        $this->arguments = new Command();
    }

    public function run()
    {
        $files = $this->arguments->getArgumentValues();
        foreach ($files AS $file) {
            require $file;
        }
    }
}
