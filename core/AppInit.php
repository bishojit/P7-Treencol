<?php


namespace Core;


class AppInit
{
    private $defaultDomain = "default";
    private $ipLong = 0;
    private $uriOnly = "";
    private $uriMethod = "get";
    private $queryString = "";
    private $userIndex = 0;
    private $domainAr = [];

    function __construct()
    {
        //  1. Detect IP
        $this->ipLong = ip2long($_SERVER['REMOTE_ADDR']);

        //  2. Detect Site
        $detected_domain = $_SERVER['HTTP_HOST'];
        $detected_domain_alias = "";
        $detected_domain_ar = explode(".", $detected_domain);

        //--Domain Mapping
        $domainMappingObj = xmlFileToObject("configs/domain-mapping.xml", null);
        foreach ($domainMappingObj->domain ?: [] as $domainObj) {
            $alias = (string)$domainObj->attributes()->alias;
            $target = (string)$domainObj->attributes()->target;

            if ($detected_domain == $alias) {
                $detected_domain_alias = $target;
            }
        }

        //--Detect Domain
        if ($detected_domain && is_dir("configs/access/" . $detected_domain)) {
            $this->defaultDomain = $detected_domain;
        } else if ($detected_domain_ar[0] == "test" || $detected_domain_ar[0] == "demo") {
            $this->defaultDomain = "demo";
        } else if ($detected_domain_alias && is_dir("configs/access/" . $detected_domain_alias)) {
            $this->defaultDomain = $detected_domain_alias;
        } else {
            $this->defaultDomain = "default";
        }

        //  3. Detect URI & User Index
        $this->uriOnly = trim(explode("?", $_SERVER['REQUEST_URI'])[0], "/");
        if (substr($this->uriOnly, 0, 2) == "u-") {
            $uriOnly_ar = explode("/", $this->uriOnly);
            $this->userIndex = (int)substr($uriOnly_ar[0], 2);
            unset($uriOnly_ar[0]);
            $this->uriOnly = implode("/", $uriOnly_ar);
        }

        //  4. Requested Method
        $this->uriMethod = strtolower($_SERVER['REQUEST_METHOD']);

        //  4. Query String
        $this->queryString = $_SERVER['QUERY_STRING'];

        //--Session Control
        if (is_file("configs/access/" . $this->defaultDomain . "/" . $this->defaultDomain . ".session.php")) {
            require_once "configs/access/" . $this->defaultDomain . "/" . $this->defaultDomain . ".session.php";
        } else {
            ErrorPages::AppInit(1, "Session file not found.");
        }
    }

    public function getDomainAr(): array
    {
        return $this->domainAr;
    }

    public function getDefaultDomain(): string
    {
        return $this->defaultDomain;
    }

    public function getIpLong(): int
    {
        return $this->ipLong;
    }

    public function getUriOnly(): string
    {
        return $this->uriOnly;
    }

    public function getUriMethod(): string
    {
        return $this->uriMethod;
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }

    public function getUserIndex(): int
    {
        return $this->userIndex;
    }
}