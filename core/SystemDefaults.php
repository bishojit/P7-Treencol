<?php


namespace Core;


class SystemDefaults
{
    private bool $multiUser = true;
    private string $uploadDir = "cloud-uploads/";
    private bool $sessionAccess = false;
    private bool $tokenAccess = false;
    private string $tokenName = "Secure-Access";

    public function __construct(AppInit $AppInit)
    {
        $systemDefaultsObj = xmlFileToObject("configs/system-defaults.xml", "System Defaults File Not Found.");

        $multiUser = $systemDefaultsObj->MultyUsers->attributes()->value;
        if ($multiUser) {
            $this->multiUser = $multiUser == "true";
        }

        $uploadDir = (string)$systemDefaultsObj->FileUploadDirectory->attributes()->value;
        if (!$uploadDir) {
            $uploadDir = $this->uploadDir;
        }
        $this->uploadDir = $uploadDir . $AppInit->getDefaultDomain() . "/";

        $sessionAccess = $systemDefaultsObj->SessionAccess->attributes()->value;
        if ($sessionAccess) {
            $this->sessionAccess = $sessionAccess == "true";
        }

        $tokenAccess = $systemDefaultsObj->TokenAccess->attributes()->value;
        if ($tokenAccess) {
            $this->tokenAccess = $tokenAccess == "true";
        }

        $tokenName = (string)$systemDefaultsObj->TokenAccess->attributes()->name;
        if ($tokenName) {
            $this->tokenName = $tokenName;
        }
    }

    public function getMultiUser(): bool
    {
        return $this->multiUser;
    }

    public function getUploadDir(): string
    {
        return $this->uploadDir;
    }

    public function isSessionAccess(): bool
    {
        return $this->sessionAccess;
    }

    public function isTokenAccess(): bool
    {
        return $this->tokenAccess;
    }

    public function getTokenName(): string
    {
        return $this->tokenName;
    }
}