<?php


namespace Packages\bikiran;


class StringDisplayDate
{
    static function en2bn($in)
    {
        $ar1 = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'AM', 'PM', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday');
        $ar2 = array('০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯', 'এ. এম.', 'পি. এম.', 'জানুয়ারী', 'ফেব্রুয়ারী', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর', 'শনিবার', 'রবিবার', 'সোমবার', 'মঙ্গলবার', 'বুধবার', 'বৃহস্পতিবার', 'শুক্রবার');
        return str_replace($ar1, $ar2, $in);
    }

    static function timeDisplayEn(int $eventTime, bool $spanTitle = false): string
    {
        $timeDeference = getTime() - $eventTime;

        if ($timeDeference < 300) {
            $out = "Now";
        } else if ($timeDeference < 3600) {
            $out = "This hour";
        } else if ($timeDeference < 7200) {
            $out = "Last hour";
        } else if ($timeDeference < 24 * 3600) {
            $out = round($timeDeference / 3600, 0) . " hour ago";
        } else {
            $out = date("Y-m-d", $eventTime);
        }

        if ($spanTitle == true) {
            return "<span title=\"" . date("d M, Y h:i:s A e") . "\">$out</span>";
        } else {
            return $out;
        }
    }

    static function timeDisplayBn(int $eventTime, bool $spanTitle = false): string
    {
        $timeDeference = getTime() - $eventTime;

        if ($timeDeference < 300) {
            $out = "এই মাত্র";
        } else if ($timeDeference < 3600) {
            $out = "এই ঘন্টায়";
        } else if ($timeDeference < 7200) {
            $out = "পূর্বের ঘন্টায়";
        } else if ($timeDeference < 24 * 3600) {
            $out = StringDisplayDate::en2bn(round($timeDeference / 3600, 0)) . " ঘন্টা পূর্বে";
        } else {
            $out = StringDisplayDate::en2bn(date("F d, Y", $eventTime));
        }

        if (!$eventTime) {
            return "---";
        } else if ($spanTitle == true) {
            return "<span title=\"" . StringDisplayDate::en2bn(date("d M, Y h:i:s A e", $eventTime)) . "\">$out</span>";
        } else {
            return $out;
        }
    }
}