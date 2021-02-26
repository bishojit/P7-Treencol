<?php


namespace Packages\mysql;


use PDO;
use PDOException;

class QuerySelect
{

    private $table = "";
    private $pdo;
    private $indexColumn = "sl";
    private $creatorColumn = null;
    private $deletedColumn = "time_deleted";
    private $error = 1;
    private $message = "Not Pulled";
    private $indexValueAr = []; // 2nd Priority  // 1st Priority is $sl on pull
    private $queryString = "";
    private $rowAllAr = [];
    private $selectTblCols = "*";

    private $cacheFilePath = "";
    private $cacheDuration = 0;
    private $QueryCache = "";
    private $cacheChecksumQuery = "";

    private $isCacheValid = false;
    private $errorRecords = true;
    private $itemInPage = 0;
    private $pageNumber = 0;

    public function __construct($table = "")
    {
        $this->table = $table;
        $this->pdo = pdo();
        return $this;
    }

    function setRemoteDB(string $host, string $userName, string $password, string $dbName, array $opt = []): QuerySelect
    {
        if (empty($opt)) {
            $opt = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
        }

        //--DB Connection
        try {
            $pdo = new PDO('mysql:host=' . $host . ';dbname=' . $dbName . ';charset=utf8', $userName, $password, $opt);
        } catch (PDOException $e) {
            die("Not Connected ($host)");
        }

        $this->pdo = $pdo;
        $this->errorRecords = false;
        $this->deletedColumn = null;
        return $this;
    }

    function setDeletedColumn($colName): QuerySelect
    {
        $this->deletedColumn = $colName;
        return $this;
    }

    public function enableFileCache(string $filePath, int $duration, bool $isChecksumQuery = true): QuerySelect
    {
        $this->cacheFilePath = $filePath;
        $this->cacheDuration = $duration;
        $this->cacheChecksumQuery = $isChecksumQuery;
        $this->QueryCache = new QueryCache($filePath, $duration);

        $this->isCacheValid = !$this->QueryCache->getIsDataRequired();
        return $this;
    }

    public function setCreatorColumn(string $creatorColumn = "creator"): QuerySelect
    {
        $this->creatorColumn = $creatorColumn;
        return $this;
    }

    public function setQueryString(string $queryString = ""): QuerySelect
    {
        $this->queryString = $queryString;
        return $this;
    }

    public function setPaging($itemInPage, $pageNumber)
    {
        $this->itemInPage = $itemInPage;
        $this->pageNumber = $pageNumber;
    }

    private function pullDataCollect(): QuerySelect
    {
        if (!strpos(strtolower($this->queryString), "where")) {
            $this->error = 3;
            $this->message = "There is no 'WHERE' on query string";
        } else {
            try {
                $qOut = $this->pdo->query($this->queryString);

                while ($row = $qOut->fetch()) {
                    $this->rowAllAr[] = $row;
                }

                $this->error = 0;
                $this->message = "Success";
            } catch (PDOException $e) {
                $this->error = 2;
                $this->message = $e->getMessage();

                //--Log Error Record
                if ($this->errorRecords) {
                    $qLog = new QueryLog();
                    $qLog->saveLogQueryError($this->queryString, $this->message);
                }
            }
        }
        return $this;
    }

    private function queryMixer(): QuerySelect
    {
        $extQuery = [];
        if ($this->creatorColumn !== null) {
            $extQuery[] = "`" . $this->table . "`.`" . $this->creatorColumn . "`=" . quote(getUserSl()) . " ";
        }

        if ($this->deletedColumn !== null) {
            if ($this->table) {
                $extQuery[] = "`" . $this->table . "`.`" . $this->deletedColumn . "`= '0' ";
            } else {
                $extQuery[] = "`" . $this->deletedColumn . "`= '0' ";
            }
        }

        //--
        if ($extQuery) {
            if (strpos($this->queryString, "WHERE")) {
                $this->queryString = str_replace("WHERE", " WHERE " . implode(" AND ", $extQuery) . " AND ", $this->queryString);
            } else if (strpos($this->queryString, "where")) {
                $this->queryString = str_replace("where", " where " . implode(" AND ", $extQuery) . " AND ", $this->queryString);
            }
        }
        if ($this->itemInPage) {
            $queryString_ar = explode("LIMIT", $this->queryString);

            $index = $this->itemInPage * $this->pageNumber;
            $this->queryString = $queryString_ar[0] . "LIMIT " . $index . ", " . $this->itemInPage;
        }
        return $this;
    }

    public function pull(int $sl = null)
    {
        if ($this->indexValueAr) { // 2nd Priority
            $sl_ar = [];
            foreach ($this->indexValueAr as $val) {
                $sl_ar[$val] = $this->pdo->quote($val);
            }
            $this->queryString = "SELECT " . $this->selectTblCols . " " . " FROM `$this->table` WHERE `" . $this->indexColumn . "` IN (" . implode(", ", $sl_ar) . ") ";
        }

        if ($sl !== null) { // 1st Priority
            $this->queryString = "SELECT " . $this->selectTblCols . " " . " FROM `$this->table` WHERE `" . $this->indexColumn . "`=" . $this->pdo->quote($sl);
        }

        $this->queryMixer();


        if ($this->cacheDuration <= 0) {
            $this->pullDataCollect();
        } else {
            $queryCache = new QueryCache($this->cacheFilePath, $this->cacheDuration, $this->cacheChecksumQuery ? $this->queryString : null);

            if ($queryCache->getIsDataRequired()) {
                $this->pullDataCollect();

                $queryCache->setData($this->rowAllAr);
            }

            $this->rowAllAr = $queryCache->getData();
        }

        return $this;
    }

    public function getRow(int $index = 0): array
    {
        return $this->rowAllAr[$index] ?: [];
    }

    public function getRows(string $keyCol = null): array
    {
        $row_all_ar = $this->rowAllAr;
        if ($keyCol !== null) {
            $rowAllAr = [];
            foreach ($this->rowAllAr as $row_ar) {
                $rowAllAr[$row_ar[$keyCol]] = $row_ar;
            }
            $row_all_ar = $rowAllAr;
        }
        return $row_all_ar;
    }

    public function getRowsCustomOrder(string $keyCol, array $orderKey_ar): array
    {
        $out_all_ar = [];

        $rowAllAr = [];
        foreach ($this->rowAllAr as $row_ar) {
            $rowAllAr[$row_ar[$keyCol]] = $row_ar;
        }

        foreach ($orderKey_ar as $key) {
            $out_all_ar[$key] = $rowAllAr[$key];
        }

        return $out_all_ar;
    }

    public function getGroupRows(string $groupCol, string $keyCol = null): array
    {
        $rowAllAr = [];
        foreach ($this->rowAllAr as $row_ar) {
            if ($keyCol === null)
                $rowAllAr[$row_ar[$groupCol]][] = $row_ar;
            else
                $rowAllAr[$row_ar[$groupCol]][$row_ar[$keyCol]] = $row_ar;
        }
        return $rowAllAr;
    }

    public function getMultiGroupRows(array $col_ar, string $keyCol = null): array
    {
        $rowAllAr = [];
        $count = count($col_ar);

        foreach ($this->rowAllAr as $row_ar) {
            if ($keyCol === null) {
                if ($count == 1) {
                    $rowAllAr[$row_ar[$col_ar[0]]][] = $row_ar;
                } else if ($count == 2) {
                    $rowAllAr[$row_ar[$col_ar[0]]][$row_ar[$col_ar[1]]][] = $row_ar;
                } else if ($count == 3) {
                    $rowAllAr[$row_ar[$col_ar[0]]][$row_ar[$col_ar[1]]][$row_ar[$col_ar[2]]][] = $row_ar;
                } else if ($count == 4) {
                    $rowAllAr[$row_ar[$col_ar[0]]][$row_ar[$col_ar[1]]][$row_ar[$col_ar[2]]][$row_ar[$col_ar[3]]][] = $row_ar;
                } else if ($count == 5) {
                    $rowAllAr[$row_ar[$col_ar[0]]][$row_ar[$col_ar[1]]][$row_ar[$col_ar[2]]][$row_ar[$col_ar[3]]][$row_ar[$col_ar[4]]][] = $row_ar;
                }
            } else {
                if ($count == 1) {
                    $rowAllAr[$row_ar[$col_ar[0]]][$row_ar[$keyCol]] = $row_ar;
                } else if ($count == 2) {
                    $rowAllAr[$row_ar[$col_ar[0]]][$row_ar[$col_ar[1]]][$row_ar[$keyCol]] = $row_ar;
                } else if ($count == 3) {
                    $rowAllAr[$row_ar[$col_ar[0]]][$row_ar[$col_ar[1]]][$row_ar[$col_ar[2]]][$row_ar[$keyCol]] = $row_ar;
                } else if ($count == 4) {
                    $rowAllAr[$row_ar[$col_ar[0]]][$row_ar[$col_ar[1]]][$row_ar[$col_ar[2]]][$row_ar[$col_ar[3]]][$row_ar[$keyCol]] = $row_ar;
                } else if ($count == 5) {
                    $rowAllAr[$row_ar[$col_ar[0]]][$row_ar[$col_ar[1]]][$row_ar[$col_ar[2]]][$row_ar[$col_ar[3]]][$row_ar[$col_ar[4]]][$row_ar[$keyCol]] = $row_ar;
                }
            }
        }

        return $rowAllAr;
    }

    public function getColValues(string $keyCol, bool $isFilter = true): array
    {
        $rowAllAr = [];
        if ($keyCol !== null) {
            $rowAllAr = [];
            foreach ($this->rowAllAr as $row_ar) {
                $rowAllAr[$row_ar[$keyCol]] = $row_ar[$keyCol];
            }
        }

        if ($isFilter) {
            return array_filter($rowAllAr);
        } else {
            return $rowAllAr;
        }
    }

    public function getGroupColValues(string $groupCol, string $keyCol): array
    {
        $rowAllAr = [];
        foreach ($this->rowAllAr as $row_ar) {
            $rowAllAr[$row_ar[$groupCol]][$row_ar[$keyCol]] = $row_ar[$keyCol];
        }
        return $rowAllAr;
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function isCacheValid(): bool
    {
        return $this->isCacheValid;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

}