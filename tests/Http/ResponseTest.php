<?php

namespace Tests\Http;

use PHPUnit\Framework\TestCase;
use Intersect\Core\Http\Response;

class ResponseTest extends TestCase {

    public function test_response()
    {
        $response = new Response('body');
        $this->assertEquals('body', $response->getBody());
        $this->assertEquals(200, $response->getStatus());
    }

    public function test_response_overrideStatusCode()
    {
        $response = new Response('body', 404);
        $this->assertEquals('body', $response->getBody());
        $this->assertEquals(404, $response->getStatus());
    }

}