<?php


namespace Packages\bikiran;


class Pagination
{
    private int $totalRows = 0;
    private int $pageRows = 0;
    private int $pageNo = 0;

    public function __construct(int $totalRows, int $pageRows, int $pageNo = 0)
    {
        $this->pageRows = $pageRows;
        $this->pageNo = $pageNo;
        $this->totalRows = $totalRows;
    }

    public function getNavigation()
    {
        $url = route()->getUriRoute();
        $dotted = false;
        $li_ar = [];
        $numberOfPage = ceil($this->totalRows / $this->pageRows);

        $get = $_GET;
        for ($i = 0; $i < $numberOfPage; $i++) { //active
            $get['page'] = $i + 1;
            $li_ar[$i] = "<li class=\"paginate_button page-item " . ($this->pageNo == $i ? "active" : "") . "\">
                <a href=\"" . mkUrl($url, [], $get) . "\" class=\"page-link\">" . ($i + 1) . "</a>
            </li>";
        }

        //--Split on Over 9 Pages
        if ($numberOfPage > 9) {

            foreach ($li_ar as $index => $li) {
                if ($index >= 3 && $index <= $numberOfPage - 4 && $index != $this->pageNo && $index != $this->pageNo - 1 && $index != $this->pageNo + 1) {
                    unset($li_ar[$index]);
                }
            }

            if (!$li_ar[$this->pageNo - 2] && $this->pageNo > 2) {
                $dotted = true;
                $li_ar[$this->pageNo - 2] = "<li class=\"paginate_button page-item disabled\"><a href=\"#\" class=\"page-link\">...</a></li>";
            }

            if (!$li_ar[$this->pageNo + 2] && $this->pageNo < $numberOfPage - 2) {
                $dotted = true;
                $li_ar[$this->pageNo + 2] = "<li class=\"paginate_button page-item disabled\"><a href=\"#\" class=\"page-link\">...</a></li>";
            }

            if ($dotted == false) {
                $li_ar[4] = "<li class=\"paginate_button page-item disabled\"><a href=\"#\" class=\"page-link\">...</a></li>";
            }
        }

        ksort($li_ar);

        //--
        $getPrev = $_GET;
        $getPrev['page'] = $this->pageNo;

        $getNext = $_GET;
        $getNext['page'] = $this->pageNo + 2;

        return "
        <ul class=\"pagination\">
            <li class=\"paginate_button page-item previous " . ($this->pageNo <= 0 ? "disabled" : "") . "\" id=\"example_previous\">
                <a href=\"" . mkUrl($url, [], $getPrev) . "\" class=\"page-link\">&laquo;</a>
            </li>
            
            " . implode($li_ar) . "
            
            <li class=\"paginate_button page-item next " . ($this->pageNo >= $numberOfPage - 1 ? "disabled" : "") . "\" id=\"example_next\">
                <a href=\"" . mkUrl($url, [], $getNext) . "\" class=\"page-link\">&raquo;</a>
            </li>
        </ul>
        ";
    }
}