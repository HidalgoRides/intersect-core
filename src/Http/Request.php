<?php

namespace Intersect\Core\Http;

class Request {

    private static $DATA_ROOT_COOKIE = 'COOKIES';
    private static $DATA_ROOT_GET = 'GET';
    private static $DATA_ROOT_POST = 'POST';
    private static $DATA_ROOT_PUT = 'PUT';
    private static $DATA_ROOT_FILES = 'FILES';
    private static $DATA_ROOT_SERVER = 'SERVER';

    private $data = [];

    /**
     * @return Request
     */
    public static function initFromGlobals()
    {
        $request = new self();
        $request->initData(self::$DATA_ROOT_SERVER, $_SERVER);
        $request->initData(self::$DATA_ROOT_COOKIE, $_COOKIE);

        switch ($request->getMethod())
        {
            case 'GET':
                $request->initData(self::$DATA_ROOT_GET, $_GET);
                break;
            case 'POST':
                $request->initData(self::$DATA_ROOT_POST, $_POST);
                $request->initData(self::$DATA_ROOT_FILES, $_FILES);
                break;
            case 'PUT':
                parse_str(file_get_contents("php://input"), $putVariables);
                $request->initData(self::$DATA_ROOT_PUT, $putVariables);
                break;
        }

        return $request;
    }

    private function __construct() {}

    public function getMethod()
    {
        return $this->server('REQUEST_METHOD');
    }

    public function getBaseUri()
    {
        $requestUriParts = explode('?', $this->getFullUri());
        return $requestUriParts[0];
    }

    public function getFullUri()
    {
        return $this->server('REQUEST_URI');
    }

    public function getHost()
    {
        return $this->server('SERVER_NAME');
    }

    public function getPort()
    {
        return $this->server('SERVER_PORT');
    }

    public function getUserAgent()
    {
        return $this->server('HTTP_USER_AGENT');
    }

    public function data($key)
    {
        $data = null;
        
        switch ($this->getMethod())
        {
            case 'GET':
                $data = $this->getData(self::$DATA_ROOT_GET, $key);
                break;
            case 'POST':
                $data = $this->getData(self::$DATA_ROOT_POST, $key);
                break;
            case 'PUT':
                $data = $this->getData(self::$DATA_ROOT_PUT, $key);
                break;
        }

        return $data;
    }

    public function cookie($key)
    {
        return $this->getData(self::$DATA_ROOT_COOKIE, $key);
    }

    public function files($key)
    {
        return $this->getData(self::$DATA_ROOT_FILES, $key);
    }

    public function server($key)
    {
        return $this->getData(self::$DATA_ROOT_SERVER, $key);
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