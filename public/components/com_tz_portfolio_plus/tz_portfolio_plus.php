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

use Joomla\CMS\Filesystem\File;

/* Setup */
$file   = JPATH_COMPONENT_ADMINISTRATOR.'/setup/index.php';

if(File::exists($file)){
?>
    <h2><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_INSTALL_OFFLINE') ?></h2>
    <div><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_INSTALL_OFFLINE_DESC') ?></div>
<?php
    return;
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::import('com_tz_portfolio_plus.includes.framework',JPATH_ADMINISTRATOR.'/components');

JLoader::import('template', JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/libraries');
JLoader::import('controller', JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/libraries');
JLoader::import('view', JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/libraries');

tzportfolioplusimport('plugin.helper');
tzportfolioplusimport('user.user');

JLoader::import('route', COM_TZ_PORTFOLIO_PLUS_SITE_HELPERS_PATH);
JLoader::import('query', COM_TZ_PORTFOLIO_PLUS_SITE_HELPERS_PATH);

// Include helpers file
JLoader::import('categories', COM_TZ_PORTFOLIO_PLUS_SITE_HELPERS_PATH);
JLoader::import('tags', COM_TZ_PORTFOLIO_PLUS_SITE_HELPERS_PATH);

JHtml::addIncludePath(COM_TZ_PORTFOLIO_PLUS_SITE_HELPERS_PATH.'/html');
JLoader::register('ContentHelperRoute', JPATH_SITE.'/components/com_content/helpers/route.php');

$controller = JControllerLegacy::getInstance('TZ_Portfolio_Plus');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
