<?php


namespace Packages\bikiran;


class TreeManager
{
    private $dataAllAr = [];
    private $titleCol = "";
    private $dataArrAllAr = [];

    function __construct($data_all_ar, $titleCol = "")
    {
        $this->dataAllAr = $data_all_ar;
        $this->titleCol = $titleCol;

        foreach ($this->dataAllAr as $det_ar) {
            $this->dataArrAllAr[$det_ar['parent_sl']][$det_ar['sl']] = $det_ar;
        }
    }

    private function getTitle($det_ar, $glue)
    {
        $title = [];
        $i = 0;

        $parentSl = $det_ar['sl'];

        while ($parentSl != 0) {
            $title[] = $this->dataAllAr[$parentSl][$this->titleCol];
            $parentSl = $this->dataAllAr[$parentSl]['parent_sl'];

            if ($i++ == 20) break;
        }
        krsort($title);

        return implode($glue, $title);
    }

    function getChildData($parentSl)
    {
        return $this->dataArrAllAr[$parentSl];
    }

    function getTreeList($filter = "", $glue = " » ")
    {
        $data_arr_ar = [];
        $filter = strtolower($filter);

        if ($this->dataAllAr[$filter]) {
            $data_arr_ar[] = [
                'sl' => $this->dataAllAr[$filter]['sl'],
                $this->titleCol => $this->getTitle($this->dataAllAr[$filter], $glue),
            ];
        } else {
            foreach ($this->dataAllAr as $det_ar) {
                $title = $this->getTitle($det_ar, $glue);

                if (!$filter || ($filter && strpos("_" . strtolower($title), $filter))) {
                    $data_arr_ar[$title] = [
                        'sl' => $det_ar['sl'],
                        $this->titleCol => $title,
                    ];
                }
            }
        }

        ksort($data_arr_ar);

        return $data_arr_ar;
    }
}