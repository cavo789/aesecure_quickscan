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

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_tz_portfolio_plus')) {
    throw new JAccessExceptionNotallowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
}

$input			= Factory::getApplication() -> input;
$option         = $input -> getCmd('option','com_tz_portfolio_plus');
$view           = $input -> getCmd('view','dashboard');
$task           = $input -> getCmd('task',null);

/* Setup */
$file   = dirname(__FILE__).'/setup/index.php';

if(File::exists($file)){
    require_once($file);
}else {

    JLoader::import('com_tz_portfolio_plus.includes.framework', JPATH_ADMINISTRATOR . '/components');

    // Register helper class
    JLoader::register('TZ_Portfolio_PlusHelper', dirname(__FILE__) . '/helpers/tz_portfolio_plus.php');

    // Register helper class
    JLoader::register('TZ_Portfolio_PlusHelperACL', dirname(__FILE__) . '/helpers/acl.php');

    // Register helper class
    JLoader::register('TZ_Portfolio_PlusHelperAddons', COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH . '/helpers/addons.php');
    JLoader::register('TZ_Portfolio_PlusHelperTemplates', COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH . '/helpers/templates.php');

    // Includes my html object
    JHtml::addIncludePath(COM_TZ_PORTFOLIO_PLUS_ADMIN_HELPERS_PATH . '/html');

    $controller = JControllerLegacy::getInstance('TZ_Portfolio_Plus');

    $controller->execute($input->get('task'));
    $controller->redirect();
}
