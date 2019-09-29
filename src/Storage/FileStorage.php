<?php

namespace Intersect\Core\Storage;

use SplFileInfo;
use FilesystemIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Intersect\Core\Exception\ObjectNotFoundException;

class FileStorage {

    private static $INSTANCE = null;

    /**
     * @return self
     */
    public static function getInstance()
    {
        if (is_null(self::$INSTANCE))
        {
            self::$INSTANCE = new static();
        }

        return self::$INSTANCE;
    }

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
            return $this->writeFile($path, $contents);
        }
        
        return $this->writeFile($path, $this->getFile($path) . $contents);
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
            return $this->writeFile($path, $contents);
        }
        
        return $this->writeFile($path,  $contents . $this->getFile($path));
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
     * @param $path
     * @param $flags
     * @return SplFileInfo[]
     */
    public function getFiles($path, $flags = FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO)
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, $flags));

        $allFiles = [];
        
        foreach ($iterator as $file)
        {
            $allFiles[] = $file;
        }

        return $allFiles;
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
            return unlink($path);
        }

        return true;
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
            return mkdir($path, $mode, true);
        }

        return true;
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
     * @param $source
     * @param $destination
     * @param $context
     * @return bool
     */
    public function copy($source, $destination, $context = null)
    {
        return copy($source, $destination, $context);
    }

    /**
     * @param $source
     * @param $destination
     * @param $context
     * @return bool
     */
    public function rename($oldName, $newName, $context = null)
    {
        return rename($oldName, $newName, $context);
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