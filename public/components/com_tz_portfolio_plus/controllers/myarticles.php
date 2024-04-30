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

JLoader::import('com_tz_portfolio_plus.controllers.articles', JPATH_ADMINISTRATOR.'/components');

class TZ_Portfolio_PlusControllerMyarticles extends TZ_Portfolio_PlusControllerArticles
{
    public function getModel($name = 'Article', $prefix = 'TZ_Portfolio_PlusModel', $config = array('ignore_request' => true))
    {
        JLoader::register('TZ_Portfolio_PlusModelArticle', COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.'/models/article.php');
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    public function featured()
    {
        $result = parent::featured();

        $this -> setRedirect(JRoute::_(TZ_Portfolio_PlusHelperRoute::getMyArticlesRoute()));

        return $result;
    }
}
