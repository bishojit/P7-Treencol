<?php

namespace Packages\bikiran;

class Generate
{
    static function token(int $length = 32, $tokenStr = "AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz0123456789"): string
    {
        $token = "";
        $strLen = strlen($tokenStr);

        $codeAlphabet_ar = str_split($tokenStr);
        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet_ar[rand(0, $strLen-1)];
        }
        return $token;
    }
}