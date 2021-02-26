<?php


namespace Packages\bikiran;


class ConvertArray
{
    public static function toSet(array $array2d, string $key, bool $filter = false): array
    {
        $out_ar = [];
        if (!is_array($array2d)) {
            return [];
        } else if (!$key) {
            return [];
        }

        foreach ($array2d as $ar) {
            $out_ar[$ar[$key]] = $ar[$key];
        }

        if ($filter !== false) {
            $out_ar = array_filter($out_ar);
        }

        return $out_ar;
    }

    public static function toColSum(array $array2d, string $key): float
    {
        $out_ar = [];
        if (!is_array($array2d)) {
            return 0;
        } else if (!$key) {
            return 0;
        }

        foreach ($array2d as $ar) {
            $out_ar[] = $ar[$key];
        }

        return array_sum($out_ar);
    }

    public static function changeKey(array $array2d, string $key): array
    {
        $out_all_ar = [];

        if (!is_array($array2d)) {
            return [];
        } else if (!$key) {
            return [];
        }

        foreach ($array2d as $ar) {
            $out_all_ar[$ar[$key]] = $ar;
        }

        return $out_all_ar;
    }

    public static function toGroup(array $array2d, string $groupKey, string $columnKey): array
    {
        $out_all_ar = [];

        if (!is_array($array2d)) {
            return [];
        } else if (!$columnKey) {
            return [];
        }

        foreach ($array2d as $ar) {
            $out_all_ar[$ar[$groupKey]][$ar[$columnKey]] = $ar;
        }

        return $out_all_ar;
    }
}