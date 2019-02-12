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

}