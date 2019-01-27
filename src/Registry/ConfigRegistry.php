<?php

namespace Intersect\Core\Registry;

class ConfigRegistry extends AbstractRegistry {

    private static $CONFIG_CACHE = [];

    public function flushCache()
    {
        self::$CONFIG_CACHE = [];
    }

    public function get($key)
    {
        if (array_key_exists($key, self::$CONFIG_CACHE))
        {
            return self::$CONFIG_CACHE[$key];
        }

        $keyParts = explode('.', $key);

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
        $this->registeredData = array_replace_recursive($this->registeredData, $configData);
    }

    public function unregister($key)
    {
        return;
    }

}