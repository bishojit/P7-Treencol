<?php

namespace Packages\bikiran;

use Packages\mysql\QueryInsert;

class SmsApiApp
{
    private string $countryCode = "";
    private string $apiId = "";
    private string $apiKey = "";
    private string $senderName = "";
    private array $messageAr = [];

    function __construct($crdSms_ar)
    {
        $this->countryCode = $crdSms_ar['country-code'];
        $this->apiId = $crdSms_ar['api-id'];
        $this->apiKey = $crdSms_ar['api-key'];
        $this->senderName = $crdSms_ar['sender'];
    }

    function newSms($number, $text): self
    {
        $textMd5 = $text;
        $this->messageAr[$textMd5]['numbers'][] = $number;
        $this->messageAr[$textMd5]['text'] = $text;
        return $this;
    }

    //--sms api code
    function send(): array
    {
        // Configurations
        $parameters = [
            'sender' => $this->senderName, // optional, if not provided then it will use default device
            'api-id' => $this->apiId,
            'api-key' => $this->apiKey,
            'country-code' => $this->countryCode,
        ];

        foreach ($this->messageAr as $message_ar) {
            $numbers = implode(", ", $message_ar['numbers']);
            $text = $message_ar['text'];

            //--Do not Edit Below Code
            $parameters['numbers'] = $numbers;
            $parameters['text'] = $text;
            $url = "https://www.smsapi.app/api/web/send-sms/post/";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $res = curl_exec($ch);
            curl_close($ch);

            $res_ar = json_decode($res, true);
            if ($res_ar['error'] != 0) {

                $insert = new QueryInsert('log_api_error');
                $insert->addRow([
                    'message' => "Error on SmsApiApp: " . json_encode($res_ar),
                ]);
                $insert->push();

                return [
                    'error' => 1,
                    'message' => "SMS Not Sent"
                ];
            }
        }

        return [
            'error' => 0,
            'message' => "SMS Sent Successfully"
        ];
    }
}