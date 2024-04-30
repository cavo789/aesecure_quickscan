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

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.formfieldcheckbox');

class JFormFieldTZCheckbox extends JFormField
{

    protected $type = 'TZCheckbox';

    protected function getName($fieldName)
    {
        $name   = parent::getName($fieldName);
        $element    = $this -> element;

        if(isset($element['index']) && $element['index'] != null){
            $name   = preg_replace('/\[\]$/','['.$element['index'].']',$name);
        }
        return $name;
    }

    protected function getInput()
    {
        $field_name = $this -> fieldname;
        $element    = $this -> element;

        // Initialize some field attributes.
        $class     = !empty($this->class) ? ' class="' . $this->class . '"' : '';
        $disabled  = $this->disabled ? ' disabled' : '';
        $value     = !empty($this->value) ? $this->value : '1';
        $required  = $this->required ? ' required aria-required="true"' : '';
        $autofocus = $this->autofocus ? ' autofocus' : '';
        $checked   = $this->checked ? ' checked' : '';

        // Initialize JavaScript field attributes.
        $onclick  = !empty($this->onclick) ? ' onclick="' . $this->onclick . '"' : '';
        $onchange = !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';

        if(isset($element['index']) && $element['index'] != null){
            $this->__set('id', $field_name .$element['index']);
        }

        // Including fallback code for HTML5 non supported browsers.
        JHtml::_('jquery.framework');
        JHtml::_('script', 'system/html5fallback.js');

        $html   = array();
        if($this -> element['merge']) {
            $html[] = '<label class="checkbox" id="'.$this -> id.'-lbl" for="'.$this -> id.'">';
        }

        $html[] = '<input type="checkbox" name="' . $this->name . '" id="' . $this->id . '" value="'
            . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"' . $class . $checked . $disabled . $onclick . $onchange
            . $required . $autofocus . ' />';

        if($this -> element['merge']) {
            $html[] = $this -> getTitle();
            $html[] = '</label>';
        }

        return implode("\n",$html);
    }
}