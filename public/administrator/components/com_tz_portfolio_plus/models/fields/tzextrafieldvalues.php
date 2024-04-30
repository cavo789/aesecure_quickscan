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

// No direct access
defined('_JEXEC') or die;

class JFormFieldTZExtraFieldValues extends JFormField
{
    protected $type = "TZExtraFieldValues";

    public function getInput(){
        if($type = $this -> form -> getValue('type')){
            $field_class    = 'TZ_Portfolio_PlusExtraField' . ucfirst($type);
            if(class_exists($field_class)) {
                $field  = $this -> form -> getData();
                if($field_class    = new $field_class($field -> toObject(), 0,
                    array('control' => $this -> formControl))){
                    return $field_class -> getInputDefault($this -> fieldname);
                }
            }
        }
    }
}