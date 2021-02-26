<?php


namespace Packages\send;

class EmailTemplates
{
    private $valuesAllAr = [];
    private $dbCellAllAr = [];
    private $templatePath = "";
    private $subject = "";

    function __construct(string $templatePath = "app/system/email-templates/email_template_default.html")
    {
        $this->setTemplatePath($templatePath);
    }

    function setTemplatePath(string $templatePath): self
    {
        $this->templatePath = str_replace("{domain}", getDefaultDomain(), $templatePath);
        return $this;
    }

    function setSubject(string $subject): self
    {
        $this->valuesAllAr['subject'] = $subject;
        return $this;
    }

    function addValue(string $key, string $val): self
    {
        $this->valuesAllAr[$key] = $val;
        return $this;
    }

    function addValueAr(array $data_ar): self
    {
        foreach ($data_ar as $key => $val) {
            $this->valuesAllAr[$key] = $val;
        }
        return $this;
    }

    function addDbCell(string $key, string $tbl, string $col, int $sl, $func = null)
    {
        $this->dbCellAllAr[$tbl][$col][$sl] = [
            'key' => $key,
            'func' => $func,
        ];
    }

    function getHtml()
    {
        $templateHtml = file_get_contents($this->templatePath);

        $replaceFrom_ar = [];
        $replaceTo_ar = [];
        foreach ($this->valuesAllAr as $key => $val) {
            $replaceFrom_ar[] = "{" . $key . "}";
            $replaceTo_ar[] = str_replace("{domain}", getDefaultDomain(), $val);
        }

        return str_replace($replaceFrom_ar, $replaceTo_ar, $templateHtml);
    }

    public function getBodyText(): string
    {
        return "";
    }

    public function getSubject(): string
    {
        return $this->valuesAllAr['subject'];
    }
}