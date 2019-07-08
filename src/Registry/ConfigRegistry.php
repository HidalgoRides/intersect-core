<?php

namespace Intersect\Core\Registry;

class ConfigRegistry extends AbstractRegistry {

    private static $CONFIG_CACHE = [];
    private $delimiter = '.';

    public function __construct($delimiter = '.')
    {
        $this->delimiter = $delimiter;
    }

    public static function flushCache()
    {
        self::$CONFIG_CACHE = [];
    }

    public function get($key)
    {
        if (array_key_exists($key, self::$CONFIG_CACHE))
        {
            return self::$CONFIG_CACHE[$key];
        }

        $keyParts = explode($this->delimiter, $key);

        $configs = $this->getAll();
        $configData = null;

        foreach ($keyParts as $keyPart)
        {
            if (is_array($configs) && array_key_exists($keyPart, $configs))
            {
                $configData = $configs[$keyPart];
                $configs = $configData;
            }
            else
            {
                $configData = null;
                break;
            }
        }

        self::$CONFIG_CACHE[$key] = $configData;

        return $configData;
    }

    public function register($configData, $obj = null)
    {
        $this->registeredData = $this->combineData($this->registeredData, $configData);
        self::flushCache();
    }

    public function unregister($key)
    {
        return;
    }

    private function combineData($registeredData, $newData)
    {
        while(list($key, $value) = each($newData))
        {
            if (is_array($value) && array_key_exists($key, $registeredData) && is_array($registeredData[$key])) 
            {
                if (array_keys($value) !== range(0, count($value) - 1))
                {
                    $registeredData[$key] = $this->combineData($registeredData[$key], $value);
                }
                else
                {
                    $registeredData[$key] = array_merge_recursive($registeredData[$key], $value);
                }
            }
            else 
            {
                $registeredData[$key] = $value;
            }
        }

        return $registeredData;
    }

}