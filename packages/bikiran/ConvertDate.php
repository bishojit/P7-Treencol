<?php


namespace Packages\bikiran;


class ConvertDate
{
    public static function YmdToY_m_d(string $Ymd): string
    {
        return substr($Ymd, 0, 4) . "-" . substr($Ymd, 4, 2) . "-" . substr($Ymd, 6, 2);
    }

    public static function Y_m_dToYmd(string $Y_m_d): string
    {
        return str_replace("-", "", $Y_m_d);
    }

    public static function Ym_To_MonthEndTime(int $Year, int $month, int $startTime = 0): int
    {
        if (!$startTime) {
            $startTime = strtotime($Year . "-" . $month . "-01");
        }

        return strtotime($Year . "-" . $month . "-" . date("t", $startTime));
    }
}