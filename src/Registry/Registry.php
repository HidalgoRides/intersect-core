<?php

namespace Intersect\Core\Registry;

interface Registry {

    public function get($key);
    public function getAll();
    public function register($key, $obj);
    public function unregister($key);

}