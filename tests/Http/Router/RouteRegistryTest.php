<?php

namespace Tests\Http\Router;

use Intersect\Core\Http\Router\NamedRoute;
use Intersect\Core\Http\Router\RouteRegistry;
use PHPUnit\Framework\TestCase;

class RouteRegistryTest extends TestCase {

    public function test_getByName()
    {
        $routeRegistry = new RouteRegistry();
        $routeRegistry->registerRoute(NamedRoute::get('test-name', '/name', function() {}));
        
        $route = $routeRegistry->getByName('test-name');
        
        $this->assertNotNull($route);
        $this->assertEquals('/name', $route->getPath());
    }
    
}