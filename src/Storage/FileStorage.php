<?php

namespace Intersect\Core\Storage;

use Intersect\Core\Exception\ObjectNotFoundException;

class FileStorage {

    public function __construct() {}

    /**
     * @param $path
     * @param $contents
     * @throws ObjectNotFoundException
     */
    public function appendFile($path, $contents)
    {
        if (!$this->fileExists($path))
        {
            $this->writeFile($path, $contents);
        }
        else
        {
            $this->writeFile($path, $this->getFile($path) . $contents);
        }
    }

    /**
     * @param $path
     * @param $contents
     * @throws ObjectNotFoundException
     */
    public function prependFile($path, $contents)
    {
        if (!$this->fileExists($path))
        {
            $this->writeFile($path, $contents);
        }
        else
        {
            $this->writeFile($path,  $contents . $this->getFile($path));
        }
    }

    /**
     * @param $path
     * @return bool|string
     * @throws ObjectNotFoundException
     */
    public function getFile($path)
    {
        if ($this->fileExists($path))
        {
            return file_get_contents($path);
        }

        throw new ObjectNotFoundException('File', ['path' => $path]);
    }

    /**
     * @param $pattern
     * @return array
     */
    public function glob($pattern)
    {
        $files = glob($pattern);
        if (!$files)
        {
            $files = [];
        }

        return $files;
    }

    /**
     * @param $path
     * @param $contents
     * @return bool|int
     */
    public function writeFile($path, $contents)
    {
        return file_put_contents($path, $contents);
    }

    /**
     * @param $path
     * @return bool
     */
    public function fileExists($path)
    {
        return file_exists($path);
    }

    /**
     * @param $path
     * @return bool|void
     */
    public function deleteFile($path)
    {
        if ($this->fileExists($path))
        {
            unlink($path);
        }
    }

    public function directoryExists($path)
    {
        return file_exists($path);
    }

    public function lastModifiedTime($path)
    {
        if ($this->fileExists($path))
        {
            return filemtime($path);
        }

        return 0;
    }

    public function writeDirectory($path, $mode = 0777)
    {
        if (!$this->fileExists($path))
        {
            mkdir($path, $mode, true);
        }
    }

    /**
     * @param $path
     * @param array $data
     * @return mixed
     */
    public function require($path, $data = [])
    {
        return $this->requirePath($path, $data);
    }

    /**
     * @param $path
     * @param array $data
     * @return mixed
     */
    public function requireOnce($path, $data = [])
    {
        return $this->requirePath($path, $data, true);
    }

    /**
     * @param $path
     * @param array $data
     * @param bool $once
     * @return mixed
     */
    private function requirePath($path, $data = [], $once = false)
    {
        if (!empty($data))
        {
            extract($data);
        }

        return ($once) ? require_once $path : require $path;
    }

}