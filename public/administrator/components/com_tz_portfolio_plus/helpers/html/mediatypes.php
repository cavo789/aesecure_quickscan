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

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class JHtmlMediaTypes
{
    public static function options($group = 'mediatype')
    {
        $options    = array();
        TZ_Portfolio_PlusPluginHelper::importPlugin($group);

        if($results	= Factory::getApplication()->triggerEvent('onAddMediaType')){
            if(count($results)){
                foreach($results as $item) {
                    $options[]  = JHtml::_('select.option', $item->value, $item->text);
                }
            }
        }
        return $options;
    }
}