<?php

namespace Core;

use Packages\bikiran\Generate;
use Packages\mysql\QueryInsert;
use Packages\mysql\QuerySelect;
use Packages\mysql\QueryUpdate;

class Users
{
    private int $stayTime = 30 * 24 * 3600;
    private int $error = 1;
    private string $message = "Not Pulled";
    private int $userSl = 0;
    private array $userInfoAr = [];
    private string $password;
    private int $logSl = 0;
    private string $loginBy = "";
    private bool $isValidationSuccess = false;
    private bool $isSessionCreated = false;

    private string $uniqueKey = "";
    private string $tempKey = "";

    function __construct($loginBy, $userInfoAr, $password)
    {
        $this->loginBy = $loginBy;
        $this->userInfoAr = $userInfoAr;
        $this->password = md5($password);
        $this->userSl = $this->userInfoAr['sl'] ?: 0;
    }

    private function infoValidation(): self
    {
        if (!$this->userSl) {
            $this->error = 2;
            $this->message = "Username or password not match";
            $this->isValidationSuccess = false;
            return $this;
        }

        if ($this->userInfoAr['status'] != "active") {
            $this->error = 3;
            $this->message = "Username or password not match";
            $this->isValidationSuccess = false;
            return $this;
        }

        if ($this->userInfoAr['login_password'] != $this->password) {
            $this->error = 4;
            $this->message = "Username or password not match";
            $this->isValidationSuccess = false;
            return $this;
        }

        //$this->error = 0;
        $this->message = "Info Validation Success";
        $this->isValidationSuccess = true;
        return $this;
    }

    private function setLoginSession(): self
    {
        if ($this->isValidationSuccess == true) {
            // do not change Below lines
            $_SESSION['user_sl_ar'][$this->userSl] = $this->userSl;

            $this->error = 0;
            $this->message = "Login Success";
            if ($_SESSION['user_sl_ar'][$this->userSl] == $this->userSl) {
                $this->isSessionCreated = true;
            }
        }
        return $this;
    }

    private function createLoginLog(): self
    {
        if ($this->isSessionCreated == true) {
            $this->uniqueKey = Generate::token(32);
            $this->tempKey = Generate::token(32);

            $insertLog = new QueryInsert('system_users_login');
            $insertLog->addRow([
                'user_sl' => $this->userSl,
                'login_by' => $this->loginBy,
                'unique_key' => $this->uniqueKey,
                'temp_key' => $this->tempKey,
                'time_expired' => getTime() + $this->stayTime,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                //'time_logout' => "",
                //'company_sl' => "",
                'checksum_browser' => self::generateBrowserChecksum(),
                //'checksum_header' => "",
            ]);
            $insertLog->push();
            $logSl = $insertLog->getLastInsertedId();

            $_SESSION['login_sl_ar'][$this->userSl] = $logSl;
            $this->logSl = $logSl;
        }
        return $this;
    }

    function logInProcess(): self
    {
        $this->infoValidation();
        $this->setLoginSession();
        $this->createLoginLog();

        return $this;
    }

    static function logOutProcess(): array
    {
        //--Select
        $select = new QuerySelect("system_users_login");
        $select->setQueryString("
        SELECT * 
        FROM `system_users_login` 
        WHERE " . quoteForIn('sl', $_SESSION['login_sl_ar'] ?: []) . "
        ");
        $select->pull();
        $login_all_ar = $select->getRows('user_sl');

        $countRow = 0;
        $update = new QueryUpdate('system_users_login');
        $update->setAuthorized();
        foreach ($_SESSION['user_sl_ar'] ?: [] as $userSl) {
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

        return [
            'error' => 0,
            'message' => "Logged out"
        ];
    }

    static function tokenUpdate($logSl, $uniqueKey, $tempKey)
    {
        //--Select
        $select = new QuerySelect("system_users_login");
        $select->setQueryString("
        SELECT * 
        FROM `system_users_login` 
        WHERE `sl`=" . quote($logSl) . "
        ");
        $select->pull();
        $loginInfo_ar = $select->getRow();

        if ($loginInfo_ar['sl']) {

            $update = new QueryUpdate('system_users_login');
            $update->setAuthorized();
            $update->updateRow($loginInfo_ar, [
                'time_logout' => getTime()
            ]);
        }
    }

    static function generateBrowserChecksum()
    {
        return md5($_SERVER['HTTP_USER_AGENT']);
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

    public function getLogInfo(): array
    {
        return [
            'log_sl' => $this->logSl,
            'unique_key' => $this->uniqueKey,
            'temp_key' => $this->tempKey,
        ];
    }
}