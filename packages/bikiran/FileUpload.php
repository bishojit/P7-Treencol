<?php

namespace Packages\bikiran;

use Packages\mysql\QueryInsert;

class FileUpload
{
    private int $dirLength = 3;
    private string $defaultDir = "uploads/";
    private string $generatedDir = "";
    private string $id = "";
    private string $fileUploadedPath = "";
    private string $ext = "";

    private array $fileInfo = [
        /*'name' => "",
        'type' => "",
        'size' => 0,
        'tmp_name' => "",
        'error' => 1,*/
    ];

    private int $maxFileSize = 0;
    private int $minFileSize = 0;
    private array $allowedExtension_ar = [];
    private array $allowedFormat_ar = [];

    private int $error = 1;
    private array $errorMessages_ar = [
        0 => "No Error",
        1 => "No Action",
    ];
    private bool $uploadSt = false;

    public function __construct($uploadInfo_ar)
    {
        $this->fileInfo = $uploadInfo_ar;
        $this->ext = $this->getExt();
        $this->id = md5(json_encode([getTime(), ...array_values($uploadInfo_ar)]));
        $this->generatedDir = substr($this->id, 0, $this->dirLength) . "/";
    }

    public function getExt(): string
    {
        $pathInfo = pathinfo($this->fileInfo['name']);
        return $pathInfo['extension'] ?: "";
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

    private function checkError(): int
    {
        if ($this->fileInfo['error'] != 0) {
            $this->error = 2;
            $this->errorMessages_ar[2] = "Error on Upload";
        } else if ($this->minFileSize && $this->fileInfo['size'] < $this->minFileSize) {
            $this->error = 3;
            $this->errorMessages_ar[3] = "Minimum File Size " . $this->minFileSize;
        } else if ($this->maxFileSize && $this->fileInfo['size'] > $this->maxFileSize) {
            $this->error = 4;
            $this->errorMessages_ar[4] = "Maximum File Size " . $this->maxFileSize;
        } else if (count($this->allowedFormat_ar) && !$this->allowedFormat_ar[$this->fileInfo['type']]) {
            $this->error = 5;
            $this->errorMessages_ar[5] = "File format (" . $this->fileInfo['type'] . ") not Allowed";
        } else {
            $this->error = 0;
            $this->errorMessages_ar[0] = "No Error";
        }

        return $this->error;
    }

    public function saveFile(): bool
    {
        global $SystemDefaults;
        $systemDir = $SystemDefaults->getUploadDir();

        if ($this->checkError() == 0) {
            $path[0] = $systemDir;
            $path[1] = $systemDir . $this->defaultDir . $this->generatedDir;
            $path[2] = $systemDir . $this->defaultDir . $this->generatedDir . $this->id;

            //--Creating DIR if not exist
            if (!is_dir($path[1])) {
                mkdir($path[1], 0777, true);
            }

            //--Saving File on DIR
            if (is_dir($path[1])) {
                $this->uploadSt = move_uploaded_file($this->fileInfo['tmp_name'], $path[2]);
            }

            if ($this->uploadSt) {
                $this->fileUploadedPath = $path[2];
            }
        }
        return $this->uploadSt;
    }
    
    public function copyFile(): bool
    {
        global $SystemDefaults;
        $systemDir = $SystemDefaults->getUploadDir();

        if ($this->checkError() == 0) {
            $path[0] = $systemDir;
            $path[1] = $systemDir . $this->defaultDir . $this->generatedDir;
            $path[2] = $systemDir . $this->defaultDir . $this->generatedDir . $this->id;

            //--Creating DIR if not exist
            if (!is_dir($path[1])) {
                mkdir($path[1], 0777, true);
            }

            //--Saving File on DIR
            if (is_dir($path[1])) {
                $this->uploadSt = copy($this->fileInfo['tmp_name'], $path[2]);
            }

            if ($this->uploadSt) {
                $this->fileUploadedPath = $path[2];
            }
        }
        return $this->uploadSt;
    }

    public function saveData(): int
    {
        if ($this->fileUploadedPath) {
            $insert = new QueryInsert("system_files");
            $insert->addRow([
                'file_id' => $this->id,
                'ext' => $this->ext,
                'type' => $this->fileInfo['type'],
                'name' => $this->fileInfo['name'],
                'location' => $this->fileUploadedPath,
            ]);
            $insert->push();
            return $insert->getLastInsertedId();
        }

        return 0;
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
        return $this->fileInfo['name'];
    }

    public function getFileSize(): int
    {
        return $this->fileInfo['size'];
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
        return $this->fileInfo['type'];
    }

    public function getFileId(): string
    {
        return $this->id;
    }

    public static function getFilePathById($id): string
    {
        $FileUpload = new FileUpload([]);
        global $SystemDefaults;
        $systemDir = $SystemDefaults->getUploadDir();
        $generatedDir = substr($id, 0, $FileUpload->dirLength) . "/";
        return $systemDir . $FileUpload->defaultDir . $generatedDir . $id;
    }

    public static function getPreviewUrl($file): string
    {
        return "https://{$_SERVER['HTTP_HOST']}/uploads/{$file}";
    }
}