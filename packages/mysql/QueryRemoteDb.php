<?php


namespace Packages\mysql;


use PDO;
use PDOException;

class QueryRemoteDb
{
    private $host = "";
    private $username = "";
    private $password = "";
    private $dbName = "";
    private $opt = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    private $error = 1;
    private $messageAr = [
        0 => "Connected",
        1 => "Not Checked",
    ];
    /**
     * @var PDO
     */
    private $pdo;

    function __construct(String $host, String $username, String $password, String $db)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->dbName = $db;
        $this->checkConnection();
    }

    function checkConnection(): bool
    {
        //--DB Connection
        try {
            $this->pdo = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->dbName . ';charset=utf8', $this->username, $this->password, $this->opt);
            $this->error = 0;
            $this->messageAr[0] = "Connected";
            return true;
        } catch (PDOException $e) {
            $this->error = 2;
            $this->messageAr[2] = $e->getMessage();
            return false;
        }
    }

    function checkDbExist($dbName): bool
    {
        if (!$this->pdo) {
            $this->checkConnection();
        }

        $this->pdo->query("");
        return false;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function getMessage(): string
    {
        return $this->messageAr[$this->error];
    }

    public function getPdo()
    {
        return $this->pdo;
    }
}