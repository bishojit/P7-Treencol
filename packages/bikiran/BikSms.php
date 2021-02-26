<?php

namespace Packages\bikiran;


use Packages\mobile\MobileNumber;

class BikSms
{
    private $username = "";
    private $password = "";
    private $messageAr = [];
    private $senderNo = "";
    /**
     * @var MobileNumber
     */
    private $MobileNumber;

    function __construct()
    {
        $crdSms_ar = json_decode(file_get_contents("configs/access/" . getDefaultDomain() . "/" . getDefaultDomain() . ".sms-config.json"), true);

        $this->username = $crdSms_ar['smsUsername'];
        $this->password = $crdSms_ar['smsPassword'];
        $this->senderNo = $crdSms_ar['senderNo'];

        $this->MobileNumber = new MobileNumber("880");
    }

    function newSms($number, $message)
    {
        $this->MobileNumber->addLongString($number);
        if ($fullNumber = $this->MobileNumber->getNumbersRawAr()[$number]) {
            $this->messageAr[] = [
                substr($fullNumber, -11),
                $message,
                date("d/m/Y h:i:s A", time() - 84600)
            ];
        }

        return $this;
    }

    //--Input => array("to", "msg", "dd/mm/yyyy hh:mm AM"); //--Time Must Be Send As UTC Format //--Output => OK, er_user, er_number, er_ip, er_unknown
    function send()
    {
        $msg_ar = $this->messageAr;
        if (is_array($msg_ar)) {
            foreach ($msg_ar as $msg) {
                $q_str[] = "to[]=$msg[0]&msg[]=$msg[1]&sdt[]=$msg[2]";
            }
        } else {
            return "er_input";
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://bik.bz/api/input.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "user=" . $this->username . "&pass=" . $this->password . "&" . implode("&", $q_str));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $out = curl_exec($ch);
        curl_close($ch);

        return $out;
    }

    public function getSenderNo()
    {
        return $this->senderNo;
    }
}



/*
Usage

$BikSms=new BikSms();
$BikSms->newSms("01925363333", "Test SMS");
$BikSms->newSms("01717416667", "Test SMS");
echo $BikSms->send();

*/