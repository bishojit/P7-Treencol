<?php


namespace Core;


class ErrorPages
{
    private static array $errorsAr = [
        "AppInit" => 11,
        "Route" => 12,
        "RouteUrl" => 12,
        "DbConnect" => 13,
        "TimeZone" => 14,
        "Auth" => 15,
        "Method" => 16,
        "func" => 17,
    ];

    private static array $layoutAr = [
        "AppInit" => "error_config_html.php",
        "Route" => "error_config_html.php",
        "RouteUrl" => "error_page_html.php", // page not found
        "DbConnect" => "error_config_html.php",
        "TimeZone" => "error_config_html.php",
        "Auth" => "error_page_html.php", // permission error [theme page]
        "Method" => "error_method_html.php", // method error [theme page]
        "func" => "error_config_html.php",
    ];

    private static function codeGenerator($fnCode, $moduleCode): int
    {
        return self::$errorsAr[$fnCode] * 100 + $moduleCode;
    }

    private static function view(string $func, array $val_ar = [])
    {
        $layoutPath = "/app/_defaults/default_pages/" . self::$layoutAr[$func];

        //--Check Method
        global $AppInit;
        $uriMethod = $AppInit->getUriMethod();

        if ($uriMethod == "post") {
            echo json_encode([
                'error' => $val_ar['code'],
                'message' => $val_ar['message'],
                'do' => "",
            ], JSON_FORCE_OBJECT);
        } else { // if ($uriMethod == "get")
            echo view($layoutPath, $val_ar);
        }

        exit();
    }

    #### Functions Call From Various Page
    public static function AppInit($errorCode, $message)
    {
        $code = self::codeGenerator(__FUNCTION__, $errorCode);

        self::view(__FUNCTION__, [
            'code' => $code,
            'message' => $message,
        ]);
    }

    public static function Route($errorCode, $message)
    {
        $code = self::codeGenerator(__FUNCTION__, $errorCode);

        self::view(__FUNCTION__, [
            'code' => $code,
            'message' => $message,
        ]);
    }

    public static function RouteUrl($errorCode, $message)
    {
        $code = self::codeGenerator(__FUNCTION__, $errorCode);

        if ($errorCode == 1) {
            header("HTTP/1.0 404 Not Found (TREEN ERROR: $code)");
        }

        self::view(__FUNCTION__, [
            'code' => $code,
            'message' => $message,
        ]);
    }

    public static function DbConnect($errorCode, $message)
    {
        $code = self::codeGenerator(__FUNCTION__, $errorCode);

        self::view(__FUNCTION__, [
            'code' => $code,
            'message' => $message,
        ]);
    }

    public static function TimeZone($errorCode, $message)
    {
        $code = self::codeGenerator(__FUNCTION__, $errorCode);

        self::view(__FUNCTION__, [
            'code' => $code,
            'message' => $message,
        ]);
    }

    public static function Auth($errorCode, $message, Auth $AuthObj)
    {
        global $Auth;
        $Auth = $AuthObj;
        $code = self::codeGenerator(__FUNCTION__, $errorCode);

        self::view(__FUNCTION__, [
            'code' => $code,
            'message' => $message,
        ]);
    }

    public static function Method($errorCode, $message)
    {
        $code = self::codeGenerator(__FUNCTION__, $errorCode);

        self::view(__FUNCTION__, [
            'code' => $code,
            'message' => $message,
        ]);
    }

    public static function func($errorCode, $message)
    {
        $code = self::codeGenerator(__FUNCTION__, $errorCode);

        self::view(__FUNCTION__, [
            'code' => $code,
            'message' => $message,
        ]);
    }
}