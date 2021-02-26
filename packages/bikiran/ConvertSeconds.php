<?php


namespace Packages\bikiran;


class ConvertSeconds
{
    public static function toHumanDuration(int $seconds): string
    {
        $out_ar = [];

        $secondRemain = $seconds;

        $year = floor($secondRemain / 31557600);
        $secondRemain -= $year * 31557600;

        $month = floor($secondRemain / 2630880);
        $secondRemain -= $month * 2630880;

        $day = floor($secondRemain / 86400);
        $secondRemain -= $day * 86400;

        $hour = floor($secondRemain / 3600);
        $secondRemain -= $hour * 3600;

        $minute = floor($secondRemain / 60);
        $secondRemain -= $minute * 60;

        if ($year > 1) {
            $out_ar[] = "$year  Year";
        }
        if ($month > 1) {
            $out_ar[] = "$month Month";
        }
        if ($day > 1) {
            $out_ar[] = "$day Day";
        }
        if ($hour > 1) {
            $out_ar[] = "$hour Hour";
        }
        if ($minute > 1) {
            $out_ar[] = "$minute Minute";
        }
        if ($secondRemain > 1) {
            $out_ar[] = "$secondRemain Second";
        }

        return implode(", ", array_splice($out_ar, 0, 2));
    }
}