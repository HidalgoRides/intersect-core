<?php

namespace Tests\Http;

use PHPUnit\Framework\TestCase;
use Intersect\Core\Http\Request;

class RequestTest extends TestCase {

    /** @var Request $request */
    private $request;

    protected function setUp()
    {
        parent::setUp();

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/?unit=test';
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['SERVER_PORT'] = '8080';
        $_SERVER['HTTP_USER_AGENT'] = 'unit-test';

        $_GET['unit'] = 'test';
        $_POST['key'] = 'value';
        $_COOKIE['foo'] = 'bar';
        $_FILES['image'] = 'image';

        $this->request = Request::initFromGlobals();
    }

    public function test_getBaseUri()
    {
        $this->assertEquals('/', $this->request->getBaseUri());
    }

    public function test_getMethod()
    {
        $this->assertEquals('GET', $this->request->getMethod());
    }

    public function test_getFullUri()
    {
        $this->assertEquals('/?unit=test', $this->request->getFullUri());
    }

    public function test_getHost()
    {
        $this->assertEquals('localhost', $this->request->getHost());
    }

    public function test_getPort()
    {
        $this->assertEquals('8080', $this->request->getPort());
    }

    public function test_getUserAgent()
    {
        $this->assertEquals('unit-test', $this->request->getUserAgent());
    }

    public function test_data_fromGetRequest()
    {
        $this->assertEquals('test', $this->request->data('unit'));
    }

    public function test_data_fromPostRequest()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->request = Request::initFromGlobals();

        $this->assertEquals('value', $this->request->data('key'));
    }

    public function test_server()
    {
        $this->assertEquals('GET', $this->request->server('REQUEST_METHOD'));
    }

    public function test_data_fromPutRequest()
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';

        stream_wrapper_unregister('php');
        stream_wrapper_register('php', 'Tests\Http\TestPHPStreamWrapper');
        $this->request = Request::initFromGlobals();
        stream_wrapper_restore('php');

        $this->assertEquals('data', $this->request->data('input'));
    }

    public function test_cookie()
    {
        $this->assertEquals('bar', $this->request->cookie('foo'));
    }

    public function test_files()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->request = Request::initFromGlobals();

        $this->assertEquals('image', $this->request->files('image'));
    }

}

class TestPHPStreamWrapper {

    public $position = 0;
    public $bodyData = 'input=data';

    public function stream_open($path, $mode = "c", $options, &$opened_path) 
    {
        return true;
    }

    public function stream_read($count) 
    {
        $this->position += strlen($this->bodyData);
        if ($this->position > strlen($this->bodyData)) 
        {
            return false;
        }

        return $this->bodyData;
    }

    public function stream_eof() 
    {
        return $this->position >= strlen($this->bodyData);
    }

    public function stream_stat() 
    {
        return array('wrapper_data' => array('test'));
    }

    public function stream_close() {}

}