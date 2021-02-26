<?php

namespace Packages\mysql;

use Packages\bikiran\Validation;
use PDOException;

class QueryInsert
{
    private string $table = "";
    private \PDO $pdo;
    private int $rowIndex = 0;
    private array $rowAll_ar = [];
    private int $error = 1;
    private string $message = "Not Pushed";
    private array $defCol_ar = [];
    private string $queryString = "";
    private int $lastInsertedId = 0;

    public function __construct(string $table, bool $isSetDefaultCols = true)
    {
        $this->table = $table;
        $this->pdo = pdo();

        if ($isSetDefaultCols == true)
            $this->setDefaultCols();
    }

    public function setDefaultCols(string $col = null, string $defaultValue = null): self // null = all default
    {
        if ($col === null && $defaultValue === null) {
            $this->defCol_ar['creator'] = getUserSl();
            $this->defCol_ar['ip_long'] = getIpLong();
            $this->defCol_ar['time_created'] = getTime();
            $this->defCol_ar['time_updated'] = getTime();
            $this->defCol_ar['time_deleted'] = 0;
        } else if ($col) {
            $this->defCol_ar[$col] = $defaultValue;
        }
        return $this;
    }

    public function addRow(array $row_ar): self
    {
        foreach ($row_ar as $key => $val) {
            $this->rowAll_ar[$this->rowIndex][$key] = $val;
        }
        if ($this->defCol_ar) {
            foreach ($this->defCol_ar as $key => $val) {
                $this->rowAll_ar[$this->rowIndex][$key] = $val;
            }
        }

        $this->rowIndex++;
        return $this;
    }

    public function push(): self
    {
        $key_ar = [];
        $qVal_ar = [];

        //--Creating Queries
        foreach ($this->rowAll_ar as $row_ar) {
            $val_ar = [];
            ksort($row_ar);
            foreach ($row_ar as $key => $val) {
                $key_ar[$key] = "`$key`";
                $val_ar[] = $val === "NULL" ? "NULL" : $this->pdo->quote($val);
            }

            $qVal_ar[] = "(" . implode(", ", $val_ar) . ")";
        }

        //--Query String
        if ($this->rowAll_ar && $qVal_ar) {
            $this->queryString = "INSERT " . "INTO `" . $this->table . "` (" . implode(", ", $key_ar) . ") VALUES " . implode(", ", $qVal_ar) . ";";

            try {
                $this->pdo->query($this->queryString);

                $this->lastInsertedId = $this->pdo->lastInsertId();
                $this->error = 0;
                $this->message = "Success";
            } catch (PDOException $e) {
                $this->lastInsertedId = 0;
                $this->error = 2;
                $this->message = $e->getMessage() . " on mysql->str";

                //--Log Record
                $qLog = new QueryLog();
                $qLog->saveLogQueryError($this->queryString, $this->message);
            }
        } else{
            $this->message = "Nothing to Insert";
        }
        return $this;
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }

    public function getLastInsertedId(): int
    {
        return $this->lastInsertedId;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}