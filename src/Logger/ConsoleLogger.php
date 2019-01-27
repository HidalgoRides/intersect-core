<?php

namespace Intersect\Core\Logger;

class ConsoleLogger implements Logger {

    public function info($message = null)
    {
        $this->log($message);
    }

    public function error($message)
    {
        $this->log('Error: '. $message);
    }

    public function warn($message)
    {
        $this->log('Warning: ' . $message);
    }

    public function debug($message)
    {
        $this->log('Debug: ' . $message);
    }

    /**
     * @param $message
     */
    private function log($message)
    {
        echo $message . PHP_EOL;
    }

}