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

use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

// Base this model on the backend version.
JLoader::register('TZ_Portfolio_PlusModelArticle', JPATH_ADMINISTRATOR
    . '/components/com_tz_portfolio_plus/models/article.php');

class TZ_Portfolio_PlusModelReject extends TZ_Portfolio_PlusModelRejectBase
{
//    public function __construct()
//    {
//        $lang   = JFactory::getLanguage();
//        $lang -> load('com_tz_portfolio_plus', JPATH_ADMINISTRATOR);
//
//        parent::__construct();
//    }

    public function getForm($data = array(), $loadData = true)
    {
        \JForm::addFormPath(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH . '/models/forms');
        \JForm::addFormPath(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH . '/model/form');

        return parent::getForm($data, $loadData);
    }



}
