<?php

namespace Intersect\Core\Http\Router;

class RouteGroup {

    private $groupName;
    private $routeConfig = [];
    private $extraOptions = [];

    public static function for($groupName, array $routeConfig, array $extraOptions = [])
    {
        $routeGroup = new RouteGroup();
        $routeGroup->groupName = $groupName;
        $routeGroup->routeConfig = $routeConfig;
        $routeGroup->extraOptions = $extraOptions;

        return $routeGroup;
    }

    public function getGroupName()
    {
        return $this->groupName;
    }

    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;
    }

    public function getRouteConfig()
    {
        return $this->routeConfig;
    }

    public function setRouteConfig(array $routeConfig)
    {
        $this->routeConfig = $routeConfig;
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