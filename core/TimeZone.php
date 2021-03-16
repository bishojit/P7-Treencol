<?php


namespace Core;


use DateTimeZone;

class TimeZone
{
    private array $listOfTimeZone = [];
    private int $time = 0;

    function __construct()
    {
        $this->listOfTimeZone = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
    }

    function setTimeZone(string $userTimeZone): bool
    {

        if (!$userTimeZone) {

            ErrorPages::TimeZone(1, "Error on TimeZone > setTimeZone ($userTimeZone).");
        }

        if (in_array($userTimeZone, $this->listOfTimeZone)) {
            //--Set Time Zone
            date_default_timezone_set($userTimeZone);
            ini_set("date.timezone", $userTimeZone);
            $this->time = time();
            return true;
        } else {

            ErrorPages::TimeZone(2, "Invalid Time Zone [" . $userTimeZone . "].");
        }
        return false;
    }

    function getTime(): int
    {
        return $this->time;
    }
}