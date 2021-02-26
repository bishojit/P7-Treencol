<?php


namespace Core;

use Packages\bikiran\Generate;
use Packages\mysql\QueryInsert;
use Packages\mysql\QuerySelect;
use Packages\mysql\QueryUpdate;

class Users
{
    private $error = 1;
    private $message = "Not Pulled";
    private $tfa_all_ar = [];
    private $userSl = 0;

    private function setLoginSession(array $userInfo_ar, string $loginBy = "default_email"): void
    {
        $sl = $userInfo_ar['sl'];
        $insertLog = new QueryInsert('log_login');
        $insertLog->addRow([
            'user_sl' => $userInfo_ar['sl'],
            'login_by' => $loginBy,
            'unique_key' => "",
        ]);
        $insertLog->push();

        // do not change Below lines
        $_SESSION['user_sl_ar'][$sl] = $sl;
        $_SESSION['login_sl_ar'][$sl] = $insertLog->getLastInsertedId();

        $this->error = 0;
        $this->message = "Login Success";
    }

    function loginProcess($userInfo_ar, $password, $loginBy, $tfaType, $tfaCode, $tfaRemember = true): Users
    {
        $this->userSl = $userInfo_ar['sl'];

        if (!$userInfo_ar) {
            $this->error = 2;
            $this->message = "Username or password not match";
        } else if ($userInfo_ar['status'] != "active") {
            $this->error = 3;
            $this->message = "Username or password not match";
        } else if ($userInfo_ar['login_password'] != md5($password)) {
            $this->error = 4;
            $this->message = "Username or password not match";
        } else {

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

        return $this;
    }

    function logOut(array $userInfo_ar = []): Users
    {
        //--Select
        $select = new QuerySelect("log_login");
        $select->setQueryString("
        SELECT * 
        FROM `log_login` 
        WHERE " . quoteForIn('sl', $_SESSION['login_sl_ar'] ?: [])."
        ");
        $select->pull();
        $login_all_ar = $select->getRows('user_sl');

        $countRow = 0;
        $update = new QueryUpdate('log_login');
        $update->setAuthorized();
        if (empty($userInfo_ar)) {

            foreach ($_SESSION['user_sl_ar']?:[] as $userSl) {
                unset($_SESSION['user_sl_ar'][$userSl]);
                unset($_SESSION['login_sl_ar'][$userSl]);

                if ($login_all_ar[$userSl]) {
                    $update->updateRow($login_all_ar[$userSl], [
                        'time_logout' => getTime()
                    ]);
                    $countRow++;
                }
            }
        } else {
            $userSl = $userInfo_ar['sl'];
            unset($_SESSION['user_sl_ar'][$userSl]);
            unset($_SESSION['login_sl_ar'][$userSl]);

            if ($login_all_ar[$userSl]) {
                $update->updateRow($login_all_ar[$userSl], [
                    'time_logout' => getTime()
                ]);
                $countRow++;
            }
        }

        if ($countRow) {
            $update->push();
        }

        return $this;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getUserSl(): int
    {
        return $this->userSl;
    }

    public function getUserIndex(int $userSl = null): int
    {
        if ($userSl == null) {
            $userSl = $this->userSl;
        }

        foreach (array_values($_SESSION['user_sl_ar'] ?: []) as $index => $sl) {
            if ($sl == $userSl) {
                return $index;
            }
        }
        return -1;
    }
}