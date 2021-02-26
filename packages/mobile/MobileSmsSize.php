<?php


namespace Packages\mobile;


class MobileSmsSize
{
    private $charSet = "";
    private $unitSizeAr = [
        'ASCII' => 160,
        'UTF-8' => 70,
    ];
    private $joinSize = 3;
    private $smsSize = 0;
    private $textLength = 0;
    private $remainCharSize = 0;

    function __construct($smsText)
    {
        $this->charSet = mb_detect_encoding($smsText);
        $this->smsSize = $this->calcSmsSize($smsText);
    }

    function getSize()
    {
        return $this->smsSize;
    }

    function getCharSet()
    {
        return $this->charSet;
    }

    function getRemainChar()
    {
        return $this->remainCharSize;
    }

    public function getTextLength()
    {
        return $this->textLength;
    }

    private function calcSmsSize($smsText)
    {
        $unitSize = $this->unitSizeAr[$this->charSet];
        $this->textLength = mb_strlen($smsText);

        if (ceil($this->textLength / $unitSize) == 1) {
            $this->remainCharSize = $unitSize - $this->textLength;
            return 1;
        } else {
            $smsSize = ceil($this->textLength / ($unitSize - $this->joinSize));
            $this->remainCharSize = $smsSize * ($unitSize - $this->joinSize) - $this->textLength;
            return $smsSize;
        }
    }
}