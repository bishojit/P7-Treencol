<?php


namespace Packages\bikiran;


class FileUpload
{
    private $fileName = "";
    private $formattedFileName = "";
    private $fileType = "";
    private $fileSize = 0;
    private $fileTempPath = "";
    private $fileUploadedPath = "";
    private $fileError = 0;

    private $maxFileSize = 0;
    private $minFileSize = 0;

    private $allowedExtension_ar = [];
    private $allowedFormat_ar = [];

    private $error = 1;
    private $errorMessages_ar = [
        0 => "No Error",
        1 => "No Action",
    ];
    private $uploadSt = false;

    public function __construct($uploadedFileInfo_ar)
    {
        $this->fileName = $uploadedFileInfo_ar['name'];
        $this->fileType = $uploadedFileInfo_ar['type'];
        $this->fileSize = $uploadedFileInfo_ar['size'];
        $this->fileTempPath = $uploadedFileInfo_ar['tmp_name'];
        $this->fileError = $uploadedFileInfo_ar['error'];

        $this->formatName();
    }

    public function formatName(): void
    {
        $this->formattedFileName = str_replace(" ", "-", ConvertString::cleanStrUtf8($this->fileName, '\da-zA-Z\x00-\x1F\x7F-\xFF\ \.'));
    }

    public function setMinSize(int $minFileSize = 0): void // 0=no limit
    {
        $this->minFileSize = $minFileSize;
    }

    public function setMaxSize(int $maxFileSize = 0): void // 0=no limit
    {
        $this->maxFileSize = $maxFileSize;
    }

    public function addAllowedExtension(string $allowExt): void
    {
        $this->allowedExtension_ar[$allowExt] = $allowExt;
    }

    public function setAllowedExtension(array $allowExt_ar): void
    {
        foreach ($allowExt_ar as $allowExt) {
            $this->allowedExtension_ar[$allowExt] = $allowExt;
        }
    }

    public function addFileFormat(string $allowFormat): void
    {
        $this->allowedFormat_ar[$allowFormat] = $allowFormat;
    }

    public function setFileFormat(array $allowFormat_ar): void
    {
        foreach ($allowFormat_ar as $allowFormat) {
            $this->allowedFormat_ar[$allowFormat] = $allowFormat;
        }
    }

    //todo: required extension validation

    private function checkError()
    {
        if ($this->fileError != 0) {
            $this->error = 2;
            $this->errorMessages_ar[2] = "Error on Upload";
        } else if ($this->minFileSize && $this->fileSize < $this->minFileSize) {
            $this->error = 3;
            $this->errorMessages_ar[3] = "Minimum File Size " . $this->minFileSize;
        } else if ($this->maxFileSize && $this->fileSize > $this->maxFileSize) {
            $this->error = 4;
            $this->errorMessages_ar[4] = "Maximum File Size " . $this->maxFileSize;
        } else if (count($this->allowedFormat_ar) && !$this->allowedFormat_ar[$this->fileType]) {
            $this->error = 5;
            $this->errorMessages_ar[5] = "File format (" . $this->fileType . ") not Allowed";
        } else {
            $this->error = 0;
            $this->errorMessages_ar[0] = "No Error";
        }

        return $this->error;
    }

    public function saveFile(string $uploadDir = "temp/"): bool
    {
        global $SystemDefaults;
        $systemDir = $SystemDefaults->getUploadDir();

        if ($this->checkError() == 0) {
            $path[0] = $systemDir;
            $path[1] = $systemDir . $uploadDir . date("Ym", getTime()) . "/";
            $path[2] = $systemDir . $uploadDir . date("Ym", getTime()) . "/" . getTime() . "_" . $this->formattedFileName;


            //--Creating DIR if not exist
            if (!is_dir($path[1])) {
                mkdir($path[1], 0777, true);
            }

            //--Saving File on DIR
            if (is_dir($path[1])) {
                $this->uploadSt = move_uploaded_file($this->fileTempPath, $path[2]);
            }

            if ($this->uploadSt) {
                $this->fileUploadedPath = $path[2];
            }
        }
        return $this->uploadSt;
    }

    public function getUploadedPath(): string
    {
        return $this->fileUploadedPath;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function getMessage(): string
    {
        return $this->errorMessages_ar[$this->getError()];
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getFileSize(): int
    {
        return $this->fileSize;
    }

    public function remove(): bool
    {
        if (is_file($this->fileUploadedPath)) {
            return unlink($this->fileUploadedPath);
        }
        return false;
    }

    public function getFileType(): string
    {
        return $this->fileType;
    }
}