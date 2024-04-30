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

use Joomla\CMS\Access\Access;

class TZ_Portfolio_PlusAccess extends Access{
    public static function getAddOnActions($addon, $group, $section = 'component')
    {
        \JLog::add(__METHOD__ . ' is deprecated. Use Access::getActionsFromFile or Access::getActionsFromData instead.', \JLog::WARNING, 'deprecated');

        $actions = self::getActionsFromFile(
            COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/'.$group.'/' . $addon . '/access.xml',
            "/access/section[@name='" . $section . "']/"
        );

        if (empty($actions))
        {
            return array();
        }
        else
        {
            return $actions;
        }
    }
}