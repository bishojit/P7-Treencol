<?php


namespace Packages\bikiran;


class Curl
{
    static function getContent($url, string $uriMethod = "POST", $post_ar = []): string // Get Method List from POSTMAN
    {

        //--Sending to PC Detecting Server
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $uriMethod);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_ar));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //--Collecting from PC Detecting Server
        $out = curl_exec($ch);
        curl_close($ch);

        return $out;
    }
}