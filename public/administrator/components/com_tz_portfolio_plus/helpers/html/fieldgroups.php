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

use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

class JHtmlFieldGroups
{
    public static function options()
    {
        $db     = TZ_Portfolio_PlusDatabase::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__tz_portfolio_plus_fieldgroups');

        $db->setQuery($query);
        if ($items = $db->loadObjectList()) {
            $data   = array();
            foreach ($items as &$item)
            {
                $data[] = JHtml::_('select.option', $item->id, $item->name);
            }
            return $data;
        }
        return array();
    }
}