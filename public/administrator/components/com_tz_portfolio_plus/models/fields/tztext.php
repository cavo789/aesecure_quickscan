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

JFormHelper::loadFieldClass('text');

class JFormFieldTZText extends JFormFieldText{

    protected $type = 'TZText';

    protected function getInput(){
        $element        = $this -> element;

        if($this -> multiple){
            $this -> __set('name',$this -> fieldname);
            if(is_array($this -> value)){
                $this -> value  = array_shift($this -> value);
            }
        }

        if($element && isset($element['data-provide'])){
            $str    = '" data-provide="'.(string) $element['data-provide'];
            $this -> id = $this->id.$str;
        }
        $html   = parent::getInput();
        return $html;
    }

}