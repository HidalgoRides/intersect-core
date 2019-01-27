<?php

namespace Intersect\Core\Logger;

use Intersect\Core\Exception\ObjectNotFoundException;
use Intersect\Storage\FileStorage;

class FileLogger implements Logger {

    /** @var string */
    private $destination;

    /** @var string */
    private $fileName;

    /** @var FileStorage */
    protected $fileStorage;

    public function __construct($fileName, $destination)
    {
        $this->fileStorage = new FileStorage();

        $this->fileName = $fileName;
        $this->destination = rtrim($destination, '/');
    }

    public function info($message)
    {
        $this->log('INFO: ' . $message);
    }

    public function error($message)
    {
        $this->log('ERROR: ' . $message);
    }

    public function warn($message)
    {
        $this->log('WARN: ' . $message);
    }

    public function debug($message)
    {
        $this->log('DEBUG: ' . $message);
    }

    /**
     * @param $message
     */
    private function log($message)
    {
        $this->checkForLogsDirectory();

        try {
            $this->fileStorage->appendFile($this->destination . '/' . $this->fileName, $this->getPrefix() . $message . PHP_EOL);
        } catch (ObjectNotFoundException $e) {
            //
        }
    }

    protected function getPrefix()
    {
        return '[' . date('Y-m-d H:i:s') . '] ';
    }

    private function checkForLogsDirectory()
    {
        $logsPath = $this->destination;
        if (!$this->fileStorage->directoryExists($logsPath))
        {
            $this->fileStorage->writeDirectory($logsPath);
        }
    }

}