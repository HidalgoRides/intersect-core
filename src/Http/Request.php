<?php

namespace Intersect\Core\Http;

class Request {

    private static $DATA_ROOT_COOKIE = 'COOKIES';
    private static $DATA_ROOT_GET = 'GET';
    private static $DATA_ROOT_POST = 'POST';
    private static $DATA_ROOT_PUT = 'PUT';
    private static $DATA_ROOT_FILES = 'FILES';
    private static $DATA_ROOT_SESSION = 'SESSION';
    private static $DATA_ROOT_SERVER = 'SERVER';
    
    private $authenticatedUser;
    private $data = [];
    private $method = 'GET';
    private $parameters = [];

    /**
     * @return Request
     */
    public static function initFromGlobals()
    {
        $request = new static();

        if (isset($_COOKIE))
        {
            $request->initData(self::$DATA_ROOT_COOKIE, $_COOKIE);
        }
        
        if (isset($_SESSION))
        {
            $request->initData(self::$DATA_ROOT_SESSION, $_SESSION);
        }

        if (isset($_SERVER))
        {
            $request->initData(self::$DATA_ROOT_SERVER, $_SERVER);

            if (array_key_exists('REQUEST_METHOD', $_SERVER))
            {
                $request->setMethod($_SERVER['REQUEST_METHOD']);
            }
        }

        switch ($request->getMethod())
        {
            case 'GET':
                if (isset($_GET))
                {
                    $request->initData(self::$DATA_ROOT_GET, $_GET);
                }
                break;
            case 'POST':
                if (isset($_POST))
                {
                    $request->initData(self::$DATA_ROOT_POST, $_POST);
                }
                
                if (isset($_FILES))
                {
                    $request->initData(self::$DATA_ROOT_FILES, $_FILES);
                }

                $request->initData(self::$DATA_ROOT_POST, self::getDataFromInputStream());
                
                break;
            case 'PUT':
                $request->initData(self::$DATA_ROOT_PUT, self::getDataFromInputStream());

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

    public function getAuthenticatedUser()
    {
        return $this->authenticatedUser;
    }

    public function setAuthenticatedUser($authenticatedUser)
    {
        $this->authenticatedUser = $authenticatedUser;
    }

    public function isAuthenticated()
    {
        return (!is_null($this->authenticatedUser));
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

    public function addSessionData($key, $value)
    {
        $this->data[self::$DATA_ROOT_SESSION][$key] = $value;
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

    public function getSessionValue($key)
    {
        return $this->getData(self::$DATA_ROOT_SESSION, $key);
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

    private static function getDataFromInputStream()
    {
        $data = null;
        $inputString = file_get_contents("php://input");
                
        if (trim($inputString) !== '') 
        {
            $inputVariables = json_decode($inputString, true);
            if (is_null($inputVariables))
            {
                parse_str($inputString, $inputVariables);
            }

            $data = $inputVariables;
        }

        return $data;
    }

    private function initData($rootKey, $data)
    {
        if (!array_key_exists($rootKey, $this->data))
        {
            $this->data[$rootKey] = [];
        }

        if (!is_null($data) && is_array($data))
        {
            foreach ($data as $key => $value)
            {
                $this->data[$rootKey][$key] = $value;
            }
        }
    }

}