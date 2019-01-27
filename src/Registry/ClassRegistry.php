<?php

namespace Intersect\Core\Registry;

use Intersect\Core\RegisteredClass;

class ClassRegistry extends AbstractRegistry {

    /**
     * @param $key
     * @param $class
     * @param bool $isSingleton
     */
    public function register($key, $class, $isSingleton = false)
    {
        parent::register($key, new RegisteredClass($class, $isSingleton));
    }

}