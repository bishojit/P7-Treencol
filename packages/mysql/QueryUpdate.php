<?php

namespace Packages\mysql;

use PDOException;

class QueryUpdate
{
    private string $table = "";
    private \PDO $pdo;
    private string $indexColumn = "sl";
    private string $updatedColumn = "time_updated";
    private string $creatorColumn = "creator";

    private int $error = 1;
    private string $message = "Not Pushed";
    private bool $history = true;
    private array $oldRowAll_ar = [];
    private array $newRowAll_ar = [];
    private string $queryString = "";
    private bool $updateOnlyPermittedRow = true;

    public function __construct(string $table, bool $updateOnlyPermittedRow = true)
    {
        $this->table = $table;
        $this->pdo = pdo();
        $this->updateOnlyPermittedRow = $updateOnlyPermittedRow;

        global $Auth;

        if ($Auth->isAdminPerm()) {
            $this->setAuthorized();
        }

        return $this;
    }

    function setAuthorized(): self
    {
        $this->updateOnlyPermittedRow = false;
        return $this;
    }

    public function setHistory(bool $boolean): self
    {
        $this->history = $boolean;
        return $this;
    }

    public function setIndexColumn(string $indexColumn): self
    {
        $this->indexColumn = $indexColumn;
        return $this;
    }

    public function updateRow(array $oldRow_ar, array $newRow_ar): self
    {
        $indexId = $oldRow_ar[$this->indexColumn];

        $this->oldRowAll_ar[$indexId] = $oldRow_ar;
        $this->newRowAll_ar[$indexId] = $newRow_ar;
        return $this;
    }

    public function push(): self
    {
        $q_ar = [];
        $rowUpdatedIndex_ar = [];

        //--History Update
        $insertHistory = new QueryInsert('log_history');

        foreach ($this->newRowAll_ar as $indexId => $det_ar) {
            $qField_ar = [];

            foreach ($det_ar as $key => $val) {
                //--Index Column Will Not Modified
                if ($key != $this->indexColumn) {

                    //--If Security On/Off Then Condition
                    if (($this->updateOnlyPermittedRow == true && $this->oldRowAll_ar[$indexId][$this->creatorColumn] == getUserSl()) || $this->updateOnlyPermittedRow == false) {

                        if ($this->newRowAll_ar[$indexId][$key] === "NULL") {
                            $qField_ar[$key] = "`$key` = NULL";
                        } else {
                            $qField_ar[$key] = "`$key` = " . $this->pdo->quote($this->newRowAll_ar[$indexId][$key]);
                        }

                        if ($this->history == true && $this->oldRowAll_ar[$indexId][$key] != $this->newRowAll_ar[$indexId][$key]) {
                            $insertHistory->addRow([
                                'tbl' => $this->table,
                                'col' => $key,
                                'tsl' => $indexId,
                                'value_ex' => $this->oldRowAll_ar[$indexId][$key],
                                'value_new' => $this->newRowAll_ar[$indexId][$key]
                            ]);
                        }
                        $rowUpdatedIndex_ar[$indexId] = true;
                    } else {

                        if ($this->history == true && $this->oldRowAll_ar[$indexId][$key] != $this->newRowAll_ar[$indexId][$key]) {
                            $insertHistory->addRow([
                                'tbl' => $this->table,
                                'col' => $key,
                                'tsl' => $indexId,
                                'value_ex' => $this->oldRowAll_ar[$indexId][$key],
                                'value_new' => json_encode([
                                    $this->newRowAll_ar[$indexId][$key],
                                    "No Permission"
                                ])
                            ]);
                        }
                    }
                }

                //--Permission Status Set
                if ($this->updateOnlyPermittedRow == true && $this->oldRowAll_ar[$indexId][$this->creatorColumn] == getUserSl()) {
                    $rowUpdatedIndex_ar[$indexId] = true;
                }
            }

            //--SQL Creation
            if (!empty($qField_ar)) {
                $qField_ar[$this->updatedColumn] = "`" . $this->updatedColumn . "` = " . $this->pdo->quote(getTime());

                $q_ar[] = "
                    UPDATE `" . $this->table . "` 
                    SET " . implode(", ", $qField_ar) . " 
                    WHERE `" . $this->indexColumn . "` = " . $indexId . "
                ";
            }
        }

        //--Insert into history
        if ($this->history == true) {
            $insertHistory->push();
        }

        if (count($this->newRowAll_ar) != count($rowUpdatedIndex_ar)) {
            $this->error = 2;
            $this->message = "Not Updated (Error on few row)";
        } else if (!$q_ar) {
            $this->error = 0;
            $this->message = "Nothing to update";
        } else {
            try {
                foreach ($q_ar as $q) {
                    $this->pdo->query($this->queryString = $q);
                }

                $this->error = 0;
                $this->message = "Updated";
            } catch (PDOException $e) {
                $this->error = 4;
                $this->message = $e->getMessage() . " on mysql->str";

                //--Log Record
                $qLog = new QueryLog();
                $qLog->saveLogQueryError($this->queryString, $this->message);
            }
        }
        return $this;
    }

    public function getOldRowAllAr(): array
    {
        return $this->oldRowAll_ar;
    }

    public function getNewRowAllAr(): array
    {
        return $this->newRowAll_ar;
    }

    public function getQueryString(): string
    {
        return $this->queryString;
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