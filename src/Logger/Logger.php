<?php

namespace Intersect\Core\Logger;

interface Logger {

    public function error($message);

    public function info($message);

    public function warn($message);

    public function debug($message);

}