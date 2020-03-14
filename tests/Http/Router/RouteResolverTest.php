<?php

namespace Tests\Http\Router;

use Intersect\Core\Http\Router\NamedRoute;
use PHPUnit\Framework\TestCase;
use Intersect\Core\Http\Router\Route;
use Intersect\Core\Http\Router\RouteGroup;
use Intersect\Core\Http\Router\RouteRegistry;
use Intersect\Core\Http\Router\RouteResolver;

class RouteResolverTest extends TestCase {

    /** @var RouteResolver $routeResolver */
    private $routeResolver;

    /** @var RouteRegistry */
    private $routeRegistry;

    protected function setUp()
    {
        $this->routeRegistry = new RouteRegistry();
        $this->routeRegistry->registerRoute(Route::get('/classpath', 'Tests\Controllers\TestController#index'));
        $this->routeRegistry->registerRoute(Route::get('/classpath-with-params/:id', 'Tests\Controllers\TestController#index2'));
        $this->routeRegistry->registerRoute(Route::get('/closure', (function() {})));
        $this->routeRegistry->registerRoute(Route::get('/closure-with-params/:id', (function() {})));

        $this->routeRegistry->registerRouteGroup(RouteGroup::for('test', [
            Route::get('/group', 'Tests\Controllers\TestController#group')
        ]));

        $this->routeRegistry->registerRouteGroup(RouteGroup::for('prefix', [
            Route::get('/test', 'Tests\Controllers\TestController#prefix'),
            Route::get('/test2', 'Tests\Controllers\TestController#prefix2')
        ], ['prefix' => 'prefix']));

        $this->routeRegistry->registerRoute(Route::get('/extra', 'Tests\Controllers\TestController#extra', [
            'before' => 'test'
        ]));

        $this->routeRegistry->registerRouteGroup(RouteGroup::for('nested-groups', [
            Route::get('/nest', 'Tests\Controllers\TestController#nest'),
            RouteGroup::for('nested-groups-2', [
                Route::get('/nest2', 'Tests\Controllers\TestController#nest2')
            ], ['prefix' => 'test'])
        ], ['prefix' => 'unit']));

        $this->routeResolver = new RouteResolver($this->routeRegistry);
    }

    public function test_resolve_get() 
    {
        $this->routeRegistry->registerRoute(Route::get('/get', 'Tests\Controllers\TestController#index'));

        /** @var RouteAction $routeAction */
        $routeAction = $this->routeResolver->resolve('GET', '/get');

        $this->assertNotNull($routeAction);
        $this->assertFalse($routeAction->getIsCallable());
        $this->assertEquals('Tests\Controllers\TestController', $routeAction->getController());
        $this->assertEquals('index', $routeAction->getMethod());
        $this->assertCount(0, $routeAction->getNamedParameters());
    }

    public function test_resolve_post() 
    {
        $this->routeRegistry->registerRoute(Route::post('/post', 'Tests\Controllers\TestController#index'));

        /** @var RouteAction $routeAction */
        $routeAction = $this->routeResolver->resolve('POST', '/post');

        $this->assertNotNull($routeAction);
        $this->assertFalse($routeAction->getIsCallable());
        $this->assertEquals('Tests\Controllers\TestController', $routeAction->getController());
        $this->assertEquals('index', $routeAction->getMethod());
        $this->assertCount(0, $routeAction->getNamedParameters());
    }

    public function test_resolve_delete() 
    {
        $this->routeRegistry->registerRoute(Route::delete('/delete', 'Tests\Controllers\TestController#index'));

        /** @var RouteAction $routeAction */
        $routeAction = $this->routeResolver->resolve('DELETE', '/delete');

        $this->assertNotNull($routeAction);
        $this->assertFalse($routeAction->getIsCallable());
        $this->assertEquals('Tests\Controllers\TestController', $routeAction->getController());
        $this->assertEquals('index', $routeAction->getMethod());
        $this->assertCount(0, $routeAction->getNamedParameters());
    }

    public function test_resolve_put() 
    {
        $this->routeRegistry->registerRoute(Route::put('/put', 'Tests\Controllers\TestController#index'));

        /** @var RouteAction $routeAction */
        $routeAction = $this->routeResolver->resolve('PUT', '/put');

        $this->assertNotNull($routeAction);
        $this->assertFalse($routeAction->getIsCallable());
        $this->assertEquals('Tests\Controllers\TestController', $routeAction->getController());
        $this->assertEquals('index', $routeAction->getMethod());
        $this->assertCount(0, $routeAction->getNamedParameters());
    }

    public function test_resolve_patch() 
    {
        $this->routeRegistry->registerRoute(Route::patch('/patch', 'Tests\Controllers\TestController#index'));

        /** @var RouteAction $routeAction */
        $routeAction = $this->routeResolver->resolve('PATCH', '/patch');

        $this->assertNotNull($routeAction);
        $this->assertFalse($routeAction->getIsCallable());
        $this->assertEquals('Tests\Controllers\TestController', $routeAction->getController());
        $this->assertEquals('index', $routeAction->getMethod());
        $this->assertCount(0, $routeAction->getNamedParameters());
    }

    public function test_resolve_head() 
    {
        $this->routeRegistry->registerRoute(Route::head('/head', 'Tests\Controllers\TestController#index'));

        /** @var RouteAction $routeAction */
        $routeAction = $this->routeResolver->resolve('HEAD', '/head');

        $this->assertNotNull($routeAction);
        $this->assertFalse($routeAction->getIsCallable());
        $this->assertEquals('Tests\Controllers\TestController', $routeAction->getController());
        $this->assertEquals('index', $routeAction->getMethod());
        $this->assertCount(0, $routeAction->getNamedParameters());
    }

    public function test_resolve_head_withOnlyGetRouteRegistered() 
    {
        $this->routeRegistry->registerRoute(Route::get('/get', 'Tests\Controllers\TestController#index'));

        /** @var RouteAction $routeAction */
        $routeAction = $this->routeResolver->resolve('HEAD', '/get');

        $this->assertNotNull($routeAction);
        $this->assertFalse($routeAction->getIsCallable());
        $this->assertEquals('Tests\Controllers\TestController', $routeAction->getController());
        $this->assertEquals('index', $routeAction->getMethod());
        $this->assertCount(0, $routeAction->getNamedParameters());
    }

    public function test_resolve_head_withNoGetOrHeadRoutesRegistered() 
    {
        /** @var RouteAction $routeAction */
        $routeAction = $this->routeResolver->resolve('HEAD', '/get');
        $this->assertNull($routeAction);
    }

    public function test_resolve_options() 
    {
        $this->routeRegistry->registerRoute(Route::options('/options', 'Tests\Controllers\TestController#index'));

        /** @var RouteAction $routeAction */
        $routeAction = $this->routeResolver->resolve('OPTIONS', '/options');

        $this->assertNotNull($routeAction);
        $this->assertFalse($routeAction->getIsCallable());
        $this->assertEquals('Tests\Controllers\TestController', $routeAction->getController());
        $this->assertEquals('index', $routeAction->getMethod());
        $this->assertCount(0, $routeAction->getNamedParameters());
    }

    public function test_resolve_routeNotFound() 
    {
        /** @var RouteAction $routeAction */
        $routeAction = $this->routeResolver->resolve('GET', '/not-found');

        $this->assertNull($routeAction);
    }

    public function test_resolve_routeAsIndexPage() 
    {
        $this->routeRegistry->registerRoute(Route::get('/', 'Tests\Controllers\TestController#index'));

        /** @var RouteAction $routeAction */
        $routeAction = $this->routeResolver->resolve('GET', '/');

        $this->assertNotNull($routeAction);
        $this->assertFalse($routeAction->getIsCallable());
        $this->assertEquals('Tests\Controllers\TestController', $routeAction->getController());
        $this->assertEquals('index', $routeAction->getMethod());
        $this->assertCount(0, $routeAction->getNamedParameters());
    }

    public function test_resolve_routeAsIndexPageWithVariablePassed() 
    {
        $this->routeRegistry->registerRoute(Route::get('/:id', 'Tests\Controllers\TestController#index'));

        /** @var RouteAction $routeAction */
        $routeAction = $this->routeResolver->resolve('GET', '/123');

        $this->assertNotNull($routeAction);
        $this->assertFalse($routeAction->getIsCallable());
        $this->assertEquals('Tests\Controllers\TestController', $routeAction->getController());
        $this->assertEquals('index', $routeAction->getMethod());
        $this->assertCount(1, $routeAction->getNamedParameters());
    }

    public function test_resolve_routeAsIndexPageWithoutVariablePassed() 
    {
        $this->routeRegistry->registerRoute(Route::get('/:id', 'Tests\Controllers\TestController#index'));

        /** @var RouteAction $routeAction */
        $routeAction = $this->routeResolver->resolve('GET', '/');

        $this->assertNotNull($routeAction);
        $this->assertFalse($routeAction->getIsCallable());
        $this->assertEquals('Tests\Controllers\TestController', $routeAction->getController());
        $this->assertEquals('index', $routeAction->getMethod());
        $this->assertCount(1, $routeAction->getNamedParameters());
    }

    public function test_resolve_routeAsClassPath() 
    {
        /** @var RouteAction $routeAction */
        $routeAction = $this->routeResolver->resolve('GET', '/classpath');

        $this->assertNotNull($routeAction);
        $this->assertFalse($routeAction->getIsCallable());
        $this->assertEquals('Tests\Controllers\TestController', $routeAction->getController());
        $this->assertEquals('index', $routeAction->getMethod());
        $this->assertCount(0, $routeAction->getNamedParameters());
    }

    public function test_resolve_routeAsClassPathWithParameters() 
    {
        /** @var RouteAction $routeAction */
        $routeAction = $this->routeResolver->resolve('GET', '/classpath-with-params/123');

        $this->assertNotNull($routeAction);
        $this->assertFalse($routeAction->getIsCallable());
        $this->assertEquals('Tests\Controllers\TestController', $routeAction->getController());
        $this->assertEquals('index2', $routeAction->getMethod());
        $this->assertCount(1, $routeAction->getNamedParameters());
        $this->assertTrue(array_key_exists('id', $routeAction->getNamedParameters()));
        $this->assertEquals(123, $routeAction->getNamedParameters()['id']);
    }

    public function test_resolve_routeAsClosure() 
    {
        /** @var RouteAction $routeAction */
        $routeAction = $this->routeResolver->resolve('GET', '/closure');

        $this->assertNotNull($routeAction);
        $this->assertTrue($routeAction->getIsCallable());
        $this->assertNull($routeAction->getController());
        $this->assertNotNull($routeAction->getMethod());
        $this->assertTrue($routeAction->getMethod() instanceof \Closure);
    }

    public function test_resolve_routeAsClosureWithParameters() 
    {
        /** @var RouteAction $routeAction */
        $routeAction = $this->routeResolver->resolve('GET', '/closure-with-params/123');

        $this->assertNotNull($routeAction);
        $this->assertTrue($routeAction->getIsCallable());
        $this->assertNull($routeAction->getController());
        $this->assertNotNull($routeAction->getMethod());
        $this->assertTrue($routeAction->getMethod() instanceof \Closure);
        $this->assertCount(1, $routeAction->getNamedParameters());
        $this->assertTrue(array_key_exists('id', $routeAction->getNamedParameters()));
        $this->assertEquals(123, $routeAction->getNamedParameters()['id']);
    }

    public function test_resolve_routeInsideOfRouteGroup() 
    {
        /** @var RouteAction $routeAction */
        $routeAction = $this->routeResolver->resolve('GET', '/group');

        $this->assertNotNull($routeAction);
        $this->assertFalse($routeAction->getIsCallable());
        $this->assertEquals('Tests\Controllers\TestController', $routeAction->getController());
        $this->assertEquals('group', $routeAction->getMethod());
        $this->assertCount(0, $routeAction->getNamedParameters());
    }

    public function test_resolve_routeRouteGroupWithPrefix() 
    {
        /** @var RouteAction $routeAction */
        $routeAction = $this->routeResolver->resolve('GET', '/prefix/test');

        $this->assertNotNull($routeAction);
        $this->assertFalse($routeAction->getIsCallable());
        $this->assertEquals('Tests\Controllers\TestController', $routeAction->getController());
        $this->assertEquals('prefix', $routeAction->getMethod());
        $this->assertCount(0, $routeAction->getNamedParameters());
    }

    public function test_resolve_routeWithExtraOptions() 
    {
        /** @var RouteAction $routeAction */
        $routeAction = $this->routeResolver->resolve('GET', '/extra');

        $this->assertNotNull($routeAction);
        $this->assertFalse($routeAction->getIsCallable());
        $this->assertEquals('Tests\Controllers\TestController', $routeAction->getController());
        $this->assertEquals('extra', $routeAction->getMethod());
        $this->assertCount(0, $routeAction->getNamedParameters());
        $this->assertCount(1, $routeAction->getExtraOptions());
        $this->assertArrayHasKey('before', $routeAction->getExtraOptions());
    }

    public function test_resolveFromName() 
    {
        $this->routeRegistry->registerRoute(NamedRoute::get('test', '/', function(){}));

        /** @var RouteAction $routeAction */
        $routeAction = $this->routeResolver->resolveFromName('test');

        $this->assertNotNull($routeAction);
        $this->assertTrue($routeAction->getIsCallable());
    }

    public function test_resolve_autoRegisteredOptionsRequest() 
    {
        $this->routeRegistry->registerRoute(Route::delete('/delete/:me', 'Tests\Controllers\TestController#delete'));

        /** @var RouteAction $routeAction */
        $routeAction = $this->routeResolver->resolve('OPTIONS', '/delete/me');

        $this->assertNotNull($routeAction);
        $this->assertNull($routeAction->getController());
        $this->assertNull($routeAction->getMethod());
        $this->assertCount(1, $routeAction->getNamedParameters());
    }

    public function test_resolve_autoRegisteredOptionsRequestWithOverride() 
    {
        $this->routeRegistry->registerRoute(Route::delete('/delete/:me', 'Tests\Controllers\TestController#delete'));
        $this->routeRegistry->registerRoute(Route::options('/delete/:me', 'Tests\Controllers\TestController#options'));

        /** @var RouteAction $routeAction */
        $routeAction = $this->routeResolver->resolve('OPTIONS', '/delete/me');

        $this->assertNotNull($routeAction);
        $this->assertNotNull($routeAction);
        $this->assertFalse($routeAction->getIsCallable());
        $this->assertEquals('Tests\Controllers\TestController', $routeAction->getController());
        $this->assertEquals('options', $routeAction->getMethod());
        $this->assertCount(1, $routeAction->getNamedParameters());
    }

    public function test_resolve_nestedRouteGroups() 
    {
        /** @var RouteAction $routeAction */
        $routeAction = $this->routeResolver->resolve('GET', '/unit/nest');

        $this->assertNotNull($routeAction);
        $this->assertFalse($routeAction->getIsCallable());
        $this->assertEquals('Tests\Controllers\TestController', $routeAction->getController());
        $this->assertEquals('nest', $routeAction->getMethod());
        $this->assertCount(0, $routeAction->getNamedParameters());
        $this->assertCount(1, $routeAction->getExtraOptions());

        /** @var RouteAction $routeAction */
        $routeAction = $this->routeResolver->resolve('GET', '/unit/test/nest2');

        $this->assertNotNull($routeAction);
        $this->assertFalse($routeAction->getIsCallable());
        $this->assertEquals('Tests\Controllers\TestController', $routeAction->getController());
        $this->assertEquals('nest2', $routeAction->getMethod());
        $this->assertCount(0, $routeAction->getNamedParameters());
        $this->assertCount(1, $routeAction->getExtraOptions());
    }

}