<?php


namespace Packages\bikiran;


class ScanAllDir
{
    public string $path = "";
    private array $fileAr = [];
    private array $dirAr = [];

    function __construct($path)
    {
        $this->path = $path;
        $this->scanDir($path);
    }

    function scanDir($path)
    {
        $pathAll_ar = scandir($path);

        foreach ($pathAll_ar as $newPath) {
            $realpath = $path . "/" . $newPath;
            if ($newPath != "." && $newPath != "..") {
                if (is_dir($realpath)) {
                    $this->scanDir($realpath);
                } else {
                    $this->fileAr[] = $realpath;
                }
            }
        }

        $this->dirAr[] = $path;
    }

    public function getFileAr(): array
    {
        return $this->fileAr;
    }

    public function getDirAr(): array
    {
        return $this->dirAr;
    }
}