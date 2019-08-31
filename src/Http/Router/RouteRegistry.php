<?php

namespace Intersect\Core\Http\Router;

class RouteRegistry {

    protected $registeredRoutes = [];
    protected $dynamicRoutes = [];

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->registeredRoutes;
    }

    public function getDynamicRoutes()
    {
        return $this->dynamicRoutes;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function get($method, $path = null)
    {
        $routes = (array_key_exists($method, $this->registeredRoutes) ? $this->registeredRoutes[$method] : []);

        if (is_null($routes) || count($routes) == 0 || is_null($path))
        {
            return $routes;
        }

        return (array_key_exists($path, $routes) ? $routes[$path] : null);
    }

    /**
     * @param Route $route
     */
    public function registerRoute(Route $route)
    {
        $method = $route->getMethod();
        $path = $route->getPath();

        if (strpos($path, ':') !== false)
        {
            $pathPaths = explode('/', $path);
            $this->dynamicRoutes[count($pathPaths)][$path] = $route;
        }

        $this->registeredRoutes[$method][$path] = $route;
    }

    public function registerRouteGroup(RouteGroup $routeGroup)
    {
        $routeGroupConfig = $routeGroup->getRouteConfig();
        $extraOptions = $routeGroup->getExtraOptions();
        
        foreach ($routeGroupConfig as $method => $route)
        {
            if ($route instanceof Route)
            {
                if (array_key_exists('prefix', $extraOptions))
                {
                    $path = '/' . trim($extraOptions['prefix'], '/') . rtrim($route->getPath(), '/');
                    $route->setPath($path);
                }

                if (count($extraOptions) > 0)
                {
                    $route->setExtraOptions(array_merge_recursive($extraOptions, $route->getExtraOptions()));
                }

                $this->registerRoute($route);
            }
        }
    }

    /**
     * @param $key
     */
    public function unregister($key)
    {
        if (array_key_exists($key, $this->registeredRoutes))
        {
            unset($this->registeredRoutes[$key]);
        }
    }

}