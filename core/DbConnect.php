<?php

namespace Core;

use PDO;
use PDOException;

class DbConnect
{
    private string $host = 'localhost';
    private string $username = 'root';
    private string $password = '';
    private string $db = '';
    private string $charset = 'utf8mb4';
    private array $opt = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    private AppInit $AppInit;

    public function __construct(AppInit $AppInit)
    {
        $this->AppInit = $AppInit;
        $default_domain = $this->AppInit->getDefaultDomain();

        $configDbObj = xmlFileToObject("configs/access/" . $default_domain . "/" . $default_domain . ".database.xml", "DB Config File Not Found.");

        $this->host = (string)$configDbObj->host;
        $this->username = (string)$configDbObj->username;
        $this->password = (string)$configDbObj->password;
        $this->db = (string)$configDbObj->db;
    }

    public function connect(): PDO
    {
        //--DB Connection
        try {
            return new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->db . ';charset=' . $this->charset,
                $this->username,
                $this->password,
                $this->opt
            );
        } catch (PDOException $e) {
            dc($e);

            ErrorPages::DbConnect(1, "Not Connected-" . $this->host . " (" . $this->AppInit->getDefaultDomain() . ")");
            exit();
        }
    }

    public function getOpt(): array
    {
        return $this->opt;
    }
}