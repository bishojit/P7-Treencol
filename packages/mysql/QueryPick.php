<?php


namespace Packages\mysql;


use PDO;
use PDOException;

class QueryPick
{
    private $indexColumn = "sl";
    private $deletedColumn = "time_deleted";
    private $isPulled = false;

    /**
     * @var PDO
     */
    private $pdo;
    private $tbl = "";
    private $tblAr = [];
    private $parameterAr = [];
    private $whereString = "";
    private $groupByString = "";
    private $orderByString = "";
    private $limitString = "";
    private $joinStringAllAr = [];

    private $rowAllAr = [];
    private $queryString = "";
    private $error = 1;
    private $messageAr = [
        0 => "No Error",
        1 => "Not Pulled",
    ];
    private $totalRow = 0;

    function __construct(string $tbl, $parameterMixed = [])
    {
        $this->tbl = $tbl;
        $this->tblAr[$tbl] = $tbl;

        if (is_string($parameterMixed)) {
            $this->parameterAr[$tbl]['parameter-string'] = $parameterMixed;
        }

        if (is_array($parameterMixed)) {
            foreach ($parameterMixed as $prm) {
                $this->parameterAr[$tbl][$prm] = $prm;
            }
        }
        $this->pdo = pdo();
    }

    function setRemote(QueryRemoteDb $queryRemoteDb)
    {
        $this->pdo = $queryRemoteDb->getPdo();
    }

    function setSecure()
    {
        $this->creatorColumn = "creator";
    }

    function setWhere(string $whereString): self
    {
        $this->whereString = $whereString;
        return $this;
    }

    function setGroupBy(string $groupByString): self
    {
        $this->groupByString = $groupByString;
        return $this;
    }

    function setOrderBy(string $orderByString): self
    {
        $this->orderByString = $orderByString;
        return $this;
    }

    function setLimit(int $numberOfRows): self
    {
        $this->limitString = $numberOfRows;
        return $this;
    }

    function setPaging(int $itemInPage, int $pageNumber): self
    {
        $index = $itemInPage * $pageNumber;
        $this->limitString = $index . ", " . $itemInPage;
        return $this;
    }

    function addLeftJoin(string $tbl, string $onString, $parameterMixed = [])
    {
        $this->tblAr[$tbl] = $tbl;

        if (is_string($parameterMixed)) {
            $this->parameterAr[$tbl]['parameter-string'] = $parameterMixed;
        }

        if (is_array($parameterMixed)) {
            foreach ($parameterMixed as $prm) {
                $this->parameterAr[$tbl][$prm] = $prm;
            }
        }

        $this->joinStringAllAr['LEFT'][] = "`$tbl` ON $onString";
        return $this;
    }

    function addRightJoin($tbl, $onString, $parameterMixed = [])
    {
        $this->tblAr[$tbl] = $tbl;

        if (is_string($parameterMixed)) {
            $this->parameterAr[$tbl]['parameter-string'] = $parameterMixed;
        }

        if (is_array($parameterMixed)) {
            foreach ($parameterMixed as $prm) {
                $this->parameterAr[$tbl][$prm] = $prm;
            }
        }

        $this->joinStringAllAr['RIGHT'][] = "`$tbl` ON $onString";
        return $this;
    }

    private function genParameter(): string
    {
        $cols_ar = [];
        foreach ($this->parameterAr as $tbl => $perm_ar) {
            foreach ($perm_ar as $key => $col) {
                if ($key != "parameter-string") {
                    $cols_ar[] = "`$tbl`.`$col`";
                } else {
                    $cols_ar[] = $col;
                }
            }
        }

        return (empty($cols_ar) ? "*" : implode(", ", $cols_ar)) . " ";
    }


    private function genWhere(): string
    {
        //--Starting
        $whereStr = "1";
        if ($this->whereString) {
            $whereStr = $this->whereString;
        }

        //--Do Not Collect Deleted Row
        if ($this->deletedColumn) {
            foreach ($this->tblAr as $tbl) {
                $whereStr .= " AND `$tbl`.`" . $this->deletedColumn . "`=0 ";
            }
        }

        return "WHERE " . $whereStr . " ";
    }

    private function pullDataCollect(): self
    {
        try {
            $qOut = $this->pdo->query($this->queryString);

            while ($row = $qOut->fetch()) {
                $this->rowAllAr[] = $row;
            }

            $this->error = 0;
        } catch (PDOException $e) {
            $this->error = 2;
            $this->messageAr[2] = $e->getMessage();

            //--Log Error Record
            $qLog = new QueryLog();
            $qLog->saveLogQueryError($this->queryString, $this->messageAr[2]);
        }

        return $this;
    }

    private function pull(): self
    {
        //--Query Creator
        $this->queryString = "SELECT ";
        $this->queryString .= $this->genParameter();
        $this->queryString .= "FROM `" . $this->tbl . "` ";
        if ($this->joinStringAllAr) {
            foreach ($this->joinStringAllAr as $key => $det_ar) {
                foreach ($det_ar as $str) {
                    $this->queryString .= $key . " JOIN " . ($str ?: 1) . " ";
                }
            }
        }
        $this->queryString .= $this->genWhere();
        if ($this->groupByString) {
            $this->queryString .= "GROUP BY " . $this->groupByString . " ";
        }
        if ($this->orderByString) {
            $this->queryString .= "ORDER BY " . $this->orderByString . " ";
        }
        if ($this->limitString) {
            $this->queryString .= "LIMIT " . $this->limitString . " ";
        }

        $this->pullDataCollect();

        $this->isPulled = true;
        return $this;
    }

    private function pullRowCount(): int
    {
        $totalRow = 0;

        //--Query Creator
        $queryString = "SELECT COUNT(1) AS 'total' ";
        $queryString .= "FROM `" . $this->tbl . "` ";
        if ($this->joinStringAllAr) {
            foreach ($this->joinStringAllAr as $det_ar) {
                foreach ($det_ar as $key => $str) {
                    $queryString .= $key . " JOIN " . ($this->whereString ?: 1) . " ";
                }
            }
        }
        $queryString .= $this->genWhere();

        try {
            $qOut = $this->pdo->query($queryString);

            while ($row = $qOut->fetch()) {
                $this->totalRow = $row['total'];
                return $this->totalRow;
            }
        } catch (PDOException $e) {
            //--Log Error Record
            $qLog = new QueryLog();
            $qLog->saveLogQueryError($queryString, $e->getMessage());
            return 0;
        }

        return 0;
    }

    function getTotalPiked($filtered = true): int
    {
        if ($filtered) {
            if ($this->isPulled == false) {
                $this->pull();
            }
            return count($this->rowAllAr);
        } else {
            if ($this->totalRow) {
                return $this->totalRow;
            } else {
                return $this->pullRowCount();
            }
        }
    }

    function getRow(int $index = 0): array
    {
        if ($this->isPulled == false) {
            $this->pull();
        }

        return $this->rowAllAr[$index] ?: [];
    }

    function getRows(string $col = null): array
    {
        if ($this->isPulled == false) {
            $this->pull();
        }

        $row_all_ar = $this->rowAllAr;
        if ($col !== null) {
            $rowAllAr = [];
            foreach ($this->rowAllAr as $row_ar) {
                $rowAllAr[$row_ar[$col]] = $row_ar;
            }
            $row_all_ar = $rowAllAr;
        }
        return $row_all_ar;
    }

    public function getGroupRows(string $groupCol, string $keyCol = null): array
    {
        if ($this->isPulled == false) {
            $this->pull();
        }

        $rowAllAr = [];
        foreach ($this->rowAllAr as $row_ar) {
            if ($keyCol === null) {
                $rowAllAr[$row_ar[$groupCol]][] = $row_ar;
            } else {
                $rowAllAr[$row_ar[$groupCol]][$row_ar[$keyCol]] = $row_ar;
            }
        }
        return $rowAllAr;
    }

    public function getMultiGroupRows(array $col_ar, string $keyCol = null): array
    {
        if ($this->isPulled == false) {
            $this->pull();
        }

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

    public function getQueryString(): string
    {
        if ($this->isPulled == false) {
            $this->pull();
        }
        return $this->queryString;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function getMessage(): string
    {
        return $this->messageAr[$this->error];
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    public function setDeletedColumn($deletedColumn): self
    {
        $this->deletedColumn = $deletedColumn;
        return $this;
    }

    public static function start(string $tbl, $parameterMixed = [])
    {
        return new self($tbl, $parameterMixed);
    }
}