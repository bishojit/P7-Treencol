<?php

namespace Packages\mobile;

class MobileNumber
{
    private $countryCode = "";
    private $operatorsAllAr = [];
    private $mobileNumberAr = [];
    private $mobileRawAr = [];

    function __construct(string $countryCode) // 880
    {
        $this->countryCode = $countryCode;


        $filePath = "packages/mobile/country/operator-" . $this->countryCode . ".json";
        if (is_file($filePath)) {
            $this->operatorsAllAr = json_decode(file_get_contents($filePath), true);
        }
    }

    private function addMobileNoS($mobileNumber): self
    {
        $mobileNumberRaw = $mobileNumber;

        //--Step-1.0: Replace 00 to + from starting
        if (substr($mobileNumber, 0, 2) == "00") { // if start with 00, replace with + // if full format number
            $mobileNumber = substr($mobileNumber, 2);
        }

        //--Step-1.1: Replace 00 to + from starting
        if (substr($mobileNumber, 0, 1) == "+") { // if start with 00, replace with + // if full format number
            $mobileNumber = substr($mobileNumber, 1);
        }

        //--Step-2: For non plus number
        if (substr($mobileNumber, 0, 1) != "+") {  // number without +
            //--Step-2.1: Check Country code //8801925363333
            if (substr($mobileNumber, 0, strlen($this->countryCode)) != $this->countryCode) {
                $mobileNumber = $this->mobileNumberJoin($this->countryCode, $mobileNumber);
            }
        }

        $mobileNumberLen = strlen($mobileNumber);

        foreach ($this->operatorsAllAr as $opCode => $item_ar) {
            $countryAndOpCode = $this->mobileNumberJoin($this->countryCode, $opCode);

            if (substr($mobileNumber, 0, strlen($countryAndOpCode)) == $countryAndOpCode && $mobileNumberLen == $item_ar['fullDigit']) {
                $this->mobileNumberAr[$mobileNumber] = $mobileNumber;
                $this->mobileRawAr[$mobileNumberRaw] = $mobileNumber;
                return $this;
            }
        }

        return $this;
    }

    private function addMobileNoM($longText, $splitWith = " "): self
    {
        $replace_ar = [
            '(' => '',
            ')' => '',
            '{' => '',
            '}' => '',
            '[' => '',
            ']' => '',
            '-' => '',
        ];

        $longText = str_replace(array_keys($replace_ar), array_values($replace_ar), $longText);
        $longText = preg_replace('/[^0-9\+\\' . $splitWith . ']/', $splitWith, $longText);
        $longText_ar = array_filter(explode($splitWith, $longText));

        foreach ($longText_ar as $mobileNo) {
            $this->addMobileNoS($mobileNo);
        }
        return $this;
    }

    private function mobileNumberJoin(string $partOne, string $partTwo): string
    {
        if (substr($partOne, -1) == substr($partTwo, 0, 1)) {
            return $this->countryCode . substr($partTwo, 1);
        } else {
            return $this->countryCode . $partTwo;
        }
    }

    public function addLongString($longText): self
    {
        $this->addMobileNoM($longText, ',');
        $this->addMobileNoM($longText, ' ');
        $this->addMobileNoM($longText, '-');
        $this->addMobileNoM($longText, '/');
        return $this;
    }

    public function getNumbers()
    {
        return $this->mobileNumberAr;
    }

    public function getNumbersRawAr()
    {
        return $this->mobileRawAr;
    }
}