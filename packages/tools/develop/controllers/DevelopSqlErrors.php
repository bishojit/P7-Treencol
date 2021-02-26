<?php

namespace Packages\tools\develop\controllers;

use Packages\bikiran\Validation;
use Packages\mysql\QuerySelect;
use Packages\mysql\QueryUpdate;

class DevelopSqlErrors
{
    function sqlErrors(): string
    {

        //--Collect SQL Errors
        $select = new QuerySelect("log_query_error");
        $select->setQueryString("
        SELECT * 
        FROM `log_query_error` 
        WHERE `time_resolved`=0
        ORDER BY `sql_string`, `time_created` DESC
        ");
        $select->pull();
        $userSl_ar = $select->getColValues('creator');
        $errorGroup_all_ar = $select->getGroupRows('group_token');

        //--Collect User Info
        $select = new QuerySelect("system_users");
        $select->setQueryString("
        SELECT * 
        FROM `system_users` 
        WHERE " . quoteForIn('sl', $userSl_ar) . "
        ");
        $select->pull();
        $user_all_ar = $select->getRows('sl');


        return view("developSqlErrors_html.php", [
            'errorGroup_all_ar' => $errorGroup_all_ar,
            'user_all_ar' => $user_all_ar,
        ]);
    }

    function errorDetails()
    {
        $groupToken = route()->getUriVariablesAr()['groupToken'];

        //--Collect SQL Errors
        $select = new QuerySelect("log_query_error");
        $select->setQueryString("
        SELECT `sl`,
            `sql_string`,
            `error_message`,
            `time_created`,
            `index_sl`,
            `php_self`,
            `line_no`,
            `class`,
            `function`
        FROM `log_query_error` 
        WHERE `group_token`=" . quote($groupToken) . "
        ");
        $select->pull();
        $error_all_ar = $select->getRows();

        return json_encode(array_map(function ($row) {
            $row['time_created'] = date('Y-m-d h:i:s A O', $row['time_created']);

            return $row;
        }, $error_all_ar), JSON_FORCE_OBJECT);
    }

    function makeResolved()
    {
        $groupToken = route()->getUriVariablesAr()['groupToken'];

        //--Collect SQL Errors
        $select = new QuerySelect("log_query_error");
        $select->setQueryString("
        SELECT *
        FROM `log_query_error` 
        WHERE `group_token`=" . quote($groupToken) . "
        ");
        $select->pull();
        $error_all_ar = $select->getRows();


        $validation = new Validation();
        $validation->chkTrue(setPost('num_rows', count($error_all_ar)), "Nothing to Update");
        $validation->validate();

        //--Update Column
        if ($validation->getStatus()) {
            $update = new QueryUpdate("log_query_error");
            $update->setAuthorized();
            foreach ($error_all_ar as $det_ar) {
                $update->updateRow($det_ar, [
                    'time_resolved' => getTime()
                ]);
            }
            $update->push();

            if ($update->getError() == 0) {

                $error = 0;
                $message = "Changed status as Resolved";
            } else {
                $error = 1;
                $message = "Not Updated";
            }

        } else {
            $error = 2;
            $message = $validation->getFirstErrorMessage();
        }

        return json_encode([
            'error' => $error,
            'message' => $message,
        ]);
    }
}