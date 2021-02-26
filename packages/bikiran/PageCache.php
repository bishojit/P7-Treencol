<?php


namespace Packages\bikiran;


class PageCache
{
    private array $headersAr = [];

    function __construct(int $timeCreated, int $duration)
    {
        $this->headersAr["Content-Type"] = "text/html; charset=UTF-8";
        $this->headersAr["Expires"] = date("D, d M Y H:i:s P", $timeCreated + $duration);
        $this->headersAr["Cache-Control"] = "max-age=" . (24 * 3600) . ", public";
        $this->headersAr["Pragma"] = "cache";
        $this->headersAr["Last-Modified"] = date("D, d M Y H:i:s P", $timeCreated);
        $this->headersAr["Etag"] = md5(getTime() . rand(10000000, 99999999));
    }

    function setCacheControl($maxAge, $type = "public")
    {
        $this->headersAr["Cache-Control"] = "max-age=$maxAge, $type";
        return $this;
    }

    function setContentType(String $mimiType)
    {
        $this->headersAr["Content-Type"] = $mimiType;
        return $this;
    }

    function setEtag(String $md5)
    {
        $this->headersAr["Etag"] = $md5;
        return $this;
    }

    function done()
    {
        foreach ($this->headersAr as $key => $val) {
            header($key . ': ' . $val);
        }
    }

    public static function start(int $timeCreated, int $duration)
    {
        return new PageCache($timeCreated, $duration);
    }
}