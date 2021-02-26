<?php


namespace Core;


use DOMDocument;

class LiveTemplate
{
    private $modulesAllAr = [];
    private $allTemplatesAr = [];
    private $htmlAr = [];
    private $jsAr = [];

    function __construct()
    {
        $i = 0;
        $routsAppConfigObj = xmlFileToObject("app/app-config.xml");
        foreach ($routsAppConfigObj->modules as $modulesObj) {
            if ((string)$modulesObj->attributes()->status[0] == 1) {
                $priority = $modulesObj->attributes()->priority * 100 + ++$i;
                $this->modulesAllAr[$priority] = $modulesObj;
            }
        }

        ksort($this->modulesAllAr);

        foreach ($this->modulesAllAr as $modulesObj) {
            $dir = (string)$modulesObj->Attributes()->dir;
            //--
            $xmlRoutesPath = realpath("app/$dir/live-template.xml");
            if (is_file($xmlRoutesPath)) {
                $routsObj = xmlFileToObject($xmlRoutesPath, "live-template.xml is not found on module " . $modulesObj->attributes()->dir);

                foreach ($routsObj->path as $pathObj) {
                    $id = (string)$pathObj->Attributes()->id;
                    $view = (string)$pathObj->Attributes()->view;

                    $this->allTemplatesAr[$id] = [
                        'id' => $id,
                        'view' => $view,
                        'dir' => $dir,
                    ];

                    $this->getTemplateHtml($id, $dir, $view);
                }
            }
        }
    }

    private function getTemplateHtml($id, $dir, $view)
    {
        $filePath = realpath("app/$dir/views/$view");
        if (is_file($filePath)) {
            $fileContent = file_get_contents($filePath);

            $dom = new DOMDocument();

            libxml_use_internal_errors(true);
            $dom->loadHTML($fileContent);
            libxml_clear_errors();

            $html = $dom->saveHTML($dom->getElementsByTagName('div')->item(0));
            if ($html) {
                $this->htmlAr[$id] = str_replace("[projectId]", "#$id", $html);
            }

            if ($dom->getElementsByTagName('script')->item(0)) {
                $js = $dom->saveHTML(($dom->getElementsByTagName('script')->item(0)->childNodes->item(0)));
                if ($js) {
                    $this->jsAr[$id] = str_replace("[this]", $id, $js);
                }
            }
        }
    }

    function getHtmlAsJs()
    {
        return "let live_template_html=" . json_encode($this->htmlAr, JSON_FORCE_OBJECT) . ";";
    }

    function getJs()
    {
        return "" . implode("\n", $this->jsAr) . "";
    }
}