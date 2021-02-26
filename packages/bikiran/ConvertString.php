<?php

namespace Packages\bikiran;

class ConvertString
{
    public static function en2bn(string $in): string
    {
        $ar1 = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'AM', 'PM', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday');
        $ar2 = array('০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯', 'এ. এম.', 'পি. এম.', 'জানুয়ারী', 'ফেব্রুয়ারী', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর', 'শনিবার', 'রবিবার', 'সোমবার', 'মঙ্গলবার', 'বুধবার', 'বৃহস্পতিবার', 'শুক্রবার');
        return str_replace($ar1, $ar2, $in);
    }

    static function num2words(int $number): string
    {
        $hyphen = '-';
        $conjunction = ' and ';
        $separator = ', ';
        $negative = 'negative ';
        $decimal = ' point ';
        $dictionary = array(
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
            20 => 'twenty',
            30 => 'thirty',
            40 => 'fourty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety',
            100 => 'hundred',
            1000 => 'thousand',
            1000000 => 'million',
            1000000000 => 'billion',
            1000000000000 => 'trillion',
            1000000000000000 => 'quadrillion',
            1000000000000000000 => 'quintillion'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int)$number < 0) || (int)$number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_num2words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . ConvertString::num2words(abs($number));
        }

        $string = null;
        $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int)($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . ConvertString::num2words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int)($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = ConvertString::num2words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= ConvertString::num2words($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string)$fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }


    static function cleanStr(string $string, string $acceptedPattern = null): string
    {
        if ($acceptedPattern == null) {
            $string = preg_replace('/[^\da-z ]/i', '', $string);
        } else {
            $string = preg_replace($acceptedPattern, ' ', $string);
        }

        $string = preg_replace('/\s+/', ' ', $string);

        return trim($string);
    }

    static function cleanStrUtf8(string $string, string $acceptedPattern = null): string
    {
        if ($acceptedPattern == null) {
            $string = preg_replace('/[^\da-z\x00-\x1F\x7F-\xFF\ ]/i', ' ', $string);
        } else {
            $string = preg_replace($acceptedPattern, ' ', $string);
        }

        $string = preg_replace('/\s+/', ' ', $string);

        return trim($string);
    }

    public static function wordSplit(string $paragraph, int $start, int $number, string $ending = '...'): string
    {
        $in_ar = explode(" ", strip_tags($paragraph));
        $in2_ar = array_filter(array_slice($in_ar, $start, $number));

        if ($ending && sizeof($in_ar) > $number) {
            $in2_ar[] = "...";
        }

        return implode(" ", $in2_ar);
    }

    static function permToDisplay(string $string): string
    {
        return ucwords(str_replace("_", " ", $string));
    }

    static function youTubeIdFromUrl($url)
    {
        $re = '/(?:https?:)?(?:\/\/)?(?:[0-9A-Z-]+\.)?(?:youtu\.be\/|youtube(?:-nocookie)?\.com\/\S*?[^\w\s-])((?!videoseries)[\w-]{11})(?=[^\w-]|$)(?![?=&+%\w.-]*(?:[\'"][^<>]*>|<\/a>))[?=&+%\w.-]*/im';
        preg_match_all($re, $url, $matches, PREG_SET_ORDER, 0);
        return $matches[0][1];
    }

    static function fbIdFromUrl($url)
    {
        $re = '/(https?:\/\/)?(www.)?(facebook\.com)\/(\S*\/)*(videos?\/(embed\?video_id=|vb.\d+\/)?)(\d+)/im';
        preg_match_all($re, $url, $matches, PREG_SET_ORDER, 0);
        return $matches[0][7];
    }

    public static function num2digitWord(float $amount): string
    {
        $ar = [
            0 => "Zero",
            1 => "One",
            2 => "Two",
            3 => "Three",
            4 => "Four",
            5 => "Five",
            6 => "Six",
            7 => "Seven",
            8 => "Eight",
            9 => "Nine",
            "." => "Dot",
        ];
        return str_replace(array_keys($ar), array_values($ar), implode(" ", str_split($amount)));
    }

    public static function extractEmails(string $str): array
    {
        // This regular expression extracts all emails from a string:
        $regexp = '/([a-z0-9_\.\-])+\@(([a-z0-9\-])+\.)+([a-z0-9]{2,4})+/i';
        preg_match_all($regexp, $str, $m);

        return isset($m[0]) ? $m[0] : [];
    }

    public static function tinyFilter(string $text)
    {
        $filter_ar = [
            "\n" => " ",
            "\t" => " ",
            "             " => " ",
            "            " => " ",
            "           " => " ",
            "          " => " ",
            "         " => " ",
            "        " => " ",
            "       " => " ",
            "      " => " ",
            "     " => " ",
            "    " => " ",
            "   " => " ",
            "  " => " ",
        ];

        $text = str_replace(array_keys($filter_ar), array_values($filter_ar), $text);
        return trim(str_replace(array_keys($filter_ar), array_values($filter_ar), $text));
    }
}