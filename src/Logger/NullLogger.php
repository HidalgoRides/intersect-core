<?php

namespace Intersect\Core\Logger;

class NullLogger implements Logger {

    public function info($message = null) {}

    public function error($message) {}

    public function warn($message) {}

    public function debug($message) {}

}