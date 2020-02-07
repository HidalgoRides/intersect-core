<?php

namespace Intersect\Core\Http;

class Request {

    private static $DATA_ROOT_COOKIE = 'COOKIES';
    private static $DATA_ROOT_GET = 'GET';
    private static $DATA_ROOT_POST = 'POST';
    private static $DATA_ROOT_PUT = 'PUT';
    private static $DATA_ROOT_FILES = 'FILES';
    private static $DATA_ROOT_SERVER = 'SERVER';
    
    private $authenticatedUserCallback;
    private $authenticatedUser;
    private $isAuthenticated;
    private $data = [];
    private $method = 'GET';
    private $parameters = [];

    /**
     * @return Request
     */
    public static function initFromGlobals()
    {
        $request = new self();
        $request->initData(self::$DATA_ROOT_SERVER, $_SERVER);
        $request->initData(self::$DATA_ROOT_COOKIE, $_COOKIE);

        if (array_key_exists('REQUEST_METHOD', $_SERVER))
        {
            $request->setMethod($_SERVER['REQUEST_METHOD']);
        }

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

        $uriParts = explode('?', $request->getFullUri());
        if (count($uriParts) > 1)
        {
            foreach (explode('&', $uriParts[1]) as $parameterPairs)
            {
                $parameterParts = explode('=', $parameterPairs);
                $key = $parameterParts[0];
                $value = (count($parameterParts) > 1) ? $parameterParts[1] : null;
                $request->addParameter($key, $value);
            }
        }

        return $request;
    }

    public function setAuthenticatedUserCallback(callable $callback)
    {
        $this->authenticatedUserCallback = $callback;
    }

    public function getAuthenticatedUser()
    {
        $authenticatedUser = null;

        if (is_null($this->authenticatedUser) && !is_null($this->authenticatedUserCallback))
        {
            $callback = $this->authenticatedUserCallback;
            $authenticatedUser = $callback($this);
        }

        $this->isAuthenticated = (!is_null($authenticatedUser));
        $this->authenticatedUser = $authenticatedUser;

        return $this->authenticatedUser;
    }

    public function isAuthenticated()
    {
        if (is_null($this->isAuthenticated))
        {
            $this->getAuthenticatedUser();
        }

        return $this->isAuthenticated;
    }

    public function addParameter($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    public function getParameter($key)
    {
        return (array_key_exists($key, $this->parameters) ? $this->parameters[$key] : null);
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setMethod($method)
    {
        $this->method = $method;
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

    public function setFullUri($uri)
    {
        $this->addServerData('REQUEST_URI', $uri);
    }

    public function getHost()
    {
        return $this->server('SERVER_NAME');
    }

    public function setHost($host)
    {
        $this->addServerData('SERVER_NAME', $host);
    }

    public function getPort()
    {
        return $this->server('SERVER_PORT');
    }

    public function setPort($port)
    {
        $this->addServerData('SERVER_PORT', $port);
    }

    public function getUserAgent()
    {
        return $this->server('HTTP_USER_AGENT');
    }

    public function setUserAgent($userAgent)
    {
        $this->addServerData('HTTP_USER_AGENT', $userAgent);
    }

    public function addData($key, $value)
    {
        $this->data[$this->getMethod()][$key] = $value;
    }

    public function addCookieData($key, $value)
    {
        $this->data[self::$DATA_ROOT_COOKIE][$key] = $value;
    }

    public function addFileData($key, $value)
    {
        $this->data[self::$DATA_ROOT_FILES][$key] = $value;
    }

    public function addServerData($key, $value)
    {
        $this->data[self::$DATA_ROOT_SERVER][$key] = $value;
    }

    public function getDataValue($key)
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

    public function getCookieValue($key)
    {
        return $this->getData(self::$DATA_ROOT_COOKIE, $key);
    }

    public function getFileValue($key)
    {
        return $this->getData(self::$DATA_ROOT_FILES, $key);
    }

    public function getServerValue($key)
    {
        return $this->getData(self::$DATA_ROOT_SERVER, $key);
    }

    /** @deprecated - use getDataValue instead */
    public function data($key)
    {
        return $this->getDataValue($key);
    }

    /** @deprecated - use getCookieValue instead */
    public function cookie($key)
    {
        return $this->getCookieValue($key);
    }

    /** @deprecated - use getFileValue instead */
    public function files($key)
    {
        return $this->getFileValue($key);
    }

    /** @deprecated - use getServerValue instead */
    public function server($key)
    {
        return $this->getServerValue($key);
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