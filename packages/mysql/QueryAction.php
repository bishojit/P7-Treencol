<?php


namespace Packages\mysql;


class QueryAction
{
    static function setAsDefault($data_all_ar, $data_ar, $tbl, $col, $defaultValue = 1): QueryUpdate
    {
        $QueryUpdate = new QueryUpdate($tbl);
        if (!$data_ar['is_default']) {
            foreach ($data_all_ar as $det_ar) {
                $QueryUpdate->updateRow($det_ar, [
                    $col => $data_ar['sl'] == $det_ar['sl'] ? $defaultValue : 0
                ]);
            }
            $QueryUpdate->push();
        } else {
            $QueryUpdate->setMessage("Default is Already Set");
        }

        return $QueryUpdate;
    }
}