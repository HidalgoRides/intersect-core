<?php

namespace Intersect\Core;

use Intersect\Core\Http\Router\Route;
use Intersect\Core\Http\Router\RouteGroup;
use Intersect\Core\Registry\ClassRegistry;
use Intersect\Core\Registry\EventRegistry;
use Intersect\Core\Registry\ConfigRegistry;
use Intersect\Core\Registry\CommandRegistry;
use Intersect\Core\Http\Router\RouteRegistry;

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

    /** @var RouteRegistry */
    private $routeRegistry;

    /** @var array */
    private $migrationPaths = [];

    public function __construct()
    {
        $this->classRegistry = new ClassRegistry();
        $this->classResolver = new ClassResolver($this->classRegistry);
        $this->commandRegistry = new CommandRegistry();
        $this->configRegistry = new ConfigRegistry();
        $this->eventRegistry = new EventRegistry();
        $this->routeRegistry = new RouteRegistry();
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
     * @return RouteRegistry
     */
    public function getRouteRegistry()
    {
        return $this->routeRegistry;
    }

    /**
     * @return array
     */
    public function getMigrationPaths()
    {
        return $this->migrationPaths;
    }

    /**
     * @return array
     */
    public function getRegisteredEvents()
    {
        return $this->eventRegistry->getAll();
    }

    /** 
     * @return array
     */
    public function getRegisteredCommands()
    {
        return $this->commandRegistry->getAll();
    }

    /** 
     * @return array
     */
    public function getRegisteredConfigs($key = null, $defaultValue = null)
    {
        if (is_null($key))
        {
            return $this->configRegistry->getAll();
        }

        $registeredConfig = $this->configRegistry->get($key);

        if (is_null($registeredConfig))
        {
            $registeredConfig = $defaultValue;
        }

        return $registeredConfig;
    }

    public function getRegisteredRoutes($method = null, $path = null)
    {
        if (is_null($method))
        {
            return $this->routeRegistry->getAll();
        }

        return $this->routeRegistry->get($method, $path);
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

    /**
     * @param $name
     * @param $class
     */
    public function bind($name, $class)
    {
        $this->classRegistry->register($name, $class);
    }

    /**
     * @param $key
     * @param Command|Closure $command
     */
    public function command($key, $command)
    {
        $this->commandRegistry->register($key, $command);
    }

    /**
     * @param $key
     * @param Event $event
     */
    public function event($key, Event $event)
    {
        $this->eventRegistry->register($key, $event);
    }

    /**
     * @param $path
     */
    public function migrationPath($path)
    {
        $this->migrationPaths[] = $path;
    }

    /**
     * @param Route $route
     */
    public function route(Route $route)
    {
        $this->routeRegistry->registerRoute($route);
    }

    /**
     * @param RouteGroup $routeGroup
     */
    public function routeGroup(RouteGroup $routeGroup)
    {
        $this->routeRegistry->registerRouteGroup($routeGroup);
    }

    /**
     * @param $name
     * @param $class
     */
    public function singleton($name, $class)
    {
        $this->classRegistry->register($name, $class, true);
    }

}