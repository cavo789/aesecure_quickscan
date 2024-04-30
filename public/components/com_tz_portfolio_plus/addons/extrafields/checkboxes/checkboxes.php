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

class TZ_Portfolio_PlusExtraFieldCheckboxes extends TZ_Portfolio_PlusExtraField{
    protected $multiple_option  = true;
    protected $multiple         = true;

    public function getInput($fieldValue = null, $group = null){

        if (!$this->isPublished())
        {
            return "";
        }

        $this->setAttribute("type", "checkbox", "input");

        $value   = !is_null($fieldValue) ? (array) $fieldValue : (array) $this->value;
        $options = $this->getFieldValues();

        $this->setVariable('value', $value);
        $this->setVariable('options', $options);

        return $this->loadTmplFile('input.php', __CLASS__);
    }

    public function getSearchInput($defaultValue = ''){

        if (!$this->isPublished())
        {
            return '';
        }

        $this->setAttribute('type', 'checkbox', 'search');

        return parent::getSearchInput($defaultValue);
    }
}