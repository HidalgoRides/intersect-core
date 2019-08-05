<?php

namespace Intersect\Core;

class RegisteredClass {

    private $class;
    private $isSingleton;

    public function __construct($class, bool $isSingleton)
    {
        $this->class = $class;
        $this->isSingleton = $isSingleton;
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