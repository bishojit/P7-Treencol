<?php


namespace Packages\bikiran;


class PhpGd
{
    static public function showHtmlImage($imgRes)
    {
        // start buffering
        ob_start();
        imagepng($imgRes);
        $contents = ob_get_contents();
        ob_end_clean();

        echo "<img src='data:image/png;base64," . base64_encode($contents) . "' />";

        imagedestroy($imgRes);
    }

    static public function zoomByWidth($width, $height, $newWidth): array
    {
        $ratio = $width / $newWidth;
        return [
            'width' => round($width / $ratio),
            'height' => round($height / $ratio),
        ];
    }

    static public function zoomByHeight($width, $height, $newHeight): array
    {
        $ratio = $height / $newHeight;
        return [
            'width' => round($width / $ratio),
            'height' => round($height / $ratio),
        ];
    }

    static public function ImageTTFCenter($image, $text, $font, $size, $angle = 0)
    {
        $xi = imagesx($image);
        $yi = imagesy($image);

        $box = imagettfbbox($size, $angle, $font, $text);

        $xr = abs(max($box[2], $box[4]));
        $yr = abs(max($box[5], $box[7]));

        $x = round(($xi - $xr) / 2);
        $y = round(($yi + $yr) / 2);

        return array($x, $y);
    }

    static public function ImageCenter($resSource, $xI, $yI)
    {
        $xS = imagesx($resSource);
        $yS = imagesy($resSource);

        /*$xI = imagesx($resImg);
        $yI = imagesy($resImg);*/

        $x = round(($xS - $xI) / 2);
        $y = round(($yS + $yI) / 2);

        return array($x, $y);
    }
}