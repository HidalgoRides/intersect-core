<?php

namespace Intersect\Core;

use Intersect\Core\Registry\ClassRegistry;
use Intersect\Core\Registry\EventRegistry;
use Intersect\Core\Registry\ConfigRegistry;
use Intersect\Core\Registry\CommandRegistry;

class Container {

    /** @var ClassRegistry */
    private $classRegistry;

    /** @var ClassResolver */
    private $classResolver;

    /** @var CommandRegistry */
    private $commandRegistry;

    /** @var ConfigRegistry */
    private $configRegistry;

    /** @var EventRegistry */
    private $eventRegistry;

    public function __construct()
    {
        $this->classRegistry = new ClassRegistry();
        $this->classResolver = new ClassResolver($this->classRegistry);
        $this->commandRegistry = new CommandRegistry();
        $this->configRegistry = new ConfigRegistry();
        $this->eventRegistry = new EventRegistry();
    }

    /**
     * @return ClassRegistry
     */
    public function getClassRegistry()
    {
        return $this->classRegistry;
    }

    /**
     * @return ClassResolver
     */
    public function getClassResolver()
    {
        return $this->classResolver;
    }

    /**
     * @return CommandRegistry
     */
    public function getCommandRegistry()
    {
        return $this->commandRegistry;
    }

    /**
     * @return ConfigRegistry
     */
    public function getConfigRegistry()
    {
        return $this->configRegistry;
    }

    /**
     * @return EventRegistry
     */
    public function getEventRegistry()
    {
        return $this->eventRegistry;
    }

    /**
     * @param $key
     * @param array $namedParameters
     * @return mixed|object
     * @throws \Exception
     */
    public function resolveClass($key, $namedParameters = [])
    {
        return $this->classResolver->resolve($key, $namedParameters);
    }

}