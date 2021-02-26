<?php


namespace Core;


class SystemDefaults
{
    private $multiUser = 0;
    private $uploadDir = "";

    public function __construct(AppInit $AppInit)
    {
        $systemDefaultsObj = xmlFileToObject("configs/system-defaults.xml", "System Defaults File Not Found.");

        $this->multiUser = (bool)$systemDefaultsObj->multi_users;
        $this->uploadDir = (string)$systemDefaultsObj->upload_dir . $AppInit->getDefaultDomain() . "/";
    }

    public function getMultiUser(): bool
    {
        return $this->multiUser;
    }

    public function getUploadDir(): string
    {
        return $this->uploadDir;
    }
}