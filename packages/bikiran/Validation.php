<?php

namespace Packages\bikiran;

use Packages\mobile\MobileNumber;

class Validation
{
    private int $counting = 0;
    private array $positionNameAr = [];
    private array $positionValuesAr = [];

    private array $messageAr = [];
    private array $statusAr = [];

    private array $optionPositionAr = [];

    private bool $validatedStatus = false;
    private array $validatedMessagesAr = [];
    private array $validatedStatusAr = [];

    function __construct()
    {
        $configFvt = getSoftInfo()->vlaidation->fvt;

        if ($configFvt) {
            $this->chkFvt();
        }
    }

    private function counting(string $name, string $value): void
    {
        $this->counting++;
        $this->positionNameAr[$this->counting] = $name;
        $this->positionValuesAr[$this->counting] = $value;
    }

    private function statusSaving(string $message, bool $status): void
    {
        $this->messageAr[$this->counting] = $message;
        $this->statusAr [$this->counting] = $status;
    }

    private function chkFvt(): self
    {
        $text = (string)$_POST['validate_fvt'];
        $this->counting('validate_fvt', $text);

        $fvtName = substr($text, 0, 8);
        $fvtCode = substr($text, 8);


        if ($_SESSION['fvt'][$fvtName] != $fvtCode) {

            $this->statusSaving("Invalid token or Session Expired [Reload Please]", false);
        } else {

            $this->statusSaving("OK", true);
        }
        unset($_SESSION['fvt'][$fvtName]);
        return $this;
    }

    function chkString(string $postName, string $title, int $min = null, int $max = null, string $strFormat = null): self
    {
        $text = (string)$_POST[$postName];
        $this->counting($postName, $text);

        if (strlen($text) == 0) {

            $this->statusSaving("Please Enter $title", false);
        } else if ($min !== null && strlen($text) < $min) {

            $this->statusSaving("$title Too Short", false);
        } else if ($max !== null && strlen($text) > $max) {

            $this->statusSaving("$title Too Long", false);
        } else if ($strFormat !== null && !preg_match('/^[' . $strFormat . ']+$/', $text)) {

            $this->statusSaving("Only Accept (" . str_replace("\\", "", $strFormat) . ") as $title", false);
        } else {

            $this->statusSaving("OK", true);
        }
        return $this;
    }

    function chkNumber(string $postName, string $title, float $min = null, float $max = null): self
    {
        $num = $_POST[$postName];
        $this->counting($postName, (string)$num);

        if ($num === null) {

            $this->statusSaving("Please Enter $title", false);
        } else if (!preg_match("/^[0-9\.\-]+$/", $num)) {

            $this->statusSaving("$title Must Be A Number", false);
        } else if ($min !== null && $num < $min) {

            $this->statusSaving("Minimum $title is $min", false);
        } else if ($max !== null && $num > $max) {

            $this->statusSaving("Maximum $title is $max", false);
        } else {

            $this->statusSaving("OK", true);
        }
        return $this;
    }

    function chkSelect(string $postName, string $title): self
    {
        $value = (string)$_POST[$postName];
        $this->counting($postName, (string)$value);

        if (!$value) {

            $this->statusSaving("Please Select $title", false);
        } else {

            $this->statusSaving("OK", true);
        }
        return $this;
    }

    function chkEmailFormat(string $postName, string $title): self
    {
        $email = (string)$_POST[$postName];
        $this->counting($postName, $email);

        if (!$email) {

            $this->statusSaving("Please Enter $title", false);
        } else if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $email)) {

            $this->statusSaving("Invalid Format of $title", false);
        } else {

            $this->statusSaving("OK", true);
        }
        return $this;
    }

    function chkMobileBd(string $postName, string $title): self
    {
        $mobile = (string)$_POST[$postName];
        $this->counting($postName, $mobile);

        if (!$mobile) {

            $this->statusSaving("Please Enter $title", false);
        } else if (strlen($mobile) < 8) {

            $this->statusSaving("$title Too Short", false);
        } else if (strlen($mobile) > 15) {

            $this->statusSaving("$title Too Long", false);
        } else if (!preg_match('/^[0-9\+]+$/', $mobile)) {

            $this->statusSaving("Only Accept 0-9 & + as $title", false);
        } else if (substr($mobile, 0, 1) != "+") {

            $this->statusSaving("$title must start with + & country code", false);
        } else if (substr($mobile, 0, 2) == "+0") {

            $this->statusSaving("$title must start with country code", false);
        } else if (strlen($mobile) < 8) {

            $this->statusSaving("$title must be minimum 8 characters", false);
        } else {

            $this->statusSaving("OK", true);
        }
        return $this;
    }

    function chkMobile(string $postName, string $title, string $countryCode, $maxNumber = 1): self
    {
        $mobile = (string)$_POST[$postName];
        $this->counting($postName, $mobile);

        $MobileNumber = new MobileNumber($countryCode);
        $MobileNumber->addLongString($mobile);
        $MobileNumberTotal = count($MobileNumber->getNumbersRawAr());

        if (!$mobile) {

            $this->statusSaving("Please enter $title", false);
        } else if (strlen($mobile) < 8) {

            $this->statusSaving("$title too Short", false);
        } else if ($MobileNumberTotal == 0) {

            $this->statusSaving("$title not Detected", false);
        } else if ($MobileNumberTotal > $maxNumber) {

            $this->statusSaving("$MobileNumberTotal $title Detected", false);
        } else {

            $this->statusSaving("OK", true);
        }
        return $this;
    }

    function chkDateString(string $postName, string $title): self
    {
        $date = (string)$_POST[$postName];
        $this->counting($postName, $date);
        $date_ar = explode("-", $date);

        if (!$date) {

            $this->statusSaving("Please Enter $title", false);
        } else if (!preg_match("/^([0-9])*\-([0-9])*\-([0-9])+$/", $date)) {

            $this->statusSaving("$title Format Must be 'yyyy-dd-mm'", false);
        } else if (!checkdate($date_ar[1] + 0, $date_ar[2] + 0, $date_ar[0] + 0)) {

            $this->statusSaving("Invalid $title", false);
        } else {

            $this->statusSaving("OK", true);
        }
        return $this;
    }

    function chkDateTimeString(string $postName, string $title): self
    {
        $date = (string)$_POST[$postName];
        $this->counting($postName, $date);

        if (!$date) {

            $this->statusSaving("Please Enter $title", false);
        } else if (!preg_match("/^([0-9])*\-([0-9])*\-([0-9])*\ ([0-9])*\:([0-9])*\ ([ampAPM])+$/", $date)) {

            $this->statusSaving("$title Format Must be 'yyyy-dd-mm hh:ii AM'", false);
        } else {

            $this->statusSaving("OK", true);
        }
        return $this;
    }

    function chkUrl(string $postName, string $title): self
    {
        $url = (string)$_POST[$postName];
        $this->counting($postName, $url);

        if (!$url) {

            $this->statusSaving("Please Enter $title", false);
        } else if (!filter_var($url, FILTER_VALIDATE_URL)) {

            $this->statusSaving("Invalid $title", false);
        } else {

            $this->statusSaving("OK", true);
        }
        return $this;
    }

    function chkDomain(string $postName, string $title = "Domain")
    {
        $domain = (string)$_POST[$postName];
        $this->counting($postName, $domain);

        $domain = str_replace('www.', '', $domain);
        $domain_ar = explode(".", $domain);
        $domain2 = array_pop($domain_ar);
        $domain1 = implode(".", $domain_ar);

        if (!$domain) {

            $this->statusSaving("Please Enter $title", false);
        } else if (in_array("", $domain_ar)) {

            $this->statusSaving("Invalid $title", false);
        } else if (substr($domain1, 0, 1) == ".") {

            $this->statusSaving("Invalid $title", false);
        } else if (!$domain1 || !preg_match('/^[a-z0-9\.]+$/', $domain1)) {

            $this->statusSaving("Invalid $title", false);
        } else if (!$domain2 || !preg_match('/^[a-z0-9]+$/', $domain2)) {

            $this->statusSaving("Invalid $title", false);
        } else {

            $this->statusSaving("OK", true);
        }
        return $this;
    }

    function chkIp(string $postName, string $title = "IP"): self
    {
        $ip = (string)$_POST[$postName];
        $this->counting($postName, $ip);

        if (!$ip) {

            $this->statusSaving("Please Enter $title", false);
        } else if (!filter_var($ip, FILTER_VALIDATE_IP)) {

            $this->statusSaving("Invalid $title", false);
        } else {

            $this->statusSaving("OK", true);
        }
        return $this;
    }

    function chkTrue(string $postName, string $message): self
    {
        $st = (bool)$_POST[$postName];
        $this->counting($postName, (string)$st);

        if (!$st) {

            $this->statusSaving($message, false);
        } else {

            $this->statusSaving("OK", true);
        }

        return $this;
    }

    function chkFalse(string $postName, string $message): self
    {
        $st = (bool)$_POST[$postName];
        $this->counting($postName, $st);

        if ($st) {

            $this->statusSaving($message, false);
        } else {

            $this->statusSaving("OK", true);
        }

        return $this;
    }

    function chkFile(string $fileName, string $title): self
    {
        $file_ar = (array)$_FILES[$fileName];
        $this->counting($fileName, json_encode($file_ar['name']));

        if (!$file_ar['name']) {

            $this->statusSaving("$title is missing", false);
        } else if (!$file_ar['type']) {

            $this->statusSaving("$title type is missing", false);
        } else if (!$file_ar['tmp_name']) {

            $this->statusSaving("$title tmp_name is missing", false);
        } else if ($file_ar['error']) {

            $this->statusSaving("$title upload error", false);
        } else if (!$file_ar['size']) {

            $this->statusSaving("$title is empty size", false);
        } else {

            $this->statusSaving("OK", true);
        }
        return $this;
    }

    function chkFileExist(string $postNameOfPath, string $title)
    {
        $path = (string)$_POST[$postNameOfPath];
        $this->counting($postNameOfPath, (string)$path);

        if (!$postNameOfPath) {

            $this->statusSaving($title . " not Found", false);
        } else if (!is_file($path)) {

            $this->statusSaving("Error on " . $title . " or " . $title . " Not Exist", false);
        } else {

            $this->statusSaving("OK", true);
        }

        return $this;
    }

    function setOptional(): self
    {
        $this->optionPositionAr[$this->counting] = 1;
        return $this;
    }

    function validate(): self
    {
        //$args_ar = func_get_args();
        $this->validatedStatus = false;

        //--Set Data
        $this->validatedMessagesAr = $this->messageAr;
        $this->validatedStatusAr = $this->statusAr;

        foreach ($this->statusAr as $countingPosition => $st) {
            if ($this->optionPositionAr[$countingPosition] != 1 && $st == false) {
                //--Set Data
                $this->validatedStatus = false;

                //--Reset Data
                $this->messageAr = [];
                $this->statusAr = [];

                return $this;
            }
        }
        //--Set Data
        $this->validatedStatus = true;

        //--Reset Data
        $this->messageAr = [];
        $this->statusAr = [];

        return $this;
    }

    function getStatus(): bool
    {
        return $this->validatedStatus;
    }

    function getFirstErrorMessage(): string
    {
        foreach ($this->validatedStatusAr as $sl => $st) {
            if (!$st) {
                return $this->validatedMessagesAr[$sl];
            }
        }
        return "";
    }

    function getFirstErrorName(): string
    {
        $name = "";
        foreach ($this->validatedStatusAr as $sl => $st) {
            if (!$st) {
                return $this->positionNameAr[$sl] ?: "";
            }
        }
        return $name;
    }

    function getFirstErrorPosition(): int
    {
        foreach ($this->validatedStatusAr as $sl => $st) {
            if (!$st) {
                return $sl;
            }
        }
        return -1;
    }
}