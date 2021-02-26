<?php


namespace Packages\bikiran;


class CsvGenerator
{
    //private $cellEnclosure = '"';

    function downloadCsv($data_all_ar, $putHeader = true)
    {
        $row_ar = [];
        if ($putHeader == true) {
            $cell_ar = [];
            foreach (array_values($data_all_ar)[0] as $key => $val) {
                $cell_ar[] .= "\"" . str_replace("\"", "\"\"", $key) . "\"";
            }
            $row_ar[] = implode(",", $cell_ar);
        }

        foreach ($data_all_ar as $det_ar) {
            $cell_ar = [];
            foreach ($det_ar as $key => $val) {
                $cell_ar[] .= "\"" . str_replace("\"", "\"\"", $val) . "\"";
            }
            $row_ar[] = implode(",", $cell_ar);
        }

        $filename = "test.csv";

        header('Content-Type: application/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '";');

        echo implode("\n", $row_ar);
    }
}