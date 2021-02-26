<?php

namespace Core;

use Packages\bikiran\Generate;
use Packages\mysql\QuerySelect;
use Packages\mysql\QueryUpdate;

class UsersTwoFA
{
    function abc()
    {

        //--Collect 2FA Data
        $select = new QuerySelect("system_users_tfa");
        $select->setQueryString("
            SELECT `sl`,
                   `user_sl`,
                   `type` 
            FROM `system_users_tfa` 
            WHERE `user_sl`=" . quote($userInfo_ar['sl']) . " 
                AND `status`='enable'
            ");
        $select->pull();
        $this->tfa_all_ar = $select->getRows('type');

        if (empty($this->tfa_all_ar)) { // If 2FA is Disabled

            $this->setLoginSession($userInfo_ar, $loginBy);
        } else if (md5($_COOKIE['tfa_key_' . $userInfo_ar['sl']] . ":" . $_COOKIE['tfa_time_' . $userInfo_ar['sl']]) == $userInfo_ar['skip_tfa_code']) { // If 2FA is Able to Skipped

            $this->setLoginSession($userInfo_ar);
        } else { // If 2FA is Enabled

            if ($tfaType && !$tfaCode) {

                $this->error = 5;
                $this->message = "Please Enter Verification Code";
            } else if ($tfaType && $_SESSION['tfa_code'] != $tfaCode) {

                $this->error = 6;
                $this->message = "Invalid Verification Code";
            } else if ($tfaType && $tfaCode) {

                $token = (new Generate)->token(10);

                if ($tfaRemember == true) {
                    setcookie('tfa_key_' . $userInfo_ar['sl'], $key = md5($token), getTime() + 30 * 24 * 3600);
                    setcookie('tfa_time_' . $userInfo_ar['sl'], $keyTime = getTime(), getTime() + 30 * 24 * 3600);

                    //--Insert
                    $update = new QueryUpdate("system_users");
                    $update->updateRow($userInfo_ar['sl'], [
                        'skip_tfa_code' => md5($key . ":" . $keyTime),
                    ]);
                    $update->push();
                }

                $this->setLoginSession($userInfo_ar);
            } else {
                $_SESSION['system_pre_logged_in_sl'] = $userInfo_ar['sl'];

                $this->error = 7;
                $this->message = "Please verify 2FA Process";
            }
        }
    }


    /*
     *
     *
     * */
}