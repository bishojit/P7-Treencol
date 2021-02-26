<?php


namespace Packages\tools\defaults\controllers;


use Packages\bikiran\Generate;

class TokenUrl
{
    function fvtGenerator()
    {
        $tokenName = Generate::token(8);
        $tokenCode = Generate::token(32);

        $_SESSION['fvt'][$tokenName] = $tokenCode; //FVT=Form Validation Token
        return json_encode([
            'code' => $tokenName . $tokenCode,
        ], JSON_FORCE_OBJECT);
    }
}