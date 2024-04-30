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

// no direct access
defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

jimport('joomla.application.component.controllerform');
jimport('joomla.filesystem.file');

JLoader::import('com_tz_portfolio_plus.libraries.controller.reject', JPATH_ADMINISTRATOR.'/components');

class TZ_Portfolio_PlusControllerReject extends TZ_Portfolio_PlusControllerRejectBase
{
    protected $view_list    = 'articles';
}
