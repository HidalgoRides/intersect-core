<?php

namespace Intersect\Core\Registry;

abstract class AbstractRegistry implements Registry {

    protected $registeredData = [];

    /**
     * @param $key
     * @return mixed|null
     */
    public function get($key)
    {
        return (array_key_exists($key, $this->registeredData) ? $this->registeredData[$key] : null);
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->registeredData;
    }

    /**
     * @param $key
     * @param $obj
     */
    public function register($key, $obj)
    {
        $this->registeredData[$key] = $obj;
    }

    /**
     * @param $key
     */
    public function unregister($key)
    {
        if (array_key_exists($key, $this->registeredData))
        {
            unset($this->registeredData[$key]);
        }
    }

}