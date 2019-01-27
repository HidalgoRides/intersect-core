<?php

namespace Intersect\Core\Registry;

use Intersect\Core\Command\Command;

class CommandRegistry extends AbstractRegistry {

    public function getAll()
    {
        $allCommands = parent::getAll();
        ksort($allCommands);
        return $allCommands;
    }

    /**
     * @param $key
     * @param Command $command
     */
    public function register($key, $command)
    {
        parent::register($key, $command);
    }

}