<?php

namespace Core;

class Configs
{
    private string $fileName = "";
    private string $dirName = "";
    private string $path = "";
    private array $data = [];

    function __construct($configName)
    {
        $this->fileName = $configName . ".json";
        $this->dirName = "configs/access/" . getDefaultDomain() . "/";
        $this->path = $this->dirName . $this->fileName;

        if (!is_dir($this->dirName)) {
            mkdir($this->dirName, 0777, true);
        }

        if (is_file($this->path)) {
            $this->data = json_decode(file_get_contents($this->path), true);
        } else {
            $this->data = [];
        }
    }

    static function setData($configName, $key, $value): array
    {
        $Configs = new Configs($configName);
        $Configs->data[$key] = $value;
        $return = file_put_contents($Configs->path, json_encode($Configs->data, JSON_FORCE_OBJECT));

        return [
            'error' => $return,
            'message' => "Done",
        ];
    }

    static function remove($configName, $key): array
    {
        $Configs = new Configs($configName);
        unset($Configs->data[$key]);
        $return = file_put_contents($Configs->path, json_encode($Configs->data, JSON_FORCE_OBJECT));

        return [
            'error' => $return,
            'message' => "Done",
        ];
    }

    static function getData($configName): array
    {
        $Configs = new Configs($configName);
        return $Configs->data;
    }

    static function getPrice($price, $duration, $currency): int
    {
        $Configs = new Configs('json_configs/currency');

        $price = $price != 0 ? ($price / $duration) * 12 : 0;
        $price2 = $price * $Configs->data['currencies'][$currency];
        return $price2;
    }
    static function getPriceTwoYrsDiscount($price): array
    {
        $price=$price*2;
        $discountPrice=($price*21)/100;
        $discountAmount=$price-$discountPrice;

        return [
            'discountPrice' => $discountPrice,
            'discountAmount' => $discountAmount,
        ];
    }
    static function getPriceFiveYrsDiscount($price): array
    {
        $price=$price*5;
        $discountPrice=($price*30)/100;
        $discountAmount=$price-$discountPrice;

        return [
            'discountPrice' => $discountPrice,
            'discountAmount' => $discountAmount,
        ];
    }
}