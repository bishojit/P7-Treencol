<?php


namespace Packages\mysql;


use PDOException;

class QueryUpdate
{
    private $table = "";
    private $pdo;
    private $indexColumn = "sl";
    private $updatedColumn = "time_updated";
    private $creatorColumn = "creator";

    private $error = 1;
    private $message = "Not Pushed";
    private $history = true;
    private $oldRowAll_ar = [];
    private $newRowAll_ar = [];
    private $queryString = "";
    private $updateOnlyPermittedRow = true;

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

    function setAuthorized(): QueryUpdate
    {
        $this->updateOnlyPermittedRow = false;
        return $this;
    }

    public function setHistory(bool $boolean): QueryUpdate
    {
        $this->history = $boolean;
        return $this;
    }

    public function setIndexColumn(string $indexColumn): QueryUpdate
    {
        $this->indexColumn = $indexColumn;
        return $this;
    }

    public function updateRow(array $oldRow_ar, array $newRow_ar): QueryUpdate
    {
        $indexId = $oldRow_ar[$this->indexColumn];

        $this->oldRowAll_ar[$indexId] = $oldRow_ar;
        $this->newRowAll_ar[$indexId] = $newRow_ar;
        return $this;
    }

    public function push(): QueryUpdate
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

    public function setMessage(string $message): QueryUpdate
    {
        $this->message = $message;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}

/*



        foreach ($this->oldRowAll_ar as $sl => $oldRow_ar) {
            $qField_ar = [];
            foreach ($oldRow_ar as $key => $val) {
                $indexId = $oldRow_ar[$this->indexColumn];

                //--Index Column Will Not Modified
                if ($this->newRowAll_ar[$indexId][$key] !== null && $key != $this->indexColumn) {// &&  && $this->newRowAll_ar[$indexId][$key] != $val

                    if (($this->updateOnlyPermittedRow == true && $this->oldRowAll_ar[$indexId][$this->creatorColumn] == getUserSl()) || $this->updateOnlyPermittedRow == false) {

                        $qField_ar[$key] = "`$key` = " . $this->pdo->quote($this->newRowAll_ar[$indexId][$key]);

                        if ($this->history == true) {
                            $insertHistory->addRow([
                                'tbl' => $this->table,
                                'col' => $key,
                                'tsl' => $indexId,
                                'value_ex' => $val,
                                'value_new' => $this->newRowAll_ar[$indexId][$key]
                            ]);
                        }
                        $rowUpdatedIndex_ar[$indexId] = true;
                    } else {

                        if ($this->history == true) {
                            $insertHistory->addRow([
                                'tbl' => $this->table,
                                'col' => $key,
                                'tsl' => $indexId,
                                'value_ex' => $val,
                                'value_new' => json_encode([$this->newRowAll_ar[$indexId][$key], "No Permission"])
                            ]);
                        }
                    }
                }


                if ($this->updateOnlyPermittedRow == true && $this->oldRowAll_ar[$indexId][$this->creatorColumn] == getUserSl()) {
                    $rowUpdatedIndex_ar[$indexId] = true;
                }
            }
            if ($qField_ar) {
                $q_ar[] = "
                UPDATE `" . $this->table . "`
                SET `" . $this->updatedColumn . "`=" . $this->pdo->quote(getTime()) . ",
                    " . implode(", ", $qField_ar) . "
                WHERE `" . $this->indexColumn . "` = " . $oldRow_ar[$this->indexColumn];
            }
        }
*/