<?php

namespace Intersect\Core\Command;

use Intersect\Core\Event;
use Intersect\Core\Container;
use Intersect\Core\Logger\Logger;
use Intersect\Core\Command\Command;
use Intersect\Core\Logger\ConsoleLogger;

class CommandRunner {

    /** @var Container */
    private $container;

    /** @var Logger */
    private $logger;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->logger = new ConsoleLogger();
    }

    public function run($argv = [], $argc = 0)
    {
        if (php_sapi_name() != 'cli')
        {
            $this->logger->warn('Please run from the command line!');
            return;
        }

        $allRegisteredCommands = $this->container->getCommandRegistry()->getAll();

        array_shift($argv);

        $requestedCommand = (isset($argv[0]) ? $argv[0] : null);
        array_shift($argv);

        if (is_null($requestedCommand) || !array_key_exists($requestedCommand, $allRegisteredCommands))
        {
            $this->logger->warn('Please choose a valid command!');
            $this->displayAllCommands($allRegisteredCommands);
            exit();
        }

        if (isset($argv[0]) && $argv[0] == '--help')
        {
            /** @var Command $command */
            $command = $allRegisteredCommands[$requestedCommand];
            $description = $command->getDescription();
            $parameters = $command->getParameters();

            $hasDescription = (!is_null($description) && trim($description) != '');
            $hasParameters = (count($parameters) > 0);
            $hasDetails = ($hasDescription || $hasParameters);

            $this->logger->info('');
            $this->logger->info('Command: ' . $requestedCommand);

            if (!$hasDetails)
            {
                $this->logger->info('No help information provided at this time');
                $this->logger->info('');
                exit();
            }

            if ($hasDescription)
            {
                $this->logger->info(' - Description: ' . $description);
            }

            if ($hasParameters)
            {
                $usage = $requestedCommand;

                foreach ($parameters as $name => $description)
                {
                    $usage .= ' {' . $name . '}';
                }

                $this->logger->info(' - Usage: ' . $usage);

                foreach ($parameters as $name => $description)
                {
                    $this->logger->info('     - ' . $name . ' - ' . $description);
                }
            }

            $this->logger->info('');
            return;
        }

        $this->logger->info('Executing command: ' . $requestedCommand);

        /** @var Command $registeredCommand */
        $registeredCommand = $this->container->getCommandRegistry()->get($requestedCommand);

        if (!is_null($registeredCommand))
        {
            $registeredCommand->execute($argv);
        }

        $this->logger->info('Finished executing command: ' . $requestedCommand);
    }

    private function displayAllCommands(&$allRegisteredCommands)
    {
        if (count($allRegisteredCommands) == 0)
        {
            $this->logger->info(' * No commands registered yet');
            return;
        }

        /** @var Command $command */
        foreach ($allRegisteredCommands as $key => $command)
        {
            $this->logger->info(' * ' . $key);
        }

        $this->logger->info('');
    }

}