<?php

namespace Intersect\Core\Http;

class Request {

    private static $DATA_ROOT_COOKIE = 'COOKIES';
    private static $DATA_ROOT_GET = 'GET';
    private static $DATA_ROOT_POST = 'POST';

    private $baseUri;
    private $data = [];
    private $fullUri;
    private $host;
    private $method;
    private $port;
    private $userAgent;

    /**
     * @return Request
     */
    public static function initFromGlobals()
    {
        $request = new self();

        $request->initData(self::$DATA_ROOT_GET, $_GET);
        $request->initData(self::$DATA_ROOT_POST, $_POST);
        $request->initData(self::$DATA_ROOT_COOKIE, $_COOKIE);

        $request->method = $_SERVER['REQUEST_METHOD'];

        $requestUri = $_SERVER['REQUEST_URI'];
        $request->fullUri = $requestUri;

        $requestUriParts = explode('?', $requestUri);
        $request->baseUri = $requestUriParts[0];

        $request->host = $_SERVER['SERVER_NAME'];
        $request->port = $_SERVER['SERVER_PORT'];

        $request->userAgent = $_SERVER['HTTP_USER_AGENT'];

        return $request;
    }

    private function __construct() {}

    public function getMethod()
    {
        return $this->method;
    }

    public function getBaseUri()
    {
        return $this->baseUri;
    }

    public function getFullUri()
    {
        return $this->fullUri;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getUserAgent()
    {
        return $this->userAgent;
    }

    public function get($key)
    {
        return $this->getData(self::$DATA_ROOT_GET, $key);
    }

    public function post($key)
    {
        return $this->getData(self::$DATA_ROOT_POST, $key);
    }

    public function cookie($key)
    {
        return $this->getData(self::$DATA_ROOT_COOKIE, $key);
    }
    
    private function getData($rootKey, $key)
    {
        return (isset($this->data[$rootKey][$key])) ? $this->data[$rootKey][$key] : null;
    }

    private function initData($rootKey, $data)
    {
        $this->data[$rootKey] = [];

        foreach ($data as $key => $value)
        {
            $this->data[$rootKey][$key] = $value;
        }
    }

}