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

JLoader::import('addons.extrafields.dropdownlist.dropdownlist', COM_TZ_PORTFOLIO_PLUS_PATH_SITE);

class TZ_Portfolio_PlusExtraFieldMultipleSelect extends TZ_Portfolio_PlusExtraFieldDropDownList{
    protected $multiple = true;

    public function getInput($fieldValue = null, $group = null){

        if(!$this -> isPublished()){
            return "";
        }

        $this -> setAttribute('multiple', 'multiple', 'input');

        return parent::getInput($fieldValue);
    }

    public function getSearchInput($defaultValue = "")
    {
        $this->setAttribute('type', 'checkbox', 'search');

        $this -> setAttribute('multiple', 'multiple', 'search');

        return parent::getSearchInput($defaultValue);
    }

}