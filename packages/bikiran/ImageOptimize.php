<?php


namespace Packages\bikiran;


class ImageOptimize
{
    public static function optimizee(String $url, $width = "", $height = "")
    {
        if ($width > 0 && $height > 0) {
            return "https://www.optimizee.xyz/images/" . str_replace("/", "_", base64_encode($url)) . "," . $width . "," . $height . ".jpg";
        }

        return "https://www.optimizee.xyz/images/" . str_replace("/", "_", base64_encode($url)) . ".jpg";
    }
}