<?php

namespace Tests\Http;

use PHPUnit\Framework\TestCase;
use Intersect\Core\Http\Request;

class RequestTest extends TestCase {

    protected function setUp()
    {
        parent::setUp();

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/?unit=test&foo=bar';
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['SERVER_PORT'] = '8080';
        $_SERVER['HTTP_USER_AGENT'] = 'unit-test';

        $_GET['unit'] = 'test';
        $_POST['key'] = 'value';
        $_COOKIE['foo'] = 'bar';
        $_FILES['image'] = 'image';
    }

    public function test_getBaseUri()
    {
        $request = Request::initFromGlobals();
        $this->assertEquals('/', $request->getBaseUri());

        $request = new Request();
        $request->setFullUri('/?unit=test');
        $this->assertEquals('/', $request->getBaseUri());
    }

    public function test_getParameter()
    {
        $request = Request::initFromGlobals();
        $this->assertEquals('test', $request->getParameter('unit'));
        $this->assertEquals('bar', $request->getParameter('foo'));
        $this->assertNull($request->getParameter('invalid'));

        $request = new Request();
        $request->addParameter('key', 'value');
        $this->assertEquals('value', $request->getParameter('key'));
    }

    public function test_getParameters()
    {
        $request = Request::initFromGlobals();
        $this->assertEquals(2, count($request->getParameters()));

        $request = new Request();
        $request->setFullUri('/?unit=test');
        $this->assertEquals('/', $request->getBaseUri());
    }

    public function test_getMethod()
    {
        $request = Request::initFromGlobals();
        $this->assertEquals('GET', $request->getMethod());

        $request = new Request();
        $this->assertEquals('GET', $request->getMethod());
    }

    public function test_getFullUri()
    {
        $request = Request::initFromGlobals();
        $this->assertEquals('/?unit=test&foo=bar', $request->getFullUri());

        $request = new Request();
        $request->setFullUri('/?unit=test&foo=bar');
        $this->assertEquals('/?unit=test&foo=bar', $request->getFullUri());
    }

    public function test_getHost()
    {
        $request = Request::initFromGlobals();
        $this->assertEquals('localhost', $request->getHost());

        $request = new Request();
        $request->setHost('localhost');
        $this->assertEquals('localhost', $request->getHost());
    }

    public function test_getPort()
    {
        $request = Request::initFromGlobals();
        $this->assertEquals('8080', $request->getPort());

        $request = new Request();
        $request->setPort('8080');
        $this->assertEquals('8080', $request->getPort());
    }

    public function test_getUserAgent()
    {
        $request = Request::initFromGlobals();
        $this->assertEquals('unit-test', $request->getUserAgent());

        $request = new Request();
        $request->setUserAgent('unit-test');
        $this->assertEquals('unit-test', $request->getUserAgent());
    }

    public function test_data_fromGetRequest()
    {
        $request = Request::initFromGlobals();
        $this->assertEquals('test', $request->data('unit'));

        $request = new Request();
        $request->addData('get', 'data');
        $this->assertEquals('data', $request->getDataValue('get'));
    }

    public function test_data_fromPostRequest()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $request = Request::initFromGlobals();

        $this->assertEquals('value', $request->data('key'));

        $request = new Request();
        $request->setMethod('POST');
        $request->addData('post', 'data');
        $this->assertEquals('data', $request->getDataValue('post'));
    }

    public function test_data_fromPostRequest_jsonString()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        stream_wrapper_unregister('php');
        stream_wrapper_register('php', 'Tests\Http\TestJsonStringStreamWrapper');
        $request = Request::initFromGlobals();
        stream_wrapper_restore('php');

        $this->assertEquals('data', $request->data('input'));

        $request = new Request();
        $request->setMethod('POST');
        $request->addData('post', 'data');
        $this->assertEquals('data', $request->getDataValue('post'));
    }

    public function test_server()
    {
        $request = Request::initFromGlobals();
        $this->assertEquals('GET', $request->server('REQUEST_METHOD'));

        $request = new Request();
        $request->setMethod('POST');
        $this->assertEquals('POST', $request->getMethod());
    }

    public function test_data_fromPutRequest()
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';

        stream_wrapper_unregister('php');
        stream_wrapper_register('php', 'Tests\Http\TestPHPStreamWrapper');
        $request = Request::initFromGlobals();
        stream_wrapper_restore('php');

        $this->assertEquals('data', $request->data('input'));

        $request = new Request();
        $request->setMethod('PUT');
        $request->addData('put', 'data');
        $this->assertEquals('data', $request->getDataValue('put'));
    }

    public function test_data_fromPutRequest_jsonString()
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';

        stream_wrapper_unregister('php');
        stream_wrapper_register('php', 'Tests\Http\TestJsonStringStreamWrapper');
        $request = Request::initFromGlobals();
        stream_wrapper_restore('php');

        $this->assertEquals('data', $request->data('input'));

        $request = new Request();
        $request->setMethod('PUT');
        $request->addData('put', 'data');
        $this->assertEquals('data', $request->getDataValue('put'));
    }

    public function test_cookie()
    {
        $request = Request::initFromGlobals();
        $this->assertEquals('bar', $request->cookie('foo'));

        $request = new Request();
        $request->addCookieData('foo', 'bar');
        $this->assertEquals('bar', $request->getCookieValue('foo'));
    }

    public function test_session()
    {
        $_SESSION['foo'] = 'bar';
        $request = Request::initFromGlobals();
        $this->assertEquals('bar', $request->getSessionValue('foo'));

        $request = new Request();
        $request->addSessionData('foo', 'bar');
        $this->assertEquals('bar', $request->getSessionValue('foo'));
    }

    public function test_files()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $request = Request::initFromGlobals();

        $this->assertEquals('image', $request->files('image'));

        $request = new Request();
        $request->addFileData('image', 'test');
        $this->assertEquals('test', $request->getFileValue('image'));
    }

    public function test_isAuthenticated_noAuthenticatedUserSet()
    {
        $request = new Request();
        $this->assertFalse($request->isAuthenticated());
    }

    public function test_isAuthenticated_authenticatedUserSet()
    {
        $request = new Request();
        $request->setAuthenticatedUser(123);

        $this->assertTrue($request->isAuthenticated());
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

class TestJsonStringStreamWrapper {

    public $position = 0;
    public $bodyData = '{"input":"data"}';

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