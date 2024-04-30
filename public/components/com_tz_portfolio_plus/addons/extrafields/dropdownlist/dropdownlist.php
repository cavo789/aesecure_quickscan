<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

//no direct access
defined('_JEXEC') or die('Restricted access');

class TZ_Portfolio_PlusExtraFieldDropDownList extends TZ_Portfolio_PlusExtraField{

    protected $multiple_option = true;

    public function getInput($fieldValue = null, $group = null){

        if(!$this -> isPublished()){
            return "";
        }

        $selectOptions  = array();
        $value          = !is_null($fieldValue) ? $fieldValue : $this->value;

        $options        = $this -> getFieldValues();

        if ($options)
        {
            $optGroupState = "close";
            foreach ($options AS $option)
            {
                $_optionText        = null;
                $_optionValue       = null;
                $_optionDisabled    = null;

                if(is_object($option)){
                    $_optionText    = $option -> text;
                    $_optionValue   = $option -> value;
                    if (isset($option->disabled) && $option->disabled)
                    {
                        $_optionDisabled    = $option->disabled;
                    }
                }else{
                    $_optionText    = $option['text'];
                    $_optionValue   = $option['value'];
                    if (isset($option['disabled']) && $option['disabled'])
                    {
                        $_optionDisabled    = $option['disabled'];
                    }
                }

                if ($_optionText == strtoupper($_optionText))
                {
                    $text = JText::_($_optionText);
                }
                else
                {
                    $text = $_optionText;
                }

                $selectOptionItem['text']  = htmlspecialchars($text, ENT_COMPAT, 'UTF-8');
                $selectOptionItem['value'] = htmlspecialchars($_optionValue, ENT_COMPAT, 'UTF-8');

                if (strtoupper($_optionValue) == "<OPTGROUP>")
                {
                    if ($optGroupState == "open")
                    {
                        $selectOptions[] = JHtml::_('select.option', '</OPTGROUP>');
                        $optGroupState   = "close";
                    }
                    $selectOptions[] = JHtml::_('select.option', '<OPTGROUP>', $selectOptionItem['text']);
                    $optGroupState   = "open";
                }
                elseif (strtoupper($_optionValue) == "</OPTGROUP>")
                {
                    $selectOptions[] = JHtml::_('select.option', '</OPTGROUP>');
                    $optGroupState   = "close";
                }
                else
                {
                    if ($_optionDisabled)
                    {
                        $selectOptions[] = JHtml::_('select.option', $selectOptionItem['value'], $selectOptionItem['text'], "value", "text", true);
                    }
                    else
                    {
                        $selectOptions[] = JHtml::_('select.option', $selectOptionItem['value'], $selectOptionItem['text']);
                    }
                }
            }
        }

        $this->setAttribute("class", $this->getInputClass(), "input");
        if ((int) $this->params->get("size"))
        {
            $this->setAttribute("size", (int) $this->params->get("size"), "input");
        }

        $this->setVariable('value', $value);
        $this->setVariable('options', $selectOptions);

        return $this -> loadTmplFile('input', __CLASS__);
    }

    public function getSearchName(){
        $params = $this -> params;
        if($params -> get('search_type', 'dropdownlist') == 'checkbox'
            || $params -> get('search_type', 'dropdownlist') == 'multiselect') {
            return 'fields[' . $this->id . '][]';
        }
        return 'fields['.$this -> id.']';
    }

    public function getSearchInput($defaultValue = '')
    {
        if (!$this->isPublished())
        {
            return '';
        }

        $this->setVariable('defaultValue', $defaultValue);

        if($this -> multiple_option) {
            $options    = $this->getFieldValues();

            $app    = JFactory::getApplication();
            $input  = $app -> input;
            if($datasearch = $input -> get('fields', array(), 'array')){
                if(isset($datasearch[$this -> id]) && !empty($datasearch[$this -> id])){
                    $defaultValue  = $datasearch[$this -> id];
                }
            }

            $value      = !is_null($defaultValue) ? $defaultValue : $this->value;
            $params     = $this -> params;

            if($this -> multiple){
                $value  = (array) $value;
            }

            if($params -> get('search_type', 'dropdownlist') == 'dropdownlist'
                || $params -> get('search_type', 'dropdownlist') == 'multiselect') {
                $firstOption = new stdClass();

                $lang = JFactory::getLanguage();
                $lang->load('com_tz_portfolio_plus', JPATH_SITE);

                $firstOption->text = JText::sprintf('COM_TZ_PORTFOLIO_PLUS_OPTION_SELECT', $this->getTitle());
                $firstOption->value = '';

                array_unshift($options, $firstOption);
                if($params -> get('search_type', 'dropdownlist') == 'multiselect'){
                    $this -> setAttribute('multiple', 'multiple', 'search');
                }
            }else{
                $this->setAttribute('type', 'checkbox', 'search');
            }

            $this->setVariable('options', $options);
            $this->setVariable('value', $value);
        }

        if($html = $this -> loadTmplFile('searchinput', __CLASS__)){
            return $html;
        }

        $this -> setAttribute('class', 'form-select', 'search');

        $html   = '<label class="form-label">'.$this -> getTitle().'</label>';

        $html  .= '<input name="'.$this -> getSearchName().'" id="'.$this -> getSearchId().'" '
            .($this -> isRequired()?' required=""':''). $this->getAttribute(null, null, 'search') .'/>';

        return $html;
    }
}