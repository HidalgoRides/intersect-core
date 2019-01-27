<?php

namespace Intersect\Core;

class RegisteredClass {

    private $class;
    private $isSingleton;

    public function __construct($class, $isSingleton)
    {
        $this->class = $class;
        $this->isSingleton = boolval($isSingleton);
    }

    public function getClass()
    {
        return $this->class;
    }

    public function isSingleton()
    {
        return $this->isSingleton;
    }

}