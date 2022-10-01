<?php

namespace App\Utils;


class FolderHelper
{
    private static $instance;

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new FolderHelper();
        }

        return self::$instance;
    }

    public function getLocaleFolders($toString = false)
    {
        return $this->getSubFolders(resource_path('lang/*'), $toString);
    }

    public function getSubFilesAndFolders($path, $onlyDir, $toString)
    {
        $globFolder = $onlyDir ? glob($path, GLOB_ONLYDIR) : glob($path);

        if ($toString) {
            $folders = '';
            foreach ($globFolder as $dir) {
                if ($folders) {
                    $folders .= ',' . basename($dir);
                } else {
                    $folders .= basename($dir);
                }
            }

            return $folders;
        } else {
            $folders = [];
            foreach ($globFolder as $dir) {
                $folders[] = basename($dir);
            }

            return $folders;
        }
    }

    public function getFiles($path, $onlyFile, $onlyName, $pattern = '*', $flags = 0)
    {
        $fullPathFiles = $this->expandDirRecursive($path, $pattern, $flags);
    }

    private function getSubFolders($path, $toString)
    {
        return $this->getSubFilesAndFolders($path, true, $toString);
    }

    private function expandDirRecursive($path = '', $pattern = '*', $flags = 0)
    {
        $paths = glob($path . '*', GLOB_MARK | GLOB_ONLYDIR | GLOB_NOSORT);
        $files = glob($path . $pattern, $flags);
        foreach ($paths as $path) {
            $files = array_merge($files, $this->expandDirRecursive($path, $pattern, $flags));
        }
        return $files;
    }

    public function getFilesInPath($path = '', $onlyFile = false, $onlyName = false, $pattern = '*', $flags = 0)
    {
        $paths = glob($path . '*', GLOB_MARK | GLOB_ONLYDIR | GLOB_NOSORT);
        $fullPathFiles = glob($path . $pattern, $flags);

        if ($onlyFile) {
            $fullFiles = [];
        }

        foreach ($paths as $path) {
            $fullPathFiles = array_merge($fullPathFiles, $this->getFilesInPath($path, $pattern, $flags));
        }

        if ($onlyFile) {
            $fileNames = [];
            foreach ($fullPathFiles as $fullPathFile) {
                $fileName = str_replace($path, '', $fullPathFile);
                if ($onlyName) {
                    $fileName = str_replace('.php', '', $fullPathFile);
                }

                $fileNames[] = $fileName;
            }
            $fullFiles = array_merge($fullFiles, $fileNames);

            return $fullFiles;
        } else {
            return $fullPathFiles;
        }
    }
}
