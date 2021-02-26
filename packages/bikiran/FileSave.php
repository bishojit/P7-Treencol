<?php

namespace Packages\bikiran;

class FileSave
{
    private int $error = 1;
    private string $message = "";
    private string $newPath = "";
    private string $oldPath = "";
    private string $oldUrl = "";
    private string $newUrl = "";

    function __construct(string $url, string $dir)
    {
        //--
        $this->getFileOldPath($url);
        $this->getFileNewPath($url, $dir);

        $newDirName = pathinfo($this->newPath, PATHINFO_DIRNAME);
        $dirCreationSt = true;
        if (!is_dir($newDirName) && $newDirName) {
            $dirCreationSt = mkdir($newDirName, 0777, true);
        }

        if (!$this->oldPath) {
            $this->error = 2;
            $this->message = "Invalid Old Path";
        } else if (!$this->newPath) {
            $this->error = 4;
            $this->message = "Invalid New Path";
        } else if (!$dirCreationSt) {
            $this->error = 3;
            $this->message = "Unable to Create New Path Directory";
        } else if ($this->oldPath == $this->newPath) {
            $this->error = 0;
            $this->message = "Same File Path";
        } else {
            $this->error = 0;
            $this->message = "Success";

            rename($this->oldPath, $this->newPath);
        }
    }

    private function getFileOldPath(string $url)
    {
        $this->oldUrl = $url;
        if (substr($url, 0, 14) == "/cloud-uploads") {
            $path = substr($url, 1);

            if (is_file($path)) {
                $this->oldPath = $path;
            }
        }
    }

    private function getFileNewPath(string $url, string $dir) // $dir="folder/";
    {
        if (substr($url, 0, 14) == "/cloud-uploads") {

            $this->newPath = substr(str_replace("/temp/", "/" . $dir, $url), 1);
            $this->newUrl = $this->newPath ? "/" . $this->newPath : "";
        }
    }

    public static function filePath(string $dir, int $timeStamp, string $fileName): string
    {
        global $SystemDefaults;
        $systemDir = $SystemDefaults->getUploadDir();
        $filePath = "";

        if ($timeStamp == 0) {
            $timeStamp = getTime();
        }

        $formattedFileName = str_replace(" ", "-", ConvertString::cleanStrUtf8($fileName, '\da-zA-Z0-9\x00-\x1F\x7F-\xFF\ \.'));
        $path[0] = $systemDir;
        $path[1] = $systemDir . $dir . date("Ym", $timeStamp) . "/";
        $path[2] = $systemDir . $dir . date("Ym", $timeStamp) . "/" . $timeStamp . "_" . $formattedFileName;


        //--Creating DIR if not exist
        if (!is_dir($path[1])) {
            mkdir($path[1], 0777, true);
        }

        //--Saving File on DIR
        if (is_dir($path[1])) {
            $filePath = $path[2];
        }

        return $filePath;
    }

    public static function moveToTrash($filePath): bool
    {
        $startingChar = substr($filePath, 0, 1);
        if ($startingChar == "/") {
            $filePath = substr($filePath, 1);
        }

        // /cloud-uploads/portal.cljschool.com/students/202003/1584948876_s1699123.png
        $newPath = str_replace("cloud-uploads/" . getDefaultDomain(), "cloud-uploads/" . getDefaultDomain() . "/trash", $filePath);
        $newDirName = pathinfo($newPath, PATHINFO_DIRNAME);
        if (!is_dir($newDirName)) {
            mkdir($newDirName, 0777, true);
        }
        return is_file($filePath) && $newPath ? rename($filePath, $newPath) : false;
    }

    public function getOldUrl(): string
    {
        return $this->oldUrl;
    }

    public function getNewUrl(): string
    {
        return $this->newUrl;
    }

    public function getOldPath(): string
    {
        return $this->oldPath;
    }

    public function getNewPath(): string
    {
        return $this->newPath;
    }
}