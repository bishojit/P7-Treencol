<?php


namespace Core;


class SoftInfo
{
    private $data;

    function __construct(AppInit $AppInit)
    {
        //--
        $this->data = xmlFileToObject("configs/access/" . $AppInit->getDefaultDomain() . "/" . $AppInit->getDefaultDomain() . ".softinfo.xml",
            "softinfo.xml is not found in" . $AppInit->getDefaultDomain());

        //--Set Timezone
        global $TimeZone;
        $userTimeZone = (string)$this->data->time->zone;
        $TimeZone->setTimeZone($userTimeZone);
    }

    public function getData(): \SimpleXMLElement
    {
        return $this->data;
    }
}