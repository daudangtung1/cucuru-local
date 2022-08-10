<?php

    namespace App\Utils;

    use Illuminate\Support\Arr;
    use Illuminate\Support\Facades\File;

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

        private function getSubFilesAndFolders($path, $onlyDir, $toString)
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

        private function getSubFolders($path, $toString)
        {
            return $this->getSubFilesAndFolders($path, true, $toString);
        }

        public function getAllLocaleFileInFolder($localeFolder = '')
        {
            $files = File::allFiles(resource_path("lang\\$localeFolder"));
            $resourceFilePath = resource_path("lang\\$localeFolder\\");

            $fileData = array_map(function ($file) use ($resourceFilePath, $localeFolder) {
                $filePath = $file->getRealPath();
                $basePath = str_replace($resourceFilePath, '', $filePath);
                $fileKey = $localeFolder . '.' . str_replace(['\\', '.php'], ['.', ''], $basePath);

                return [
                    'fileKey' => $fileKey,
                    'filePath' => $filePath,
                    'basePath' => $basePath,
                    'baseName' => $file->getBasename(),
                ];
            }, $files);

            return $fileData;
        }

        public function getContentLocaleFile($file = [])
        {
            if (empty($file)) return [];

            $fileData = include($file['filePath']);
            if (empty($fileData)) return [];

            return convert_associative_array_to_flatten_array($fileData, $file['fileKey']);
        }
    }
