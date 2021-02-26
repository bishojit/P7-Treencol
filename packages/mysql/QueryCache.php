<?php


namespace Packages\mysql;


class QueryCache
{

    private $cachePath = "";
    private $jsonDataAllAr = [];
    private $cacheValidity = 600; // 10 min
    private $time = 0;
    private $timeValidated = 0;
    private $query = "";
    private $checksumQuery = "";

    function __construct(string $filePath, int $cacheValidity, string $query = null)
    {
        $this->cacheValidity = $cacheValidity;
        $this->time = getTime();
        $this->query = $query;

        $this->cachePath = $filePath;
        if (is_file($this->cachePath)) {
            $this->jsonDataAllAr = json_decode(file_get_contents($this->cachePath), true);
            $this->timeValidated = $this->jsonDataAllAr['time_created'] + $this->jsonDataAllAr['seconds_validate'];
            $this->checksumQuery = $this->jsonDataAllAr['checksum_query'];
            if ($cacheValidity == 0) {
                unlink($this->cachePath);
            }
        }
        return $this;
    }

    function getIsDataRequired(): bool
    {
        if ($this->cacheValidity == 0) {
            return true;
        } else if ($this->time >= $this->timeValidated) {
            return true;
        } else if ($this->query !== null && md5($this->query) != $this->checksumQuery) {
            return true;
        }
        return false;
    }

    function setData(array $data): QueryCache
    {
        $this->jsonDataAllAr['time_created'] = $this->time;
        $this->jsonDataAllAr['seconds_validate'] = $this->cacheValidity;
        $this->jsonDataAllAr['checksum_query'] = md5($this->query);
        $this->jsonDataAllAr['data'] = $data;
        if ($this->cacheValidity != 0) {
            file_put_contents($this->cachePath, json_encode($this->jsonDataAllAr));
        }
        return $this;
    }

    function getTime(): int
    {
        return $this->jsonDataAllAr['time_created'];
    }

    function getValiditySeconds(): int
    {
        return $this->jsonDataAllAr['seconds_validate'];
    }

    function getData(): array
    {
        return $this->jsonDataAllAr['data'];
    }
}