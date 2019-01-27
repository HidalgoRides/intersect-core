<?php

namespace Intersect\Core\Command;

use Intersect\Core\Logger\ConsoleLogger;
use Intersect\Core\Logger\Logger;

abstract class AbstractCommand implements Command {

    /** @var Logger */
    protected $logger;

    public function __construct()
    {
        $this->logger = new ConsoleLogger();
    }

    public function getDescription()
    {
        return null;
    }

    public function getParameters()
    {
        return [];
    }

}