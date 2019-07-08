<?php

namespace Tests\Registry;

use PHPUnit\Framework\TestCase;
use Intersect\Core\Registry\ConfigRegistry;

class ConfigRegistryTest extends TestCase {

    public function test_registrationAndRetrieval() 
    {
        $configRegistry = new ConfigRegistry();
        $configRegistry->register([
            'key' => 'unit',
            'test' => [
                'foo' => 'bar'
            ]
        ]);

        $this->assertEquals('unit', $configRegistry->get('key'));
        $this->assertEquals('bar', $configRegistry->get('test.foo'));
        $this->assertNull($configRegistry->get('notfound'));
    }

    public function test_registrationAndRetrievalWithOverride() 
    {
        $configRegistry = new ConfigRegistry();
        $configRegistry->register([
            'key' => 'unit'
        ]);

        $configRegistry->register([
            'key' => 'override'
        ]);

        $this->assertEquals('override', $configRegistry->get('key'));
    }

    public function test_noKeysUsed() 
    {
        $configRegistry = new ConfigRegistry();
        
        $configRegistry->register([
            'routes' => [
                'test'
            ]
        ]);

        $configRegistry->register([
            'routes' => [
                'foo'
            ]
        ]);

        $routeConfig = $configRegistry->get('routes');
        $this->assertNotNull($routeConfig);
        $this->assertCount(2, $routeConfig);
        $this->assertTrue(in_array('test', $routeConfig));
        $this->assertTrue(in_array('foo', $routeConfig));
    }

}