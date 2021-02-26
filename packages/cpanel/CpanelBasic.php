<?php

namespace Packages\cpanel;

class CpanelBasic
{
    private int $error = 1;
    private string $host = "";
    private array $header = [];
    private string $resultJson = "";
    private int $httpStatus = 0;
    private string $message = "";

    function __construct($host, $user)
    {
        $this->host = $host;
        $authData_ar = json_decode(file_get_contents("configs/access/" . getDefaultDomain() . "/json_configs/cpanel_auth.json"), true);
        $token = $authData_ar[$host][$user]['token'];

        $this->header[0] = "Authorization: whm $user:$token";
    }


    function getAccountList(): array
    {
        $accList = [];
        $query = "https://{$this->host}:2087/json-api/listaccts?api.version=1";


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($curl, CURLOPT_URL, $query);
        $this->resultJson = curl_exec($curl);
        $this->httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($this->httpStatus != 200) {
            $this->error = 1;
            $this->message = "[!] Error: " . $this->httpStatus . " returned";
        } else {
            $this->error = 0;
            $accList = json_decode($this->resultJson, true)['data']['acct'];
            $this->message = "[+] Current cPanel users on the system:";
        }

        return [
            'error' => $this->error,
            'message' => $this->message,
            'acc_list' => $accList,
        ];
    }

    function createUserSession($cpUser, $domain): array
    {
        $url = "";
        $query = "https://{$this->host}:2087/json-api/create_user_session?api.version=1&user=$cpUser&service=cpaneld";

        $curl = curl_init();                                     // Create Curl Object.
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);       // Allow self-signed certificates...
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);       // and certificates that don't match the hostname.
        curl_setopt($curl, CURLOPT_HEADER, false);               // Do not include header in output
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);        // Return contents of transfer on curl_exec.
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->header);         // Set the username and password.
        curl_setopt($curl, CURLOPT_URL, $query);                 // Execute the query.
        $this->resultJson = curl_exec($curl);
        $this->httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($this->httpStatus != 200) {
            $this->error = 1;
            $this->message = "[!] Error: " . $this->httpStatus . " returned";
        } else {
            $this->error = 0;
            $url = str_replace($this->host, $domain, json_decode($this->resultJson, true)['data']['url']);
            $this->message = "[+] Current cPanel users on the system:";
        }

        return [
            'error' => $this->error,
            'message' => $this->message,
            'url' => $url,
        ];
    }

    public function getResultJson(): string
    {
        return $this->resultJson;
    }
}