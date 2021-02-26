<?php

namespace Packages\bikiran;

class Generate
{
    static function token(int $length = 32): string
    {
        $tokenStr = "AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz0123456789";
        $token = "";

        $codeAlphabet_ar = str_split($tokenStr);
        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet_ar[rand(0, 31)];
        }
        return $token;
    }
}