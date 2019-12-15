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

    /**
     * @return array
     */
    public function getDynamicRoutes($method)
    {
        return (array_key_exists($method, $this->dynamicRoutes) ? $this->dynamicRoutes[$method] : []);
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

        $optionsRoute = null;
        $isOptionsRoute = ($method === 'OPTIONS');

        if (!$isOptionsRoute)
        {
            $optionsRoute = Route::options($path, (function() {}), $route->getExtraOptions());
        }

        if (strpos($path, ':') !== false)
        {
            $pathParts = explode('/', $path);
            $pathPartsCount = count($pathParts);
            $this->dynamicRoutes[$method][$pathPartsCount][$path] = $route;
            // auto-register OPTIONS request
            if (!$isOptionsRoute)
            {
                $this->dynamicRoutes['OPTIONS'][$pathPartsCount][$path] = $optionsRoute;
            }
        }

        $this->registeredRoutes[$method][$path] = $route;

        // auto-register OPTIONS request
        if (!$isOptionsRoute)
        {
            $this->registeredRoutes['OPTIONS'][$path] = $optionsRoute;   
        }
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