<?php


namespace Packages\google\firebase;


class FirebaseCloudMessaging
{
    function sendNotification(array $token_ar, array $message_ar)
    {
        $fcm_server_key = "AAAAUg0gcFE:APA91bGVwO2vEj5V19q6_0byQefjKJXKC-n9ELLtwKUiN12dYFIBMIoaEFTKgxMO8g7eRqCS2HMXXKJbeSzhRi1tvCZQpbZt3vCR4LjiYwDdCNOj6qYPnusvLE916Ksq8BaMCFkXKpdG";

        /*foreach ($token_ar as $token) {
            $registrationIds[] = $token['token'];
        }*/
        // $tokens = ['cCLA1_8Inic:APA91bGhuCksjWEETYWVOh04scsZInxdWmXekEr5F9-1zJuTDZDw3It_tNmpA__PmoxDTISZzplD_ciXvsuw2pMtYSzdfIUAUfcTLnghvJS0CVkYW9sVx2HnF1rqnxsFgSdYmcXpHKLs'];

        $header = [
            'Authorization: Key=' . $fcm_server_key,
            'Content-Type: Application/json'
        ];

        /*$message_ar = [
            'title' => 'Testing Notification',
            'body' => 'Testing Notification from localhost',
            'icon' => 'img/icon.png',
            'image' => 'img/d.png',
        ];*/

        $payload = [
            'registration_ids' => $token_ar,
            'data' => $message_ar,
        ];


        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => $header
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        return $response;
    }
}