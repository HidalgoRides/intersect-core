<?php

namespace Intersect\Core\Http\Router;

class Route {

    private $action;
    private $method;
    private $name;
    private $path;
    private $extraOptions = [];
    
    public static function get($path, $action, $extraOptions = [])
    {
        return self::newRouteForMethod('GET', $path, $action, $extraOptions);
    }

    public static function post($path, $action, $extraOptions = [])
    {
        return self::newRouteForMethod('POST', $path, $action, $extraOptions);
    }

    public static function delete($path, $action, $extraOptions = [])
    {
        return self::newRouteForMethod('DELETE', $path, $action, $extraOptions);
    }

    public static function put($path, $action, $extraOptions = [])
    {
        return self::newRouteForMethod('PUT', $path, $action, $extraOptions);
    }

    public static function patch($path, $action, $extraOptions = [])
    {
        return self::newRouteForMethod('PATCH', $path, $action, $extraOptions);
    }

    public static function options($path, $action, $extraOptions = [])
    {
        return self::newRouteForMethod('OPTIONS', $path, $action, $extraOptions);
    }

    public static function head($path, $action, $extraOptions = [])
    {
        return self::newRouteForMethod('HEAD', $path, $action, $extraOptions);
    }

    /**
     * @return static
     */
    private static function newRouteForMethod($method, $path, $action, $extraOptions = [])
    {
        $route = new Route();
        $route->setMethod($method);
        $route->setPath($path);
        $route->setAction($action);
        $route->setExtraOptions($extraOptions);

        return $route;
    }

    private function __construct() {}

    public function getAction()
    {
        return $this->action;
    }

    public function setAction($action)
    {
        $this->action = $action;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getExtraOptions()
    {
        return $this->extraOptions;
    }

    public function setExtraOptions(array $extraOptions)
    {
        $this->extraOptions = $extraOptions;
    }

}