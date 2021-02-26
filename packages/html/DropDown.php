<?php


namespace Packages\html;


class DropDown
{

    private $name;
    private $optionGroup;
    private $optionAllAr;
    private $default;
    private $attributeAr;
    private $optionPrefix;
    private $optionPostfix;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->setAttribute('name', $name);
        $this->optionGroup = "default";
        return $this;
    }

    public function setOptionGroup(string $label): DropDown
    {
        $this->optionGroup = $label;
        return $this;
    }

    public function setOptionPrefix(string $prefix): DropDown
    {
        $this->optionPrefix = $prefix;
        return $this;
    }

    public function setOptionPostfix(string $prefix): DropDown
    {
        $this->optionPostfix = $prefix;
        return $this;
    }

    public function setOption(string $value, string $option): DropDown
    {
        $this->optionAllAr[$this->optionGroup][$value] = $this->optionPrefix . $option . $this->optionPostfix;
        return $this;
    }

    public function setOptionSeries(int $start, int $end): DropDown
    {
        if ($start < $end) {
            for ($i = $start; $i <= $end; $i++) {
                $this->optionAllAr[$this->optionGroup][$i] = $this->optionPrefix . $i . $this->optionPostfix;
            }
        } else {
            for ($i = $start; $i >= $end; $i--) {
                $this->optionAllAr[$this->optionGroup][$i] = $this->optionPrefix . $i . $this->optionPostfix;
            }
        }
        return $this;
    }

    public function setOptionArrayS(array $array, string $stringStyle = "", $valAndKeySame = false): DropDown
    {
        foreach ($array as $value => $option) {
            if ($valAndKeySame == true) {
                $value = $option;
            }
            if ($stringStyle && function_exists($stringStyle))
                $this->optionAllAr[$this->optionGroup][$value] = $this->optionPrefix . $stringStyle($option) . $this->optionPostfix;
            else
                $this->optionAllAr[$this->optionGroup][$value] = $this->optionPrefix . $option . $this->optionPostfix;
        }
        return $this;
    }

    public function setOptionArrayM(array $array_all, string $valueKey, string $optionKey, string $stringStyle = ""): DropDown
    {
        foreach ($array_all as $det_ar) {
            if ($stringStyle && function_exists($stringStyle))
                $this->optionAllAr[$this->optionGroup][$det_ar[$valueKey]] = $this->optionPrefix . $stringStyle($det_ar[$optionKey]) . $this->optionPostfix;
            else
                $this->optionAllAr[$this->optionGroup][$det_ar[$valueKey]] = $this->optionPrefix . $det_ar[$optionKey] . $this->optionPostfix;
        }
        return $this;
    }

    public function setDefault($value): DropDown
    {
        $this->default = $value;
        $this->attributeAr['data-default'] = $value;
        return $this;
    }

    public function removeOptionS(string $value, string $group = "default"): DropDown
    {
        unset($this->optionAllAr[$group][$value]);
        return $this;
    }

    public function removeOptionM(array $value_ar, string $group = "default"): DropDown
    {
        foreach ($value_ar as $value) {
            unset($this->optionAllAr[$group][$value]);
        }
        return $this;
    }

    public function setAttribute(string $name, string $value): DropDown
    {
        $this->attributeAr[$name] = $value;
        return $this;
    }

    public function setAsRequired(): DropDown
    {
        $this->attributeAr['required'] = "required";
        return $this;
    }

    public function setAsDisabled(): DropDown
    {
        $this->attributeAr['disabled'] = "disabled";
        return $this;
    }

    public function getHtml(): string
    {
        $opHtml = "";
        $attributeHtml = "";
        $opGroupHtml = "";
        $optGroup = "";

        //--Attribute Arranging
        if ($this->attributeAr) {
            foreach ($this->attributeAr as $name => $value) {
                $attributeHtml .= "$name=\"$value\" ";
            }
        }

        //--Default Option Arranging
        if ($this->optionAllAr["default"]) {
            foreach ($this->optionAllAr["default"] as $value => $option) {
                if ($this->default == $value)
                    $opHtml .= "<option value=\"$value\" selected>$option</option>";
                else
                    $opHtml .= "<option value=\"$value\">$option</option>";
            }
        }
        unset($this->optionAllAr["default"]);

        //--ModelGroup Option Arranging
        if ($this->optionAllAr) {
            foreach ($this->optionAllAr as $label => $optionAllAr) {
                if ($optionAllAr) {
                    foreach ($optionAllAr as $value => $option) {
                        if ($this->default == $value)
                            $opGroupHtml .= "<option value=\"$value\" selected>$option</option>";
                        else
                            $opGroupHtml .= "<option value=\"$value\">$option</option>";
                    }
                }
                $optGroup .= "<optgroup label=\"$label\">$opGroupHtml</optgroup>";
                unset($opGroupHtml);
            }
        }
        return "<select " . $attributeHtml . ">$opHtml $optGroup</select>";
    }
}