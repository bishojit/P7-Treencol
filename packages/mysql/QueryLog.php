<?php


namespace Packages\mysql;


class QueryLog
{
    private $tokenStr = "AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz0123456789";

    private function token($length)
    {
        $token = '';
        $codeAlphabet_ar = str_split($this->tokenStr);
        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet_ar[rand(0, 31)];
        }
        return $token;
    }

    function saveLogQueryError($sqlString, $errorMessage)
    {
        $sql_ar = [];
        $creator = getUserSl();
        $ip_long = getIpLong();
        $debug_all_ar = debug_backtrace();

        //--Generate Unique Key
        $unique_key = $this->token(32);

        if ($debug_all_ar) {
            foreach ($debug_all_ar as $sl => $det_ar) {
                $in_ar['group_token'] = quote((string)$unique_key);
                $in_ar['index_sl'] = quote((string)$sl);
                $in_ar['php_self'] = quote((string)$det_ar['file']);
                $in_ar['line_no'] = quote((string)$det_ar['line']);
                $in_ar['class'] = quote((string)$det_ar['class']);
                $in_ar['function'] = quote((string)$det_ar['function']);
                $in_ar['sql_string'] = quote((string)$sqlString);
                $in_ar['error_message'] = quote((string)$errorMessage);
                $in_ar['creator'] = quote((string)$creator);
                $in_ar['ip_long'] = quote((string)$ip_long);
                $in_ar['time_created'] = quote((string)getTime());
                $in_ar['time_updated'] = quote((string)getTime());

                $sql_ar[] = "(" . implode(", ", $in_ar) . ")";
            }

            if ($sql_ar) {
                runSql("
                INSERT " . " INTO `log_query_error`
                (`group_token`, `index_sl`, `php_self`, `line_no`, `class`, `function`, `sql_string`, `error_message`, `creator`, `ip_long`, `time_created`, `time_updated`)
                 VALUES " . implode(",", $sql_ar)
                );
            }
        }
    }
}