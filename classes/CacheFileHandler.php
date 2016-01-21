<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 1/12/16
 * Time: 10:03 AM
 */

namespace ebussola\statefull\classes;


class CacheFileHandler
{

    const CACHE_DIR_NAME = 'statefull-cache';

    private $cachePath;

    public function __construct()
    {
        $this->cachePath = \App::storagePath() . '/' . self::CACHE_DIR_NAME;
    }

    public function saveCacheFile($pagePath, $content) {
        $file = new \SplFileInfo('/' . trim($pagePath, '/') . '.html');
        @mkdir($this->cachePath . $file->getPath(), 0777, true);

        file_put_contents($this->cachePath . $file->getPathname(), $content);
    }

    public function deleteCacheFile($pagePath, $recursive=false)
    {
        if (file_exists($this->cachePath)) {
            if ($recursive) {
                $this->deltree($this->cachePath . $pagePath);
                @unlink($this->cachePath . $pagePath . '.html');
            }
            else {
                unlink($this->cachePath . $pagePath . '.html');
            }
        }
    }

    public function getCachePath()
    {
        return $this->cachePath;
    }

    private function deltree($dir) {
        $it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);
    }

}