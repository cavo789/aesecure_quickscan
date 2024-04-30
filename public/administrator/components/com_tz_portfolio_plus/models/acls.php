<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2017 tzportfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - http://tzportfolio.com/forum

# Family website: http://www.templaza.com

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

class TZ_Portfolio_PlusModelAcls extends JModelList{
    public function getItems(){
        $sections   = json_decode(COM_TZ_PORTFOLIO_PLUS_ACL_SECTIONS);
        $items      = array();
        foreach($sections as $i => $section){
            $item               = new stdClass();
            $item -> section    = $section;
            switch ($section){
                default:
                    $item -> title  = JText::_('COM_TZ_PORTFOLIO_PLUS_'.strtoupper($section).'S');
                    break;
                case 'category':
                    $item -> title  = JText::_('COM_TZ_PORTFOLIO_PLUS_CATEGORIES');
                    break;
                case 'group':
                    $item -> title  = JText::_('COM_TZ_PORTFOLIO_PLUS_FIELD_GROUPS');
                    break;
                case 'style':
                    $item -> title  = JText::_('COM_TZ_PORTFOLIO_PLUS_TEMPLATE_STYLES');
                    break;

            }
            $items[]        = $item;
        }
        return $items;
    }
}