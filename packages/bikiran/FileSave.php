<?php

namespace Packages\bikiran;

class FileSave
{
    private $error = 1;
    private $message = "";
    private $newPath = "";
    private $oldPath = "";
    private $newUrl = "";

    function __construct(string $path, string $dir)
    {
        //--
        $this->getImageOldPath($path);
        $this->getImageNewPath($path, $dir);

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

    private function getImageOldPath(string $url)
    {
        $oldPath = "";
        if (substr($url, 0, 14) == "/cloud-uploads") {
            $path = substr($url, 1);
            if (is_file($path)) {
                $oldPath = $path;
            }
        } else if (substr($url, 0, 3) == "://" || substr($url, 0, 8) == "https://" || substr($url, 0, 7) == "http://") {
            // https://www.outside-image.com/files/201608/1470143491_03.jpg Outside Image
            $oldPath = $url;
        }
        $this->oldPath = $oldPath;
    }

    private function getImageNewPath(string $url, string $dir) // $dir="folder/";
    {
        $newPath = "";
        if (substr($url, 0, 14) == "/cloud-uploads") {

            $newPath = substr(str_replace("/temp/", "/" . $dir, $url), 1);
        } else if (substr($url, 0, 3) == "://" || substr($url, 0, 8) == "https://" || substr($url, 0, 7) == "http://") { // https://www.dailyjanakantha.com/files/201608/1470143491_03.jpg
            $fileName_ar = explode("_", end(explode("/", $url)));

            $timeStamp = (int)array_shift($fileName_ar);
            $newPath = $this->filePath($dir, $timeStamp, implode("_", $fileName_ar));
        }

        $this->newPath = $newPath;
        $this->newUrl = $newPath ? "/" . $newPath : "";
    }

    public static function filePath(string $dir, int $timeStamp, string $fileName): string
    {
        global $SystemDefaults;
        $systemDir = $SystemDefaults->getUploadDir();
        $filePath = "";

        if ($timeStamp == 0) {
            $timeStamp = getTime();
        }

        $formattedFileName = str_replace(" ", "-", ConvertString::cleanStrUtf8($fileName, '\da-z\x00-\x1F\x7F-\xFF\ \.'));
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

    public static function moveToTrash($filePath)
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

    public function getNewUrl(): string
    {
        return $this->newUrl;
    }
}