<?php


namespace Packages\bikiran;


class Domain
{
    private $url = "";

    function __construct(string $url)
    {
        if (substr($url, 0, 7) == "http://")
            $this->url = $url;
        elseif (substr($url, 0, 8) == "https://")
            $this->url = $url;
        elseif (substr($url, 0, 2) == "//")
            $this->url = "http:" . $url;
        else
            $this->url = "http://" . $url;
    }

    function getScheme(): string
    {
        return parse_url($this->url, PHP_URL_SCHEME);
    }

    function getUserName(): string
    {
        return parse_url($this->url, PHP_URL_USER);
    }

    function getPassword(): string
    {
        return parse_url($this->url, PHP_URL_PASS);
    }

    function getHost(): string
    {
        return parse_url($this->url, PHP_URL_HOST);
    }

    function getPort(): string
    {
        return parse_url($this->url, PHP_URL_PORT);
    }

    function getPath(): string
    {
        return parse_url($this->url, PHP_URL_PATH);
    }

    function getQuery(): string
    {
        return parse_url($this->url, PHP_URL_QUERY);
    }

    function getFragment(): string
    {
        return parse_url($this->url, PHP_URL_FRAGMENT);
    }
}