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

class TZ_Portfolio_PlusExtraFieldRadio extends TZ_Portfolio_PlusExtraField{
    protected $multiple_option  = true;

    public function getInput($fieldValue = null, $group = null){

        if(!$this -> isPublished()){
            return "";
        }

        $options = $this->getFieldValues();
        $value   = !is_null($fieldValue) ? $fieldValue : $this->value;

        $this->setAttribute("type", "radio", "input");

        $this->setVariable('options', $options);
        $this->setVariable('value', $value);

        return $this->loadTmplFile('input.php', __CLASS__);
    }

    protected function getParentAttribute(){

        if($this -> getAttribute('disabled', null, 'input')){
            return ' disabled=""';
        }
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
        $params = $this -> params;

        if ($this->getAttribute('type', '', 'search') == '')
        {
            $this->setAttribute('type', 'text', 'search');
        }

        if ((int) $this->params->get('size', 32))
        {
            $this->setAttribute('size', (int) $this->params->get('size', 32), 'search');
        }

        if(isset($this -> dataSearch[$this -> id])){
            $defaultValue  = $this -> dataSearch[$this -> id];
        }

        $this->setVariable('defaultValue', $defaultValue);

        if($this -> multiple_option) {
            $options    = $this->getFieldValues();

            $value      = !is_null($defaultValue) ? $defaultValue : $this->value;
            if($this -> multiple){
                $value  = (array) $value;
            }

            if($params -> get('search_type', 'dropdownlist') == 'dropdownlist'
                || $params -> get('search_type', 'dropdownlist') == 'multiselect'){
                $options    = $this -> removeDefaultOption($options);

                $firstOption = new stdClass();

                $lang   = JFactory::getLanguage();
                $lang -> load('com_tz_portfolio_plus', JPATH_SITE);

                $firstOption->text      = JText::sprintf('COM_TZ_PORTFOLIO_PLUS_OPTION_SELECT', $this->getTitle());
                $firstOption->value     = '';
                $firstOption->default   = 1;

                array_unshift($options, $firstOption);

                if($params -> get('search_type', 'dropdownlist') == 'multiselect'){
                    $this -> setAttribute('multiple', 'multiple', 'search');
                }
            }

            $this->setVariable('options', $options);
            $this->setVariable('value', $value);
        }

        if($params -> get('search_type', 'dropdownlist') == 'checkbox') {
            $this->setAttribute('type', 'checkbox', 'search');
        }elseif($params -> get('search_type', 'dropdownlist') == 'radio'){
            $this->setAttribute('type', 'radio', 'search');
        }

        if($html = $this -> loadTmplFile('searchinput')){
            return $html;
        }

        $html   = '<label class="group-label">'.$this -> getTitle().'</label>';

        $html  .= '<input name="'.$this -> getSearchName().'" id="'.$this -> getSearchId().'" '
            .($this -> isRequired()?' required=""':''). $this->getAttribute(null, null, 'search') .'/>';

        return $html;
    }


    public function prepareForm(&$form, $data){
        parent::prepareForm($form, $data);
        $name   = $form -> getName();
        if($name == 'com_tz_portfolio_plus.addon' || $name == 'com_tz_portfolio_plus.field'){
            if(!COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE){
                $form -> removeField('switcher', 'params');
            }
        }
    }
}