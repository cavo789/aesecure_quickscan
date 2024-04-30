<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2015 templaza.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Factory;

class TZ_Portfolio_PlusHtmlSidebar extends JHtmlSidebar{

    public static function addEntry($name, $link = '', $active = false, $cur_link = true)
    {
        if(!empty($link) && $cur_link) {
            $input          = Factory::getApplication() -> input;
            $component_link = array();
            if($option = $input -> getCmd('option')){
                $component_link[]   = 'option='.$option;
            }
            if($view = $input -> getCmd('view')){
                $component_link[]   = 'view='.$view;
            }
            if($layout = $input -> getCmd('layout')){
                $component_link[]   = 'layout='.$layout;
            }
            if($id = $input -> getInt('addon_id')){
                $component_link[]   = 'addon_id='.$id;
            }
            if($link) {
                $component_link[]   = $link;
            }
            if(count($component_link)){
                $link   = 'index.php?'.implode('&',$component_link);
            }
        }

		parent::addEntry($name, $link, $active);
    }

}