<?php

namespace Intersect\Core\Http\Router;

class RouteAction {

    private $controller;
    private $isCallable = false;
    private $method;
    private $namedParameters = [];
    private $extraOptions = [];

    public function getController()
    {
        return $this->controller;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
    }

    public function getIsCallable()
    {
        return $this->isCallable;
    }

    public function setIsCallable(bool $isCallable)
    {
        $this->isCallable = $isCallable;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function getNamedParameters()
    {
        return $this->namedParameters;
    }

    public function setNamedParameters(array $namedParameters)
    {
        $this->namedParameters = $namedParameters;
    }

    public function getExtraOptions()
    {
        return $this->extraOptions;
    }

    public function setExtraOptions(array $extraOptions)
    {
        $this->extraOptions = $extraOptions;
    }

    public static function fromRoute(Route $route)
    {
        $routeAction = new static();
        $routeAction->setExtraOptions($route->getExtraOptions());

        $action = $route->getAction();

        if ($action instanceof \Closure)
        {
            $routeAction->setMethod($action);
            $routeAction->setIsCallable(true);
        }
        else
        {
            $methodParts = explode('#', $action);

            if (isset($methodParts[1]))
            {
                $routeAction->setController($methodParts[0]);
                $routeAction->setMethod($methodParts[1]);
            }
        }

        return $routeAction;
    }

}