<?php


namespace Packages\bikiran;


class StringEncodingDecoding
{
    static string $string = "AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz0123456789-_";

    static function encoding(string $in): string
    {
        $out = '';
        $bit_ar = [];
        $enc_code = str_split(StringEncodingDecoding::$string);
        $in_ar = str_split($in);
        foreach ($in_ar as $chr) {
            $bit_ar[] = str_pad(decbin(ord($chr)), 8, "0", STR_PAD_LEFT);
        }
        $bit = implode("", $bit_ar);

        $bit6_ar = str_split($bit, 6);
        $bit6_ar[sizeof($bit6_ar) - 1] = str_pad($bit6_ar[sizeof($bit6_ar) - 1], 6, "0", STR_PAD_RIGHT);
        foreach ($bit6_ar as $bit) {
            $out .= $enc_code[bindec($bit)];
        }
        return $out;
    }

    static function decoding(string $in): string
    {
        $out = '';
        $bit_ar = [];
        $enc_code = str_split(StringEncodingDecoding::$string);
        $in_ar = str_split($in);
        foreach ($in_ar as $chr) {
            $bit_ar[] = str_pad(decbin(array_search($chr, $enc_code)), 6, "0", STR_PAD_LEFT);
        }
        $bit = implode("", $bit_ar);

        $bit8_ar = str_split($bit, 8);

        foreach ($bit8_ar as $bit) {
            if (($chr = bindec($bit)) > 31 && $chr < 127)
                $out .= chr($chr);
        }

        return $out;
    }
}