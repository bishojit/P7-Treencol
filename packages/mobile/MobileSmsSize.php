<?php

namespace Packages\mobile;

class MobileSmsSize
{
    private string $charSet = "";
    private array $unitSizeAr = [
        'ASCII' => 160,
        'UTF-8' => 70,
    ];
    private int $joinSize = 3;
    private int $smsSize = 0;
    private int $textLength = 0;
    private int $remainCharSize = 0;

    function __construct($smsText)
    {
        $this->charSet = mb_detect_encoding($smsText);
        $this->smsSize = $this->calcSmsSize($smsText);
    }

    function getSize(): int
    {
        return $this->smsSize;
    }

    function getCharSet(): string
    {
        return $this->charSet;
    }

    function getRemainChar(): int
    {
        return $this->remainCharSize;
    }

    public function getTextLength(): int
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