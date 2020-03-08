<?php

namespace Intersect\Core\Http\Router;

use Intersect\Core\Http\Router\Route;

class NamedRoute {
    
    /**
     * @return Route
     */
    public static function get($name, $path, $action, $extraOptions = [])
    {
        $route = Route::get($path, $action, $extraOptions);
        $route->setName($name);
        return $route;
    }

    /**
     * @return Route
     */
    public static function post($name, $path, $action, $extraOptions = [])
    {
        $route = Route::post($path, $action, $extraOptions);
        $route->setName($name);
        return $route;
    }

    /**
     * @return Route
     */
    public static function delete($name, $path, $action, $extraOptions = [])
    {
        $route = Route::delete($path, $action, $extraOptions);
        $route->setName($name);
        return $route;
    }

    /**
     * @return Route
     */
    public static function put($name, $path, $action, $extraOptions = [])
    {
        $route = Route::put($path, $action, $extraOptions);
        $route->setName($name);
        return $route;
    }

    /**
     * @return Route
     */
    public static function patch($name, $path, $action, $extraOptions = [])
    {
        $route = Route::patch($path, $action, $extraOptions);
        $route->setName($name);
        return $route;
    }

    /**
     * @return Route
     */
    public static function options($name, $path, $action, $extraOptions = [])
    {
        $route = Route::options($path, $action, $extraOptions);
        $route->setName($name);
        return $route;
    }

    /**
     * @return Route
     */
    public static function head($name, $path, $action, $extraOptions = [])
    {
        $route = Route::head($path, $action, $extraOptions);
        $route->setName($name);
        return $route;
    }

}